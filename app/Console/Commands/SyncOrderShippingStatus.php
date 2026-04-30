<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Services\ShipbubbleService;
use App\Services\WalletService;
use Illuminate\Console\Command;
use App\Services\BrevoMailService;

class SyncOrderShippingStatus extends Command
{
    protected $signature   = 'orders:sync-shipping-status';
    protected $description = 'Check Shipbubble for shipping updates on active order items and update statuses + release escrow on delivery.';

    public function __construct(
        protected ShipbubbleService $shipbubble,
        protected WalletService     $walletService,
        protected BrevoMailService  $brevo,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        // Fetch all active items that have a shipbubble_shipment_id
        $activeItems = OrderItem::whereNotIn('status', ['delivered', 'completed', 'cancelled'])
            ->whereNotNull('shipbubble_shipment_id')
            ->with(['order', 'seller'])
            ->get();

        if ($activeItems->isEmpty()) {
            $this->info('No active order items with a Shipbubble shipment ID found.');
            return self::SUCCESS;
        }

        $this->info("Checking {$activeItems->count()} item(s) across orders...");

        // Group by shipbubble_shipment_id — items in the same seller shipment share one ID
        $grouped = $activeItems->groupBy('shipbubble_shipment_id');

        foreach ($grouped as $shipmentId => $itemsInShipment) {
            $order = $itemsInShipment->first()->order;

            try {
                $trackingData = $this->shipbubble->track($shipmentId);
                $apiStatus    = $trackingData['status'] ?? null;

                if (!$apiStatus) {
                    $this->warn("  Shipment {$shipmentId} (Order #{$order->order_number}) — no status returned, skipping.");
                    continue;
                }

                $mappedStatus = match (strtolower($apiStatus)) {
                    'delivered'                        => 'delivered',
                    'completed'                        => 'delivered',
                    'picked_up', 'in_transit', 'transit'=> 'shipped',
                    'confirmed', 'processing'          => 'confirmed',
                    'cancelled'                        => 'cancelled',
                    default                            => null,
                };

                if (!$mappedStatus) {
                    $this->line("  Shipment {$shipmentId} — unmapped status '{$apiStatus}', skipping.");
                    continue;
                }

                foreach ($itemsInShipment as $item) {
                    if ($item->status === $mappedStatus) {
                        continue; // Nothing changed for this item
                    }

                    $previousItemStatus = $item->status;

                    if ($mappedStatus === 'delivered') {
                        // Release escrow for this item and potentially complete the order
                        $this->walletService->releaseEscrowForItem($item);
                        $this->info("  ✓ Item '{$item->item_name}' (Order #{$order->order_number}) — delivered, escrow released.");

                    } elseif ($mappedStatus === 'cancelled') {
                        $item->update(['status' => 'cancelled']);
                        $this->warn("  Item '{$item->item_name}' (Order #{$order->order_number}) — cancelled.");

                    } else {
                        // shipped / confirmed
                        $item->update(['status' => $mappedStatus]);
                        $this->info("  Item '{$item->item_name}' (Order #{$order->order_number}) — {$previousItemStatus} → {$mappedStatus}");
                    }

                        // Send email to buyer
                        try {
                            $sellerItems = $itemsInShipment; // all items in this shipment belong to same seller
                            $this->brevo->sendOrderStatusUpdate(
                                $order->user,
                                $order,
                                $sellerItems,
                                $mappedStatus,
                                $trackingData['tracking_number'] ?? null
                            );
                        } catch (\Exception $e) {
                            \Log::error("SyncOrderShippingStatus — email failed for order #{$order->order_number}: " . $e->getMessage());
                        }

                    // Log each status change on the order timeline
                    OrderStatusLog::create([
                        'order_id'        => $order->id,
                        'from_status'     => $previousItemStatus,
                        'to_status'       => $mappedStatus,
                        'changed_by_type' => 'system',
                        'changed_by_id'   => null,
                        'note'            => "Item '{$item->item_name}' (Shipment: {$shipmentId}) auto-updated to {$mappedStatus}.",
                    ]);
                }

                // Update the order's top-level status to reflect the "worst" item status
                // (so if even one item is just 'shipped', order shows 'shipped')
                $this->syncOrderStatus($order);

            } catch (\Exception $e) {
                $this->error("  Shipment {$shipmentId} (Order #{$order->order_number}) — failed: {$e->getMessage()}");
                \Log::error("SyncOrderShippingStatus failed for shipment {$shipmentId}: {$e->getMessage()}");
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }

    /**
     * Set the order's status to reflect the current state of its items.
     * Priority: cancelled < pending < confirmed < shipped < delivered < completed
     */
    private function syncOrderStatus(Order $order): void
    {
        $order->load('items');

        if ($order->allItemsDelivered()) {
            return; // already handled inside releaseEscrowForItem
        }

        // Ignore cancelled items when determining order status
        // (a mix of cancelled + shipped should show 'shipped', not 'cancelled')
        $activeItems = $order->items->whereNotIn('status', ['cancelled']);

        if ($activeItems->isEmpty()) {
            // All items are cancelled — mark order cancelled
            if ($order->status !== 'cancelled') {
                $order->update(['status' => 'cancelled']);
            }
            return;
        }

        $statusPriority = [
            'pending'    => 1,
            'confirmed'  => 2,
            'shipped'    => 3,
            'delivered'  => 4,
            'completed'  => 5,
        ];

        // Order status = lowest priority among ACTIVE (non-cancelled) items only
        $lowestStatus = $activeItems
            ->sortBy(fn($i) => $statusPriority[$i->status] ?? 0)
            ->first()
            ?->status ?? 'pending';

        if ($lowestStatus !== $order->status && $lowestStatus !== 'completed') {
            $order->update(['status' => $lowestStatus]);
        }
    }