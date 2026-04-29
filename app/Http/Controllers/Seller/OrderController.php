<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Notification;
use App\Services\ShipbubbleService;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        protected ShipbubbleService $shipbubble,
        protected BrevoMailService  $brevo
    ) {}
 
    public function index(Request $request)
    {
        $sellerId = auth('seller')->id();

        $query = OrderItem::where('seller_id', $sellerId)
            ->with(['order.user', 'order'])
            ->whereHas('order', function ($q) {
                $q->whereNotIn('payment_status', ['pending', 'failed']);
            })
            ->latest();

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->whereNotIn('payment_status', ['pending', 'failed'])
                  ->where(function ($q) use ($search) {
                      $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('shipping_name', 'like', "%{$search}%");
                  });
            });
        }

        $items = $query->paginate(20)->withQueryString();

        // Reusable base to keep stats consistent with the same filter
        $base = fn() => OrderItem::where('seller_id', $sellerId)
            ->whereHas('order', fn($q) => $q->whereNotIn('payment_status', ['pending', 'failed']));

        $stats = [
            'total'     => $base()->count(),
            'pending'   => $base()->where('status', 'pending')->count(),
            'confirmed' => $base()->where('status', 'confirmed')->count(),
            'shipped'   => $base()->where('status', 'shipped')->count(),
            'completed' => $base()->where('status', 'completed')->count(),
        ];

        return view('seller.orders.index', compact('items', 'stats'));
    }

    public function show(Order $order)
    {
        // Verify this seller has items in this order
        $hasItem = OrderItem::where('order_id', $order->id)
            ->where('seller_id', auth('seller')->id())
            ->exists();

        if (!$hasItem) abort(403);

        $order->load(['items' => function ($q) {
            $q->where('seller_id', auth('seller')->id());
        }, 'user', 'statusLogs']);

        $myItems = $order->items;

        return view('seller.orders.show', compact('order', 'myItems'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:confirmed,shipped,delivered,cancelled'],
            'note'   => ['nullable', 'string', 'max:500'],
            // Shipping fields when marking as shipped
            'tracking_number'         => ['nullable', 'string'],
            'tracking_url'            => ['nullable', 'url'],
            'shipping_carrier'        => ['nullable', 'string'],
            'shipping_service_name'   => ['nullable', 'string'],
            'estimated_delivery_date' => ['nullable', 'string'],
        ]);

        if ($request->status === 'cancelled') {
            return back()->with('error', 'To cancel an order, please contact support. We will review and process the cancellation on your behalf.');
        }

        $sellerId = auth('seller')->id();

        $hasItem = OrderItem::where('order_id', $order->id)
            ->where('seller_id', $sellerId)
            ->exists();

        if (!$hasItem) abort(403);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        DB::transaction(function () use ($order, $request, $oldStatus, $newStatus, $sellerId) {
            // Update order item status for this seller
            OrderItem::where('order_id', $order->id)
                ->where('seller_id', $sellerId)
                ->update(['status' => $newStatus]);

            // Update order-level status
            // Only update if ALL items share same status
            $allItems      = OrderItem::where('order_id', $order->id)->get();
            $allSameStatus = $allItems->every(fn($i) => $i->status === $newStatus);

            $orderUpdate = [];

            if ($allSameStatus) {
                $orderUpdate['status'] = $newStatus;
            }

            // Shipping details when marking shipped
            // if ($newStatus === 'shipped') {
            //     if ($request->tracking_number) {
            //         $orderUpdate['tracking_number']         = $request->tracking_number;
            //         $orderUpdate['tracking_url']            = $request->tracking_url;
            //         $orderUpdate['shipping_carrier']        = $request->shipping_carrier;
            //         $orderUpdate['shipping_service_name']   = $request->shipping_service_name;
            //         $orderUpdate['estimated_delivery_date'] = $request->estimated_delivery_date;
            //     }
            // }

            if (!empty($orderUpdate)) {
                $order->update($orderUpdate);
            }

            // Log
            OrderStatusLog::create([
                'order_id'        => $order->id,
                'from_status'     => $oldStatus,
                'to_status'       => $newStatus,
                'changed_by_type' => 'seller',
                'changed_by_id'   => $sellerId,
                'note'            => $request->note,
            ]);

            // Notify buyer
            $messages = [
                'confirmed'  => "Your order #{$order->order_number} has been confirmed and is being prepared.",
                'shipped'    => "Your order #{$order->order_number} has been shipped!" . ($request->tracking_number ? " Tracking: {$request->tracking_number}" : ''),
                'delivered'  => "Your order #{$order->order_number} has been delivered.",
                'cancelled'  => "A seller has cancelled part of your order #{$order->order_number}.",
            ];

            Notification::create([
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $order->user_id,
                'type'            => 'order_' . $newStatus,
                'title'           => 'Order ' . ucfirst($newStatus),
                'body'            => $messages[$newStatus] ?? "Order #{$order->order_number} status updated.",
                'action_url'      => route('buyer.orders.show', $order->id),
            ]);
        });

        try {
            $sellerItems = OrderItem::where('order_id', $order->id)
                ->where('seller_id', $sellerId)
                ->with('product')
                ->get();

            $this->brevo->sendOrderStatusUpdate(
                $order->user,
                $order,
                $sellerItems,
                $newStatus,
                $request->tracking_number ?? null
            );
        } catch (\Exception $e) {
            \Log::error('Order status email failed', [
                'order_id' => $order->id,
                'status'   => $newStatus,
                'error'    => $e->getMessage(),
            ]);
        }

        return back()->with('success', "Order status updated to " . ucfirst($newStatus) . ".");
    }
}