<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Models\Seller;
use App\Models\User;
use App\Services\BrevoMailService;
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

        foreach ($recipients as $recipient) {
            try {
                // Clean the newsletter body before using it
                $cleanBody = $this->cleanNewsletterBody($newsletter->body);
                
                $htmlContent = view('emails.newsletter', [
                    'subject'       => $newsletter->subject,
                    'body'          => $cleanBody,
                    'recipientName' => $recipient->full_name ?? null,
                ])->render();

                // Also clean the final rendered HTML to be safe
                $htmlContent = $this->cleanFinalHtml($htmlContent);

                $success = $brevoMailService->send(
                    $recipient->email,
                    $recipient->full_name ?? ',i am from Orderer',  // ← fallback
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

        $newsletter->update([
            'status'  => Newsletter::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Clean the newsletter body by removing Blade/PHP syntax
     */
    private function cleanNewsletterBody(string $body): string
    {
        // Remove Blade echo tags {{ $var }}
        $body = preg_replace('/\{\{\s*.*?\s*\}\}/', '', $body);
        
        // Remove Blade raw echo tags {!! $var !!}
        $body = preg_replace('/\{\!!\s*.*?\s*!!\}/', '', $body);
        
        // Remove Blade directives @if, @foreach, @php, etc.
        $body = preg_replace('/@(?:php|endphp|if|elseif|else|endif|foreach|endforeach|for|endfor|while|endwhile|continue|break|switch|case|endswitch|csrf|method|include|each|yield|section|endsection|stop|show|append|overwrite|parent|once|push|endpush|prepend|endprepend|inject|lang|choice|can|cannot|elsecan|elsecannot|error|enderror)\b[^\n]*/', '', $body);
        
        // Remove PHP opening and closing tags
        $body = preg_replace('/<\?php[\s\S]*?\?>/', '', $body);
        $body = str_replace(['<?php', '?>', '<?=', '<?'], '', $body);
        
        // Remove any remaining curly braces that might cause issues
        $body = preg_replace('/\{\{|\}\}|\{!!|!!\}/', '', $body);
        
        // Clean up extra whitespace
        $body = preg_replace('/\s+/', ' ', $body);
        $body = trim($body);
        
        return $body;
    }

    /**
     * Clean the final rendered HTML for any remaining problematic syntax
     */
    private function cleanFinalHtml(string $html): string
    {
        // Remove any remaining Blade syntax that might have survived
        $html = preg_replace('/\{\{\s*.*?\s*\}\}/', '', $html);
        $html = preg_replace('/\{\!!\s*.*?\s*!!\}/', '', $html);
        $html = preg_replace('/@(?:php|endphp|if|elseif|else|endif|foreach|endforeach)/', '', $html);
        
        // Remove empty style tags
        $html = preg_replace('/<style\b[^>]*>\s*<\/style>/', '', $html);
        
        // Fix unclosed HTML tags (common issue)
        $html = preg_replace('/<p\s*\/?>/i', '<p>', $html);
        
        // Remove any NULL bytes or control characters
        $html = preg_replace('/[\x00-\x1F\x7F]/', '', $html);
        
        return $html;
    }

    private function buildRecipientList(string $audience): \Illuminate\Support\Collection
    {
        return match ($audience) {
            Newsletter::AUDIENCE_BUYERS  => User::where('is_active', true)
                                                ->get(['id', 'first_name', 'last_name', 'email']),

            Newsletter::AUDIENCE_SELLERS => Seller::where('is_active', true)
                                                  ->where('is_approved', true)
                                                  ->get(['id', 'first_name', 'last_name', 'email']),

            Newsletter::AUDIENCE_GUESTS => NewsletterSubscriber::all(['id', 'email'])
                ->map(fn($s) => (object)[
                    'email'     => $s->email,
                    'full_name' => ',i am from Orderer', 
                ]),
            default => User::where('is_active', true)->get(['id', 'first_name', 'last_name', 'email'])
                            ->merge(
                                Seller::where('is_active', true)->where('is_approved', true)
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