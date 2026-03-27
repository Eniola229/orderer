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
            ->latest();

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(20)->withQueryString();

        $stats = [
            'total'     => OrderItem::where('seller_id', $sellerId)->count(),
            'pending'   => OrderItem::where('seller_id', $sellerId)->where('status', 'pending')->count(),
            'confirmed' => OrderItem::where('seller_id', $sellerId)->where('status', 'confirmed')->count(),
            'shipped'   => OrderItem::where('seller_id', $sellerId)->where('status', 'shipped')->count(),
            'completed' => OrderItem::where('seller_id', $sellerId)->where('status', 'completed')->count(),
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
            if ($newStatus === 'shipped') {
                if ($request->tracking_number) {
                    $orderUpdate['tracking_number']         = $request->tracking_number;
                    $orderUpdate['tracking_url']            = $request->tracking_url;
                    $orderUpdate['shipping_carrier']        = $request->shipping_carrier;
                    $orderUpdate['shipping_service_name']   = $request->shipping_service_name;
                    $orderUpdate['estimated_delivery_date'] = $request->estimated_delivery_date;
                }
            }

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

        return back()->with('success', "Order status updated to " . ucfirst($newStatus) . ".");
    }

    /**
     * Book shipment with Shipbubble for this order after it's confirmed.
     * Called when seller clicks "Book Shipment" on the order detail page.
     */
    public function bookShipment(Request $request, Order $order)
    {
        $hasItem = OrderItem::where('order_id', $order->id)
            ->where('seller_id', auth('seller')->id())
            ->exists();

        if (!$hasItem) abort(403);

        $request->validate([
            'service_code' => ['required', 'string'],
            'carrier'      => ['required', 'string'],
            'service_name' => ['required', 'string'],
            'rate_data'    => ['nullable', 'string'],
        ]);

        $seller = auth('seller')->user();

        $sender    = $this->shipbubble->buildSenderFromSeller($seller);
        $recipient = $this->shipbubble->buildRecipientFromOrder($order);

        $package = [
            'weight'  => $order->package_weight ?? 0.5,
            'length'  => 20,
            'width'   => 20,
            'height'  => 20,
            'items'   => $order->items->map(fn($i) => [
                'name'     => $i->item_name,
                'quantity' => $i->quantity,
                'value'    => $i->unit_price,
            ])->toArray(),
        ];

        try {
            $requestToken = session('shipbubble_request_token_' . $order->id);

            $shipment = $this->shipbubble->createShipment(
                $request->service_code,
                $sender,
                $recipient,
                $package,
                $requestToken ?? ''
            );

            $order->update([
                'shipbubble_shipment_id'  => $shipment['shipment_id'] ?? null,
                'tracking_number'         => $shipment['tracking_number'] ?? null,
                'tracking_url'            => $shipment['tracking_url'] ?? null,
                'shipping_carrier'        => $request->carrier,
                'shipping_service_name'   => $request->service_name,
                'estimated_delivery_date' => $shipment['estimated_delivery_date'] ?? null,
                'status'                  => 'shipped',
            ]);

            OrderItem::where('order_id', $order->id)
                ->where('seller_id', auth('seller')->id())
                ->update(['status' => 'shipped']);

            OrderStatusLog::create([
                'order_id'        => $order->id,
                'from_status'     => $order->status,
                'to_status'       => 'shipped',
                'changed_by_type' => 'seller',
                'changed_by_id'   => auth('seller')->id(),
                'note'            => "Shipment booked via {$request->carrier}. Tracking: " . ($shipment['tracking_number'] ?? 'N/A'),
            ]);

            Notification::create([
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $order->user_id,
                'type'            => 'order_shipped',
                'title'           => 'Order Shipped',
                'body'            => "Order #{$order->order_number} has been shipped via {$request->carrier}. Tracking: " . ($shipment['tracking_number'] ?? 'N/A'),
                'action_url'      => route('buyer.orders.show', $order->id),
            ]);

            return back()->with('success', 'Shipment booked successfully! Tracking: ' . ($shipment['tracking_number'] ?? 'N/A'));

        } catch (\Exception $e) {
            return back()->with('error', 'Shipment booking failed: ' . $e->getMessage());
        }
    }

    /**
     * Fetch Shipbubble rates for this order (AJAX).
     */
    public function getShipmentRates(Request $request, Order $order)
    {
        $request->validate([
            'weight_kg'   => ['nullable', 'numeric', 'min:0.1'],
            'length'      => ['nullable', 'numeric', 'min:1'],
            'width'       => ['nullable', 'numeric', 'min:1'],
            'height'      => ['nullable', 'numeric', 'min:1'],
        ]);

        $hasItem = OrderItem::where('order_id', $order->id)
            ->where('seller_id', auth('seller')->id())
            ->exists(); 

        if (!$hasItem) abort(403);

        try {
            // Step 1 — validate sender address to get address_code
            $seller = auth('seller')->user();
            $senderValidation = $this->shipbubble->validateAddress([
                'name'    => $seller->name ?? '',
                'email'   => $seller->email ?? '',
                'phone'   => $seller->phone ?? '',
                'address' => $seller->address ?? '',
                'city'    => $seller->city ?? '',
                'state'   => $seller->state ?? $seller->city ?? '',
                'country' => $seller->country ?? '',
            ]);

            // Step 2 — validate recipient address to get address_code
            $recipientValidation = $this->shipbubble->validateAddress([
                'name'    => $order->recipient_name ?? $order->customer_name ?? 'Recipient',
                'email'   => $order->customer_email ?? '',
                'phone'   => $order->customer_phone ?? '',
                'address' => $order->delivery_address ?? '',
                'city'    => $order->delivery_city,
                'state'   => $order->delivery_state ?? $order->delivery_city,
                'country' => $order->delivery_country,
            ]);

            $senderAddressCode    = $senderValidation['data']['address_code'] ?? null;
            $recipientAddressCode = $recipientValidation['data']['address_code'] ?? null;

            if (!$senderAddressCode || !$recipientAddressCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not validate one or both addresses. Please provide a more detailed address.',
                ], 422);
            }

            // Step 3 — calculate total weight from order items
            $totalWeight = $order->items->sum(fn($i) => ($i->product->weight_kg ?? 0.5) * $i->quantity);
            if ($totalWeight <= 0) $totalWeight = 0.5;

            // Step 4 — fetch rates
            $response = $this->shipbubble->getRates([
                'sender_address_code'   => $senderAddressCode,
                'reciever_address_code' => $recipientAddressCode,

                'weight'      => $request->weight_kg ?? max($totalWeight, 0.1),
                'value'       => (float)($order->total_value ?? 10),
                'length'      => $request->length ?? 25,
                'width'       => $request->width ?? 25,
                'height'      => $request->height ?? 25,
                'category_id' => 2178251,

                'item_name'   => 'Package',
                // If you need to pass multiple items, you might need to adjust this based on your shipbubble API
                // 'items' => $order->items->map(fn($i) => [
                //     'name'     => $i->item_name,
                //     'quantity' => $i->quantity,
                //     'value'    => $i->unit_price,
                // ])->toArray(),
            ]);

            // Log full Shipbubble response for debugging (optional)
            // \Log::info('Shipbubble full rates response', $response);

            $rateData = $response['data'] ?? $response;

            if (!empty($rateData['request_token'])) {
                session(['shipbubble_request_token_' . $order->id => $rateData['request_token']]);
            }

            $couriers = $rateData['couriers'] ?? [];

            return response()->json([
                'success' => true,
                'rates'   => $couriers,
            ]);

        } catch (\Exception $e) {
            $rawMessage = $e->getMessage();

            // Try to extract the real API message from JSON in the exception
            $apiMessage = null;
            if (preg_match('/\{.*\}/s', $rawMessage, $match)) {
                $decoded    = json_decode($match[0], true);
                $apiMessage = $decoded['message'] ?? null;
            }

            \Log::error('getShipmentRates: failed to fetch courier rates', [
                'error'            => $rawMessage,
                'order_id'         => $order->id,
                'seller_id'        => auth('seller')->id(),
                'delivery_city'    => $order->delivery_city,
                'delivery_country' => $order->delivery_country,
            ]);

            return response()->json([
                'success' => false,
                'message' => $apiMessage ?? 'Could not find a courier for this address. Please check the details and try again.',
            ], 422);
        }
    }
}