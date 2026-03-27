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

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($r) => $r->where('email', 'like', "%{$s}%"))
            );
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total'     => Order::count(),
            'pending'   => Order::where('status', 'pending')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'revenue'   => Order::where('payment_status', 'paid')->sum('total'),
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