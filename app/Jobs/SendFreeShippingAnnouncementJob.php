<?php

namespace App\Jobs;

use App\Models\FreeShippingRule;
use App\Models\User;
use App\Models\Seller;
use App\Services\BrevoMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFreeShippingAnnouncementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly string $ruleId
    ) {}

    public function handle(BrevoMailService $brevo): void
    {
        $rule = FreeShippingRule::with('buyers', 'products', 'sellers')
            ->find($this->ruleId);

        if (!$rule || !$rule->isCurrentlyActive()) {
            return;
        }

        $recipients = $this->buildRecipientList($rule);

        // Check if method exists on the injected service
        if (!method_exists($brevo, 'sendFreeShippingAnnouncement')) {
            // Log the actual class being used
            Log::error("Method not found. Class being used: " . get_class($brevo));
            
            // Get reflection info
            $reflection = new \ReflectionClass($brevo);
            $methods = array_map(function($method) {
                return $method->getName();
            }, $reflection->getMethods(\ReflectionMethod::IS_PUBLIC));
            
            Log::error("Available methods: " . implode(', ', array_slice($methods, 0, 20)));
            return;
        }

        foreach ($recipients as $recipient) {
            try {
                $brevo->sendFreeShippingAnnouncement($recipient, $rule);
            } catch (\Throwable $e) {
                Log::error("FreeShipping announcement failed for {$recipient->email}", [
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    private function buildRecipientList(FreeShippingRule $rule): \Illuminate\Support\Collection
    {
        return match ($rule->applies_to) {

            'all_buyers' => User::where('is_active', true)
                ->get(['id', 'first_name', 'last_name', 'email']),

            'new_buyers' => User::where('is_active', true)
                ->where('created_at', '>=', now()->subDays($rule->new_buyer_days ?? 30))
                ->get(['id', 'first_name', 'last_name', 'email']),

            'buyers_no_orders' => User::where('is_active', true)
                ->whereDoesntHave('orders')
                ->get(['id', 'first_name', 'last_name', 'email']),

            'specific_buyers' => $rule->buyers()
                ->where('is_active', true)
                ->get(['users.id', 'first_name', 'last_name', 'email']),

            default => collect(),
        };
    }

    public function failed(\Throwable $e): void
    {
        Log::error("SendFreeShippingAnnouncementJob permanently failed for rule [{$this->ruleId}]", [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}