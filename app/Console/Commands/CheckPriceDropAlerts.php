<?php
namespace App\Console\Commands;

use App\Models\PriceDropAlert;
use App\Models\Notification;
use App\Services\BrevoMailService;
use Illuminate\Console\Command;

class CheckPriceDropAlerts extends Command
{
    protected $signature   = 'alerts:price-drops';
    protected $description = 'Notify users when product prices have dropped';

    public function handle(BrevoMailService $brevo): void
    {
        $this->info('Checking price drop alerts...');

        $alerts = PriceDropAlert::where('notified', false)
            ->with(['user', 'product'])
            ->get();

        $notified = 0;

        foreach ($alerts as $alert) {
            $product      = $alert->product;
            $currentPrice = $product->sale_price ?? $product->price;

            $shouldNotify = false;

            if ($alert->target_price) {
                // Notify when price hits or drops below target
                $shouldNotify = $currentPrice <= $alert->target_price;
            }

            if ($shouldNotify && $alert->user) {
                Notification::create([
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id'   => $alert->user_id,
                    'type'            => 'price_drop',
                    'title'           => 'Price Drop Alert!',
                    'body'            => "\"{$product->name}\" is now \${$currentPrice} — down to your target price!",
                    'action_url'      => route('product.show', $product->slug),
                ]);

                $alert->update(['notified' => true]);
                $notified++;
            }
        }

        $this->info("Notified {$notified} user(s).");
    }
}