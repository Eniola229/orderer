<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\WalletService;
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
        $order->load(['user', 'items.seller', 'statusLogs', 'escrow']);
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

        $this->wallet->releaseEscrow($order);

        OrderStatusLog::create([
            'order_id'        => $order->id,
            'from_status'     => $order->status,
            'to_status'       => 'completed',
            'changed_by_type' => 'admin',
            'changed_by_id'   => auth('admin')->id(),
            'note'            => 'Manually completed by admin.',
        ]);

        return back()->with('success', 'Order completed and escrow released.');
    }

    public function forceRefund(Order $order)
    {
        if (!auth('admin')->user()->canEditOrders()) abort(403);

        $this->wallet->refundEscrow($order);

        $order->update(['status' => 'cancelled']);

        OrderStatusLog::create([
            'order_id'        => $order->id,
            'from_status'     => $order->status,
            'to_status'       => 'cancelled',
            'changed_by_type' => 'admin',
            'changed_by_id'   => auth('admin')->id(),
            'note'            => 'Refund issued by admin.',
        ]);

        return back()->with('success', 'Order refunded to buyer wallet.');
    }
}