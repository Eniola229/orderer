<?php
namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\WalletService;
use Illuminate\Console\Command;

class AutoReleaseEscrow extends Command
{
    protected $signature   = 'escrow:auto-release';
    protected $description = 'Safety net: force-release escrow for items stuck in shipped status 
                          for 5+ days where Shipbubble failed to confirm delivery.';

    public function handle(WalletService $walletService): void
    {
        $this->info('Checking for escrow to auto-release...');

        // Target ITEMS (not orders) that are stuck in shipped
        // for 3+ days — meaning Shipbubble never returned a delivered status
        $stuckItems = OrderItem::whereIn('status', ['shipped'])
            ->whereHas('escrowHold', fn($q) => $q->where('status', 'held'))
            ->whereHas('order.statusLogs', function ($q) {
                $q->where('to_status', 'shipped')
                  ->where('created_at', '<=', now()->subDays(5));
            })
            ->with(['order.user', 'seller', 'escrowHold'])
            ->get();

        $this->info("Found {$stuckItems->count()} stuck item(s) eligible for auto-release.");

        if ($stuckItems->isEmpty()) {
            $this->info('Nothing to release.');
            return;
        }

        // Group by order to avoid duplicate notifications
        $grouped = $stuckItems->groupBy('order_id');

        foreach ($grouped as $orderId => $items) {
            $order = $items->first()->order;

            foreach ($items as $item) {
                try {
                    // Reuse the same method SyncShippingStatus uses — no double logic
                    $walletService->releaseEscrowForItem($item);
                    $this->info("  ✓ Released escrow for item '{$item->item_name}' (Order #{$order->order_number})");

                } catch (\Exception $e) {
                    $this->error("  ✗ Failed for item '{$item->item_name}' (Order #{$order->order_number}): {$e->getMessage()}");
                    \Log::error("AutoReleaseEscrow failed for item {$item->id}: {$e->getMessage()}");
                }
            }

            // Notify buyer once per order (not per item)
            try {
                \App\Models\Notification::create([
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id'   => $order->user_id,
                    'type'            => 'escrow_auto_released',
                    'title'           => 'Order Auto-Completed',
                    'body'            => "Order #{$order->order_number} has been auto-completed after 5 days. Payment released to seller(s).",
                    'action_url'      => route('buyer.orders.show', $order->id),
                ]);
            } catch (\Exception $e) {
                \Log::warning("AutoReleaseEscrow notification failed for order #{$order->order_number}: {$e->getMessage()}");
            }
        }

        $this->info('Done.');
    }
}