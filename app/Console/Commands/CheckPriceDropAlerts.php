<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\FlashSale;
use App\Models\PriceDropAlert;
use App\Models\Notification;
use App\Services\BrevoMailService;
use Illuminate\Console\Command;

class CheckPriceDropAlerts extends Command
{
    protected $signature   = 'alerts:price-drops';
    protected $description = 'Notify users when product prices drop or flash sales start for products in their cart';

    public function handle(BrevoMailService $brevo): int
    {
        $this->info('Checking price drop alerts and cart flash sales...');

        $priceDropNotified = $this->checkPriceDropAlerts($brevo);
        $cartNotified      = $this->checkCartPriceChanges($brevo);

        $this->table(
            ['Type', 'Notified'],
            [
                ['Price Drop Alerts', $priceDropNotified],
                ['Cart Price Changes / Flash Sales', $cartNotified],
            ]
        );

        return self::SUCCESS;
    }

    // ── 1. Price drop alerts (user set a target price) ────────────────────
    protected function checkPriceDropAlerts(BrevoMailService $brevo): int
    {
        $alerts   = PriceDropAlert::where('notified', false)
            ->with(['user', 'product'])
            ->get();

        $notified = 0;

        foreach ($alerts as $alert) {
            $product      = $alert->product;
            if (!$product || !$alert->user) continue;

            $currentPrice = $this->getCurrentPrice($product);

            if ($alert->target_price && $currentPrice <= $alert->target_price) {
                try {
                    $brevo->sendPriceDropAlert($alert->user, $product, $currentPrice, $alert->target_price);
                } catch (\Exception $e) {
                    \Log::error("Price drop email failed for user #{$alert->user_id}: " . $e->getMessage());
                }

                Notification::create([
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id'   => $alert->user_id,
                    'type'            => 'price_drop',
                    'title'           => 'Price Drop Alert!',
                    'body'            => "\"{$product->name}\" has dropped to ₦" . number_format($currentPrice, 2) . " — your target price!",
                    'action_url'      => route('product.show', $product->slug),
                ]);

                $alert->update(['notified' => true]);
                $notified++;
            }
        }

        return $notified;
    }

    // ── 2. Cart price changes & flash sales ───────────────────────────────
    protected function checkCartPriceChanges(BrevoMailService $brevo): int
    {
        // Only logged-in user carts
        $carts = Cart::whereNotNull('user_id')
            ->with(['user', 'items.product.images'])
            ->get();

        $notified = 0;

        foreach ($carts as $cart) {
            if (!$cart->user) continue;

            $changedItems = [];

            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                if (!$product || $product->status !== 'approved') continue;

                $newPrice  = $this->getCurrentPrice($product);
                $cartPrice = (float) $cartItem->price;

                if ($newPrice < $cartPrice) {
                    // Update the cart item price to reflect the drop
                    $cartItem->update(['price' => $newPrice]);

                    $primaryImg = $product->images->where('is_primary', true)->first()
                                  ?? $product->images->first();

                    $changedItems[] = [
                        'name'      => $product->name,
                        'old_price' => $cartPrice,
                        'new_price' => $newPrice,
                        'saving'    => $cartPrice - $newPrice,
                        'image'     => $primaryImg?->image_url ?? null,
                        'url'       => route('product.show', $product->slug),
                        'is_flash'  => $this->isFlashSaleActive($product),
                    ];
                }
            }

            if (!empty($changedItems)) {
                try {
                    $brevo->sendCartPriceDropAlert($cart->user, $changedItems);
                } catch (\Exception $e) {
                    \Log::error("Cart price drop email failed for user #{$cart->user_id}: " . $e->getMessage());
                }

                Notification::create([
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id'   => $cart->user_id,
                    'type'            => 'cart_price_drop',
                    'title'           => '🛒 Price drop on items in your cart!',
                    'body'            => count($changedItems) . ' item(s) in your cart just got cheaper. Check it out!',
                    'action_url'      => route('cart.index'),
                ]);

                $notified++;
            }
        }

        return $notified;
    }

    // ── Helpers ───────────────────────────────────────────────────────────
    protected function getCurrentPrice($product): float
    {
        // Check active flash sale first
        $flashSale = FlashSale::where('product_id', $product->id)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->where(function ($q) {
                $q->whereNull('quantity_limit')
                  ->orWhereColumn('quantity_sold', '<', 'quantity_limit');
            })
            ->first();

        if ($flashSale) {
            return (float) $flashSale->sale_price;
        }

        return (float) ($product->sale_price ?? $product->price);
    }

    protected function isFlashSaleActive($product): bool
    {
        return FlashSale::where('product_id', $product->id)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->exists();
    }
}