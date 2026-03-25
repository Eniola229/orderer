<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Services\WalletService;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected WalletService  $wallet,
        protected BrevoMailService $brevo
    ) {}

    public function index(Request $request)
    {
        $query = OrderItem::with(['order', 'order.user'])
            ->where('seller_id', auth('seller')->id());

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $items = $query->latest()->paginate(15);

        $counts = [
            'all'       => OrderItem::where('seller_id', auth('seller')->id())->count(),
            'pending'   => OrderItem::where('seller_id', auth('seller')->id())->where('status', 'pending')->count(),
            'confirmed' => OrderItem::where('seller_id', auth('seller')->id())->where('status', 'confirmed')->count(),
            'shipped'   => OrderItem::where('seller_id', auth('seller')->id())->where('status', 'shipped')->count(),
            'delivered' => OrderItem::where('seller_id', auth('seller')->id())->where('status', 'delivered')->count(),
        ];

        return view('seller.orders.index', compact('items', 'counts'));
    }

    public function show(Order $order)
    {
        // Ensure seller has items in this order
        $hasItems = OrderItem::where('order_id', $order->id)
            ->where('seller_id', auth('seller')->id())
            ->exists();

        if (!$hasItems) abort(403);

        $myItems = OrderItem::where('order_id', $order->id)
            ->where('seller_id', auth('seller')->id())
            ->get();

        $escrow = $order->escrow;

        return view('seller.orders.show', compact('order', 'myItems', 'escrow'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:confirmed,shipped'],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        $hasItems = OrderItem::where('order_id', $order->id)
            ->where('seller_id', auth('seller')->id())
            ->exists();

        if (!$hasItems) abort(403);

        $fromStatus = $order->status;

        OrderStatusLog::create([
            'order_id'        => $order->id,
            'from_status'     => $fromStatus,
            'to_status'       => $request->status,
            'changed_by_type' => 'seller',
            'changed_by_id'   => auth('seller')->id(),
            'note'            => $request->note,
        ]);

        $order->update(['status' => $request->status]);

        // Update seller's specific items
        OrderItem::where('order_id', $order->id)
            ->where('seller_id', auth('seller')->id())
            ->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated to ' . ucfirst($request->status));
    }
}