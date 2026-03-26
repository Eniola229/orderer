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

    public function confirmDelivery(Order $order)
    {
        if ($order->user_id !== auth('web')->id()) abort(403);

        if ($order->status !== 'shipped') {
            return back()->with('error', 'This order cannot be confirmed yet.');
        }

        // Log status change
        OrderStatusLog::create([
            'order_id'        => $order->id,
            'from_status'     => 'shipped',
            'to_status'       => 'delivered',
            'changed_by_type' => 'buyer',
            'changed_by_id'   => auth('web')->id(),
            'note'            => 'Buyer confirmed delivery.',
        ]);

        $order->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);

        // Release escrow
        $this->wallet->releaseEscrow($order);

        // Notify seller
        \App\Models\Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $order->items->first()->seller_id,
            'type'            => 'delivery_confirmed',
            'title'           => 'Delivery Confirmed',
            'body'            => "Order #{$order->order_number} has been delivered. Payment released to your wallet.",
            'action_url'      => route('seller.orders.show', $order->id),
        ]);

        return back()->with('success', 'Delivery confirmed! Payment released to the seller.');
    }
}
