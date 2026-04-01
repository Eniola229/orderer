<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\ShipbubbleService;
use Illuminate\Console\Command;

class SyncOrderShippingStatus extends Command
{
    protected $signature   = 'orders:sync-shipping-status';
    protected $description = 'Check Shipbubble for shipping updates on active orders and update order/item statuses.';

    public function __construct(protected ShipbubbleService $shipbubble)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        // Only orders that are still in progress and have a shipbubble shipment ID
        $orders = Order::whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('shipbubble_shipment_id')
            ->with('items')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No active orders with a Shipbubble shipment ID found.');
            return self::SUCCESS;
        }

        $this->info("Checking {$orders->count()} order(s)...");

        foreach ($orders as $order) {
            try {
                $trackingData = $this->shipbubble->track($order->shipbubble_shipment_id);

                $apiStatus = $trackingData['status'] ?? null;

                if (!$apiStatus) {
                    $this->warn("  Order #{$order->order_number} — no status returned, skipping.");
                    continue;
                }

                $mappedStatus = match (strtolower($apiStatus)) {
                    'completed'              => 'completed',
                    'cancelled'              => 'cancelled',
                    'picked_up', 'in_transit'=> 'shipped',
                    'confirmed', 'processing'=> 'confirmed',
                    'delivered'              => 'delivered',
                    default                  => null,
                };

                // Nothing useful came back or status hasn't changed
                if (!$mappedStatus || $mappedStatus === $order->status) {
                    $this->line("  Order #{$order->order_number} — status unchanged ({$order->status}).");
                    continue;
                }

                $previousStatus = $order->status;

                // Update order
                $order->update(['status' => $mappedStatus]);

                // Update all order items
                $order->items()->update(['status' => $mappedStatus]);

                // Log the change
                OrderStatusLog::create([
                    'order_id'        => $order->id,
                    'from_status'     => $previousStatus,
                    'to_status'       => $mappedStatus,
                    'changed_by_type' => 'system',
                    'changed_by_id'   => null,
                    'note'            => "Auto-updated sync (Status: {$apiStatus}).",
                ]);

                $this->info("  Order #{$order->order_number} — {$previousStatus} → {$mappedStatus}");

            } catch (\Exception $e) {
                $this->error("  Order #{$order->order_number} — failed: {$e->getMessage()}");
                \Log::error("SyncOrderShippingStatus failed for order #{$order->order_number}: {$e->getMessage()}");
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}