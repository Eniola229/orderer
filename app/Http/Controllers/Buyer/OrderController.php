<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\WalletService;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected WalletService    $wallet,
        protected BrevoMailService $brevo
    ) {}

    public function index(Request $request)
    {
        $query = Order::where('user_id', auth('web')->id())->with('items');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15);

        return view('buyer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth('web')->id()) abort(403);

        $order->load(['items.seller', 'statusLogs']);

        return view('buyer.orders.show', compact('order'));
    }

    public function confirmItem(Request $request, Order $order)
    {
        $request->validate([
            'seller_id' => ['required', 'string'],
        ]);

        $buyerId  = auth('web')->id();
        $sellerId = $request->seller_id;

        // Guard — this order belongs to this buyer
        if ($order->user_id !== $buyerId) abort(403);

        // Get all shipped items for this seller in this order
        $items = \App\Models\OrderItem::where('order_id', $order->id)
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['shipped', 'delivered'])
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No shipped or delivered items found for this seller.');
        }

        foreach ($items as $item) {
            try {
                app(\App\Services\WalletService::class)->releaseEscrowForItem($item);
            } catch (\Exception $e) {
                \Log::error("confirmItem — failed for item #{$item->id}: " . $e->getMessage());
                return back()->with('error', "Could not confirm delivery for '{$item->item_name}': " . $e->getMessage());
            }
        }

        // Notify seller
        \App\Models\Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $sellerId,
            'type'            => 'order_delivered',
            'title'           => 'Delivery Confirmed',
            'body'            => "Buyer confirmed delivery for order #{$order->order_number}. Payment has been released.",
            'action_url'      => route('seller.orders.show', $order->id),
        ]);

        \App\Models\OrderStatusLog::create([
            'order_id'        => $order->id,
            'from_status'     => 'shipped',
            'to_status'       => 'delivered',
            'changed_by_type' => 'buyer',
            'changed_by_id'   => $buyerId,
            'note'            => "Buyer confirmed delivery of " . $items->count() . " item(s) from seller.",
        ]);

        return back()->with('success', 'Delivery confirmed! Payment has been released to the seller.');
    }
}
