<?php
// app/Console/Commands/AutoReleaseEscrow.php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\EscrowHold;
use App\Services\WalletService;
use Illuminate\Console\Command;

class AutoReleaseEscrow extends Command
{
    protected $signature   = 'escrow:auto-release';
    protected $description = 'Auto-release escrow for orders shipped more than 3 days ago with no delivery confirmation';

    public function handle(WalletService $walletService): void
    {
        $this->info('Checking for escrow to auto-release...');

        // Find orders that have been shipped for 3+ days without buyer confirmation
        $eligibleOrders = Order::whereIn('status', ['shipped', 'delivered'])
            ->whereHas('escrow', fn($q) => $q->where('status', 'held'))
            ->whereHas('statusLogs', function ($q) {
                $q->where('to_status', 'shipped')
                  ->where('created_at', '<=', now()->subDays(3));
            })
            ->with(['items.seller', 'user', 'escrow'])
            ->get();

        $this->info("Found {$eligibleOrders->count()} order(s) eligible for auto-release.");

        foreach ($eligibleOrders as $order) {
            try {
                $walletService->releaseEscrow($order);

                \App\Models\Notification::create([
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id'   => $order->user_id,
                    'type'            => 'escrow_auto_released',
                    'title'           => 'Order Auto-Completed',
                    'body'            => "Order #{$order->order_number} has been auto-completed after 3 days. Payment released to seller.",
                    'action_url'      => route('buyer.orders.show', $order->id),
                ]);

                $this->info("✓ Released escrow for order #{$order->order_number}");

            } catch (\Exception $e) {
                $this->error("✗ Failed for order #{$order->order_number}: " . $e->getMessage());
            }
        }

        $this->info('Done.');
    }
}