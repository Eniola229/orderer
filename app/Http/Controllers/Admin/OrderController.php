<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\WalletService;
use App\Models\EscrowHold;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected WalletService $wallet) {}

    public function index(Request $request)
    {
        if (!auth('admin')->user()->canView()) abort(403);

        $query = Order::with(['user', 'items']);

        // Filter by order status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or user email
        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($r) => $r->where('email', 'like', "%{$s}%"));
            });
        }
     
        $orders = $query->latest()->paginate(20)->withQueryString();

        // Calculate stats based on the same filters (excluding pagination)
        $statsQuery = clone $query;
        $stats = [
            'total'     => $statsQuery->count(),
            'pending'   => (clone $statsQuery)->where('status', 'pending')->count(),
            'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
            'revenue'   => (clone $statsQuery)->where('payment_status', 'paid')->sum('total'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        if (!auth('admin')->user()->canView()) abort(403);

        $order->load([
            'user',
            'items.seller',
            'statusLogs',
            'escrowHolds.orderItem', 
            'escrowHolds.seller',
        ]);

        return view('admin.orders.show', compact('order'));
    }
    public function disputes()
    {
        if (!auth('admin')->user()->canView()) abort(403);

        $orders = Order::whereIn('status', ['dispute', 'refund_requested'])
            ->with(['user', 'items'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.disputes', compact('orders'));
    }
    
    public function forceComplete(Order $order)
    {
        if (!auth('admin')->user()->canEditOrders()) abort(403);

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Order already finalised.');
        }

        $heldEscrows = EscrowHold::where('order_id', $order->id)
            ->where('status', 'held')
            ->count();

        if ($heldEscrows === 0) {
            return back()->with('error', 'No pending escrow funds to release.');
        }

        $this->wallet->releaseEscrow($order);

        // Only mark non-cancelled items as completed
        $order->items()->whereNotIn('status', ['cancelled'])->update(['status' => 'completed']);

        OrderStatusLog::create([
            'order_id'        => $order->id,
            'from_status'     => $order->status,
            'to_status'       => 'completed',
            'changed_by_type' => 'admin',
            'changed_by_id'   => auth('admin')->id(),
            'note'            => 'Manually completed by admin. Escrow released for ' . $heldEscrows . ' item(s).',
        ]);

        return back()->with('success', "Order completed and escrow released for {$heldEscrows} item(s).");
    }

    public function forceRefund(Order $order, \App\Services\ShipbubbleService $shipbubble)
    {
        if (!auth('admin')->user()->canEditOrders()) abort(403);

        if (in_array($order->status, ['completed', 'cancelled', 'refunded'])) {
            return back()->with('error', 'Order is already finalised and cannot be refunded.');
        }

         // Load items WITH their order relationship
        $order->load(['items.order']);


        // Only items that are still refundable
        $refundableItems = $order->items->whereIn('status', ['pending', 'confirmed', 'processing']);

        if ($refundableItems->isEmpty()) {
            return back()->with('error', 'No refundable items found. All items are either already cancelled, shipped, or delivered.');
        }

        // Check escrow exists per refundable item — not just per order
        $heldEscrowCount = EscrowHold::where('order_id', $order->id)
            ->whereIn('order_item_id', $refundableItems->pluck('id'))
            ->where('status', 'held')
            ->count();

        if ($heldEscrowCount === 0) {
            return back()->with('error', 'No pending escrow funds found for refundable items.');
        }

        // ── STEP 1: Shipbubble pre-flight — abort if any shipment already processed ──
        foreach ($refundableItems as $item) {
            if (!$item->shipbubble_shipment_id) continue;

            $otherActiveItemsFromSameSeller = \App\Models\OrderItem::where('order_id', $order->id)
                ->where('seller_id', $item->seller_id)
                ->where('id', '!=', $item->id)
                ->whereNotIn('status', ['cancelled', 'delivered', 'completed'])
                ->count();

            if ($otherActiveItemsFromSameSeller > 0) continue;

            try {
                $result = $shipbubble->cancelShipment($item->shipbubble_shipment_id);

                if (($result['status'] ?? '') === 'failed') {
                    return back()->with('error',
                        "Cannot cancel order — item '{$item->item_name}' shipment ({$item->shipbubble_shipment_id}) " .
                        "has already been processed by the courier: \"{$result['message']}\". " .
                        "Please handle this shipment manually before cancelling."
                    );
                }

            } catch (\Exception $e) {
                $errorBody = json_decode(
                    preg_replace('/^Shipbubble cancel failed: /', '', $e->getMessage()),
                    true
                );

                if (($errorBody['message'] ?? '') === 'Shipment label already processed') {
                    return back()->with('error',
                        "Cannot cancel order — item '{$item->item_name}' shipment ({$item->shipbubble_shipment_id}) " .
                        "is already being processed by the courier. " .
                        "Please handle this shipment manually before cancelling."
                    );
                }

                return back()->with('error',
                    "Cannot cancel order — Shipbubble returned an unexpected error for item '{$item->item_name}': " .
                    $e->getMessage()
                );
            }
        }

        // ── STEP 2: Refund each item via its own escrow row ──────────────────
        $previousStatus  = $order->status;
        $refundedAmount  = 0;
        $shipbubbleNotes = [];

        foreach ($refundableItems as $item) {
            if ($item->shipbubble_shipment_id) {
                $otherActive = \App\Models\OrderItem::where('order_id', $order->id)
                    ->where('seller_id', $item->seller_id)
                    ->where('id', '!=', $item->id)
                    ->whereNotIn('status', ['cancelled', 'delivered', 'completed'])
                    ->count();

                if ($otherActive === 0) {
                    $shipbubbleNotes[] = "Shipment {$item->shipbubble_shipment_id} cancelled.";
                }
            }

            try {
                $this->wallet->cancelItemEscrow($item);
                $refundedAmount += (float) $item->total_price;
            } catch (\Exception $e) {
                \Log::error("forceRefund — failed to refund item #{$item->id} '{$item->item_name}': " . $e->getMessage());
                // Optionally continue or return with error
                return back()->with('error', "Failed to refund item '{$item->item_name}': " . $e->getMessage());
            }
        }

        // ── STEP 3: Refund shipping fee on top ───────────────────────────────
        $shippingFee = (float) $order->shipping_fee;
        $totalRefund = $refundedAmount + $shippingFee;

        if ($shippingFee > 0) {
            try {
                $this->wallet->credit(
                    $order->user,
                    $shippingFee,
                    'escrow_refund',
                    "Shipping fee refund for cancelled order #{$order->order_number}",
                    'order',
                    $order->id
                );
            } catch (\Exception $e) {
                \Log::error("forceRefund — failed to refund shipping fee for order #{$order->order_number}: " . $e->getMessage());
            }
        }

        $order->update(['status' => 'cancelled']);

        $note  = "Order cancelled by admin. ";
        $note .= "₦" . number_format($refundedAmount, 2) . " refunded for " . $refundableItems->count() . " item(s). ";
        $note .= "₦" . number_format($shippingFee, 2) . " shipping fee refunded. ";
        $note .= "Total: ₦" . number_format($totalRefund, 2) . ". ";
        if (!empty($shipbubbleNotes)) {
            $note .= implode(' ', $shipbubbleNotes);
        }

        OrderStatusLog::create([
            'order_id'        => $order->id,
            'from_status'     => $previousStatus,
            'to_status'       => 'cancelled',
            'changed_by_type' => 'admin',
            'changed_by_id'   => auth('admin')->id(),
            'note'            => $note,
        ]);

        return back()->with('success',
            "Order cancelled. ₦" . number_format($totalRefund, 2) . " refunded to buyer's wallet " .
            "(" . $refundableItems->count() . " item(s) + ₦" . number_format($shippingFee, 2) . " shipping)."
            );
        }

    /**
     * Cancel a single order item, refund the buyer, and cancel the Shipbubble shipment
     * if this was the only item in that shipment.
     */
    public function cancelOrderItem(Order $order, \App\Models\OrderItem $item, \App\Services\ShipbubbleService $shipbubble)
    {
        if (!auth('admin')->user()->canEditOrders()) abort(403);

        // Guard — wrong order
        if ($item->order_id !== $order->id) {
            return back()->with('error', 'Item does not belong to this order.');
        }

        // Guard — already finalised
        if (in_array($item->status, ['cancelled', 'delivered', 'completed'])) {
            return back()->with('error', "Item is already {$item->status} and cannot be cancelled.");
        }

        // Guard — cannot cancel once shipped or beyond
        if (in_array($item->status, ['shipped', 'in_transit', 'picked_up'])) {
            return back()->with('error', "Item has already been shipped and cannot be cancelled.");
        }

        // Guard — escrow must exist
        $escrow = \App\Models\EscrowHold::where('order_id', $order->id)
            ->where('seller_id', $item->seller_id)
            ->where('status', 'held')
            ->first();

        if (!$escrow) {
            return back()->with('error', 'No held escrow found for this item. Cannot refund.');
        }

        $shipbubbleCancelled = false;
        $shipbubbleError     = null;

        // ── Shipbubble cancel logic ──────────────────────────────────────────
        // Only cancel the shipment if this seller has NO OTHER active items in this order.
        // If the seller has multiple items and only one is being cancelled, the shipment
        // should continue for the remaining items.
        // ── Shipbubble cancel logic ──────────────────────────────────────────
        if ($item->shipbubble_shipment_id) {
            $otherActiveItemsFromSameSeller = \App\Models\OrderItem::where('order_id', $order->id)
                ->where('seller_id', $item->seller_id)
                ->where('id', '!=', $item->id)
                ->whereNotIn('status', ['cancelled', 'delivered', 'completed'])
                ->count();

            if ($otherActiveItemsFromSameSeller === 0) {
                try {
                    $result = $shipbubble->cancelShipment($item->shipbubble_shipment_id);

                    // Shipbubble explicitly told us the shipment is already processed
                    // Block the cancellation — item is already on its way
                    if (($result['status'] ?? '') === 'failed') {
                        return back()->with('error', 
                            "Cannot cancel this item — the shipment has already been processed by the courier. " .
                            "Shipbubble says: {$result['message']}"
                        );
                    }

                    $shipbubbleCancelled = true;

                } catch (\Exception $e) {
                    // Parse the JSON error body from Shipbubble
                    $errorBody = json_decode(
                        preg_replace('/^Shipbubble cancel failed: /', '', $e->getMessage()), 
                        true
                    );

                    if (($errorBody['message'] ?? '') === 'Shipment label already processed') {
                        return back()->with('error', 
                            "Cannot cancel — this shipment is already being processed by the courier and is on its way."
                        );
                    }

                    // Some other unexpected error — log it but don't block
                    $shipbubbleError = $e->getMessage();
                    \Log::warning("Unexpected Shipbubble cancel error for shipment {$item->shipbubble_shipment_id}: {$shipbubbleError}");
                }
            }
        }
        // ── Refund item to buyer and update escrow ───────────────────────────
        try {
            $this->wallet->cancelItemEscrow($item);
        } catch (\Exception $e) {
            \Log::error("cancelOrderItem — wallet refund failed for item #{$item->id}: " . $e->getMessage());
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }

        // ── Log ──────────────────────────────────────────────────────────────
        $note = "Item '{$item->item_name}' cancelled by admin. ₦" . number_format($item->total_price, 2) . " refunded to buyer.";
        if ($shipbubbleCancelled) {
            $note .= " Shipment {$item->shipbubble_shipment_id} cancelled (no other items from this seller).";
        } elseif ($shipbubbleError) {
            $note .= " Shipment cancel failed: {$shipbubbleError} — may need manual handling.";
        } else {
            $note .= " Shipment kept active (seller has other items in this order).";
        }

        OrderStatusLog::create([
            'order_id'        => $order->id,
            'from_status'     => $order->status,
            'to_status'       => $order->fresh()->status, // reflects any auto-update from cancelItemEscrow
            'changed_by_type' => 'admin',
            'changed_by_id'   => auth('admin')->id(),
            'note'            => $note,
        ]);

        $message = "Item '{$item->item_name}' cancelled. ₦" . number_format($item->total_price, 2) . " refunded to buyer's wallet.";
        if ($shipbubbleError) {
            $message .= " Warning: Shipbubble shipment could not be cancelled automatically.";
        }

        return back()->with('success', $message);
    }
}  