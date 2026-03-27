<?php

namespace App\Jobs;

use App\Models\EscrowHold;
use App\Models\Order;
use App\Services\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoReleaseEscrowJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $orderId) {}

    public function handle(WalletService $walletService): void
    {
        $order = Order::with(['items.seller', 'user'])->find($this->orderId);

        if (!$order) return;

        // Only auto-release if still in shipped/delivered state after 3 days
        if (!in_array($order->status, ['shipped', 'delivered'])) return;

        $escrow = EscrowHold::where('order_id', $order->id)
            ->where('status', 'held')
            ->first();

        if (!$escrow) return;

        // Check it's been 7+ days since shipping
        $shippedLog = $order->statusLogs()
            ->where('to_status', 'shipped')
            ->latest()
            ->first();

        if ($shippedLog && $shippedLog->created_at->diffInDays(now()) < 3) {
            return; // not yet 3 days
        }

        try {
            $walletService->releaseEscrow($order);

            \App\Models\Notification::create([
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $order->user_id,
                'type'            => 'escrow_auto_released',
                'title'           => 'Order Completed',
                'body'            => "Order #{$order->order_number} has been automatically completed and payment released to the seller.",
                'action_url'      => route('buyer.orders.show', $order->id),
            ]);

            Log::info("Auto-released escrow for order #{$order->order_number}");

        } catch (\Exception $e) {
            Log::error("Auto-release escrow failed for order #{$order->order_number}: " . $e->getMessage());
        }
    }
}