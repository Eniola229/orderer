<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Models\Seller;
use App\Models\User;
use App\Services\BrevoMailService;
use App\Services\TermiiService; 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\NewsletterSubscriber;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(private readonly string $newsletterId) {}

    public function handle(BrevoMailService $brevoMailService): void
    {
        /** @var Newsletter $newsletter */
        $newsletter = Newsletter::findOrFail($this->newsletterId);

        if ($newsletter->status !== Newsletter::STATUS_QUEUED) {
            return;
        }

        $newsletter->update(['status' => Newsletter::STATUS_SENDING]);

        $recipients = $this->buildRecipientList($newsletter->audience);

        $newsletter->update(['total_recipients' => $recipients->count()]);

        // ── Email sending loop ────────────────────────────────────────────────
        foreach ($recipients as $recipient) {
            try {
                $cleanBody = $this->cleanNewsletterBody($newsletter->body);

                $htmlContent = view('emails.newsletter', [
                    'subject'       => $newsletter->subject,
                    'body'          => $cleanBody,
                    'recipientName' => $recipient->full_name ?? null,
                ])->render();

                $htmlContent = $this->cleanFinalHtml($htmlContent);

                $success = $brevoMailService->send(
                    $recipient->email,
                    $recipient->full_name ?? ',i am from Orderer',
                    $newsletter->subject,
                    $htmlContent
                );

                if ($success) {
                    $newsletter->increment('sent_count');
                } else {
                    throw new \Exception('Brevo API returned non-success response');
                }
            } catch (\Throwable $e) {
                $newsletter->increment('failed_count');
                Log::error("Newsletter [{$newsletter->id}] failed for {$recipient->email}: {$e->getMessage()}");
            }
        }

        // ── SMS via Termii (fires after all emails, independently) ────────────
        if ($newsletter->send_sms && $newsletter->sms_message && $newsletter->sms_audience) {
            $this->sendSms($newsletter);
        }

        $newsletter->update([
            'status'  => Newsletter::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    // ── SMS dispatcher ────────────────────────────────────────────────────────
    private function sendSms(Newsletter $newsletter): void
    {
        $phones = collect();

        if (in_array($newsletter->sms_audience, ['users', 'both'])) {
            User::whereNotNull('phone')
                ->where('phone', '!=', '')
                ->where('is_active', true)
                ->pluck('phone')
                ->each(fn($p) => $phones->push($p));
        }

        if (in_array($newsletter->sms_audience, ['sellers', 'both'])) {
            Seller::whereNotNull('phone')
                ->where('phone', '!=', '')
                ->where('is_active', true)
                ->where('is_approved', true)
                ->pluck('phone')
                ->each(fn($p) => $phones->push($p));
        }

        // Merge manually added extra numbers
        if (! empty($newsletter->sms_extra_numbers)) {
            foreach ($newsletter->sms_extra_numbers as $num) {
                $num = trim((string) $num);
                if ($num !== '') {
                    $phones->push($num);
                }
            }
        }

        $allNumbers = $phones->unique()->values()->toArray();

        if (empty($allNumbers)) {
            Log::info("Newsletter [{$newsletter->id}] SMS skipped — no phone numbers found.");
            return;
        }

        try {
            $termii = app(TermiiService::class);
            $result = $termii->sendBulk($allNumbers, $newsletter->sms_message);

            // Log::info("Newsletter [{$newsletter->id}] SMS result", [
            //     'audience'      => $newsletter->sms_audience,
            //     'total_numbers' => count($allNumbers),
            //     'sent'          => $result['sent'],
            //     'failed'        => $result['failed'],
            //     'errors'        => $result['errors'],
            // ]);
        } catch (\Throwable $e) {
            // SMS failure must NOT fail the whole job — emails already sent
            Log::error("Newsletter [{$newsletter->id}] SMS exception: {$e->getMessage()}");
        }
    }

    // ── Body cleaners ─────────────────────────────────────────────────────────
    private function cleanNewsletterBody(string $body): string
    {
        $body = preg_replace('/\{\{\s*.*?\s*\}\}/', '', $body);
        $body = preg_replace('/\{\!!\s*.*?\s*!!\}/', '', $body);
        $body = preg_replace('/@(?:php|endphp|if|elseif|else|endif|foreach|endforeach|for|endfor|while|endwhile|continue|break|switch|case|endswitch|csrf|method|include|each|yield|section|endsection|stop|show|append|overwrite|parent|once|push|endpush|prepend|endprepend|inject|lang|choice|can|cannot|elsecan|elsecannot|error|enderror)\b[^\n]*/', '', $body);
        $body = preg_replace('/<\?php[\s\S]*?\?>/', '', $body);
        $body = str_replace(['<?php', '?>', '<?=', '<?'], '', $body);
        $body = preg_replace('/\{\{|\}\}|\{!!|!!\}/', '', $body);
        $body = preg_replace('/\s+/', ' ', $body);

        return trim($body);
    }

    private function cleanFinalHtml(string $html): string
    {
        $html = preg_replace('/\{\{\s*.*?\s*\}\}/', '', $html);
        $html = preg_replace('/\{\!!\s*.*?\s*!!\}/', '', $html);
        $html = preg_replace('/@(?:php|endphp|if|elseif|else|endif|foreach|endforeach)/', '', $html);
        $html = preg_replace('/<style\b[^>]*>\s*<\/style>/', '', $html);
        $html = preg_replace('/<p\s*\/?>/i', '<p>', $html);
        $html = preg_replace('/[\x00-\x1F\x7F]/', '', $html);

        return $html;
    }

    // ── Recipient builder ─────────────────────────────────────────────────────
    private function buildRecipientList(string $audience): \Illuminate\Support\Collection
    {
        return match ($audience) {
            Newsletter::AUDIENCE_BUYERS => User::where('is_active', true)
                ->get(['id', 'first_name', 'last_name', 'email']),

            Newsletter::AUDIENCE_SELLERS => Seller::where('is_active', true)
                ->where('is_approved', true)
                ->get(['id', 'first_name', 'last_name', 'email']),

            Newsletter::AUDIENCE_GUESTS => NewsletterSubscriber::all(['id', 'email'])
                ->map(fn($s) => (object)[
                    'email'     => $s->email,
                    'full_name' => '.',
                ]),

            'new_buyers' => User::where('is_active', true)
                ->where('created_at', '>=', now()->subDays(30))
                ->get(['id', 'first_name', 'last_name', 'email']),

            'buyers_no_orders' => User::where('is_active', true)
                ->whereDoesntHave('orders')
                ->get(['id', 'first_name', 'last_name', 'email']),

            'buyers_with_orders' => User::where('is_active', true)
                ->whereHas('orders')
                ->get(['id', 'first_name', 'last_name', 'email']),

            'sellers_no_listings' => Seller::where('is_active', true)
                ->where('is_approved', true)
                ->whereDoesntHave('products')
                ->whereDoesntHave('services')
                ->whereDoesntHave('properties')
                ->get(['id', 'first_name', 'last_name', 'email']),

            default => User::where('is_active', true)
                ->get(['id', 'first_name', 'last_name', 'email'])
                ->merge(
                    Seller::where('is_active', true)
                        ->where('is_approved', true)
                        ->get(['id', 'first_name', 'last_name', 'email'])
                ),
        };
    }

    public function failed(\Throwable $e): void
    {
        Newsletter::where('id', $this->newsletterId)
                  ->update(['status' => Newsletter::STATUS_FAILED]);

        Log::error("SendNewsletterJob permanently failed for newsletter [{$this->newsletterId}]: {$e->getMessage()}");
    }
}