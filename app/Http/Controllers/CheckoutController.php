<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\KorapayTransaction;
use App\Services\WalletService;
use App\Services\KorapayService;
use App\Services\BrevoMailService;
use App\Services\ShipbubbleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
 
class CheckoutController extends Controller
{
    public function __construct(
        protected WalletService     $walletService,
        protected KorapayService    $korapay,
        protected BrevoMailService  $brevo,
        protected ShipbubbleService $shipbubble
    ) {}

    public function index()
    {
        $cartModel = $this->getCart();
        $items     = $cartModel->items()->with(['product.images'])->get();

        if ($items->isEmpty()) {
            return redirect()->route('shop.index')->with('info', 'Your cart is empty.');
        }

        $cartItems = $items->map(function ($item) {
            $product = $item->product;
            $img     = $product?->images->where('is_primary', true)->first()
                       ?? $product?->images->first();
            return [
                'id'       => $item->product_id,
                'name'     => $product?->name,
                'price'    => (float) $item->price,
                'quantity' => $item->quantity,
                'image'    => $img?->image_url ?? null,
            ];
        })->toArray();

        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['quantity']);

        return view('storefront.checkout', compact('cartItems', 'subtotal'));
    }

    public function place(Request $request)
    {
        $request->validate([
            'shipping_name'         => ['required', 'string', 'max:200'],
            'shipping_phone'        => ['required', 'string', 'max:20'],
            'shipping_address'      => ['required', 'string'],
            'shipping_city'         => ['required', 'string', 'max:100'],
            'shipping_state'        => ['required', 'string', 'max:100'],
            'shipping_country'      => ['required', 'string', 'max:30'],
            'payment_method'        => ['required', 'in:wallet,korapay'],
            'shipping_service_code' => ['required', 'string'],
            'shipping_carrier'      => ['required', 'string'],
            'shipping_service_name' => ['required', 'string'],
            'shipping_fee'          => ['required', 'numeric', 'min:0'],
        ]);

        $cartModel = $this->getCart();
        $cartItems = $cartModel->items()->with(['product.images'])->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Cart is empty.');
        }

        $user        = auth('web')->user();
        $subtotal    = 0;
        $items       = [];
        $totalWeight = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;
            if (!$product || $product->status !== 'approved') continue;

            $totalPrice   = $item->price * $item->quantity;
            $subtotal    += $totalPrice;
            $totalWeight += ($product->weight_kg ?? 0.5) * $item->quantity;
            $items[]      = [
                'product'  => $product,
                'cartItem' => ['price' => $item->price, 'quantity' => $item->quantity],
                'total'    => $totalPrice,
            ];
        }
        if ($totalWeight <= 0) $totalWeight = 0.5;

        $shippingFee = (float) $request->shipping_fee;
        $total       = $subtotal + $shippingFee;

        if ($request->payment_method === 'wallet' && $user->wallet_balance < $total) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'order_number'          => 'ORD-' . strtoupper(Str::random(10)),
                'user_id'               => $user->id,
                'subtotal'              => $subtotal,
                'total'                 => $total,
                'payment_method'        => $request->payment_method,
                'payment_status'        => $request->payment_method === 'wallet' ? 'paid' : 'pending',
                'status'                => 'pending',
                'shipping_name'         => $request->shipping_name,
                'shipping_phone'        => $request->shipping_phone,
                'shipping_address'      => $request->shipping_address,
                'shipping_city'         => $request->shipping_city,
                'shipping_state'        => $request->shipping_state,
                'shipping_country'      => $request->shipping_country,
                'shipping_zip'          => $request->shipping_zip,
                'notes'                 => $request->notes,
                'shipping_fee'          => $shippingFee,
                'shipping_carrier'      => $request->shipping_carrier,
                'shipping_service_code' => $request->shipping_service_code,
                'shipping_service_name' => $request->shipping_service_name,
                'package_weight'        => $totalWeight,
                'declared_value'        => $subtotal,
                'shipping_rate_data'    => json_decode($request->shipping_rate_data ?? '{}', true),
            ]);

            foreach ($items as $item) {
                $product        = $item['product'];
                $cartItem       = $item['cartItem'];
                $commissionRate = $product->category->commission_rate ?? 10;
                $commissionAmt  = round($item['total'] * ($commissionRate / 100), 2);
                $sellerEarnings = $item['total'] - $commissionAmt;
                $primaryImg     = $product->images->where('is_primary', true)->first() ?? $product->images->first();

                OrderItem::create([
                    'order_id'          => $order->id,
                    'seller_id'         => $product->seller_id,
                    'orderable_type'    => 'App\Models\Product',
                    'orderable_id'      => $product->id,
                    'item_name'         => $product->name,
                    'item_image'        => $primaryImg->image_url ?? null,
                    'unit_price'        => $cartItem['price'],
                    'quantity'          => $cartItem['quantity'],
                    'total_price'       => $item['total'],
                    'commission_rate'   => $commissionRate,
                    'commission_amount' => $commissionAmt,
                    'seller_earnings'   => $sellerEarnings,
                    'status'            => 'pending',
                ]);

                $product->decrement('stock', $cartItem['quantity']);
                $product->increment('total_sold', $cartItem['quantity']);
            }

            OrderStatusLog::create([
                'order_id'        => $order->id,
                'from_status'     => null,
                'to_status'       => 'pending',
                'changed_by_type' => 'buyer',
                'changed_by_id'   => $user->id,
                'note'            => 'Order placed.',
            ]);

            if ($request->payment_method === 'wallet') {
                $this->walletService->debit(
                    $user,
                    $total,
                    'debit',
                    "Payment for order #{$order->order_number}",
                    'order',
                    $order->id
                );

                $this->walletService->holdEscrow($order);

                // Book shipment with Shipbubble immediately after wallet payment
                $this->bookShipmentForOrder($order, $user, $totalWeight, $subtotal);

                DB::commit();
                $this->getCart()->items()->delete();

                $this->sendOrderEmails($user, $order);

                return redirect()->route('buyer.orders.show', $order->id)
                    ->with('success', "Order #{$order->order_number} placed successfully!");

            } else {
                // Korapay — book shipment after payment callback
                $reference = $this->korapay->generateReference('ORD');
                $order->update(['payment_reference' => $reference]);

                $this->korapay->createTransaction($user, $total, 'order_payment', $reference);

                DB::commit();
                $this->getCart()->items()->delete();

                $checkoutData = $this->korapay->initializeCheckout(
                    $user->email,
                    $user->full_name,
                    $total,
                    $reference,
                    route('checkout.callback'),
                    '',
                    ['order_id' => $order->id, 'type' => 'order_payment']
                );

                return redirect($checkoutData['checkout_url']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout place error: ' . $e->getMessage());
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    /**
     * Book the shipment with Shipbubble after payment is confirmed.
     * Uses request_token from session + service_code + courier_id from order.
     */
    protected function bookShipmentForOrder(Order $order, $user, float $weight, float $declaredValue): void
    {
        $requestToken = session('shipbubble_request_token');

        \Log::info('bookShipmentForOrder called', [
            'order_id'      => $order->id,
            'request_token' => $requestToken,
            'service_code'  => $order->shipping_service_code,
            'carrier'       => $order->shipping_carrier,
        ]);

        if (!$requestToken) {
            \Log::warning('bookShipmentForOrder — no request_token in session, skipping', [
                'order_id' => $order->id,
            ]);
            return;
        }

        try {
            // Extract courier_id from saved rate data
            $rateData  = $order->shipping_rate_data ?? [];
            $courierId = $rateData['courier_id'] ?? $rateData['id'] ?? null;

            \Log::info('bookShipmentForOrder — rate data', [
                'order_id'  => $order->id,
                'rate_data' => $rateData,
                'courier_id'=> $courierId,
            ]);

            if (!$courierId) {
                \Log::warning('bookShipmentForOrder — no courier_id found in rate_data', [
                    'order_id'  => $order->id,
                    'rate_data' => $rateData,
                ]);
                return;
            }

            $shipment = $this->shipbubble->createShipment(
                $order->shipping_service_code,
                (string) $courierId,
                [
                    'name'    => config('app.name'),
                    'email'   => config('mail.from.address'),
                    'phone'   => '08000000000',
                    'address' => 'Orderer Fulfillment Center, Lagos',
                    'city'    => 'Lagos',
                    'state'   => 'Lagos',
                    'country' => 'NG',
                ],
                [
                    'name'    => $order->shipping_name,
                    'email'   => $user->email,
                    'phone'   => $order->shipping_phone,
                    'address' => $order->shipping_address,
                    'city'    => $order->shipping_city,
                    'state'   => $order->shipping_state,
                    'country' => $order->shipping_country,
                ],
                [
                    'weight' => $weight,
                    'length' => 20,
                    'width'  => 20,
                    'height' => 20,
                    'items'  => [[
                        'name'     => "Order #{$order->order_number}",
                        'quantity' => 1,
                        'value'    => max($declaredValue, 10),
                    ]],
                ],
                $requestToken
            );

            \Log::info('Shipbubble createShipment response', [
                'order_id' => $order->id,
                'shipment' => $shipment,
            ]);

            // Save using correct field names from API response
            $order->update([
                'shipbubble_shipment_id'  => $shipment['order_id'] ?? null,
                'tracking_number'         => $shipment['courier']['tracking_code'] ?? null,
                'tracking_url'            => $shipment['tracking_url'] ?? null,
                'estimated_delivery_date' => $shipment['estimated_delivery_date'] ?? null,
                'courier_id'              => $courierId,
                'shipping_status'         => $shipment['status'] ?? 'pending',
            ]);

            session()->forget('shipbubble_request_token');

        } catch (\Exception $e) {
            \Log::error('bookShipmentForOrder failed for order #' . $order->order_number . ': ' . $e->getMessage());
            // Don't fail the order — shipment can be manually processed
        }
    }

    public function getRates(Request $request)
    {
        $request->validate([
            'shipping_address' => ['required', 'string'],
            'shipping_city'    => ['required', 'string'],
            'shipping_state'   => ['required', 'string'],
            'shipping_country' => ['required', 'string'],
            'shipping_name'    => ['required', 'string'],
            'shipping_phone'   => ['required', 'string'],
        ]);

        $cartModel = $this->getCart();
        $cartItems = $cartModel->items()->with(['product.seller'])->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $totalWeight = 0;
        $subtotal    = 0;
        $itemName    = 'Package';
        $sellerIds   = [];

        foreach ($cartItems as $item) {
            $product = $item->product;
            if (!$product) continue;
            $totalWeight += ($product->weight_kg ?? 0.5) * $item->quantity;
            $subtotal    += $item->price * $item->quantity;
            $itemName     = $product->name;
            if ($product->seller_id) {
                $sellerIds[] = $product->seller_id;
            }
        }

        if ($totalWeight <= 0) $totalWeight = 0.5;

        $uniqueSellerIds = array_unique($sellerIds);

        try {
            // ── Sender address code ─────────────────────────────────────────
            // If all items belong to one seller, use their pre-validated address_code.
            // Otherwise fall back to our default fulfillment address.
            if (count($uniqueSellerIds) === 1) {
                $seller = \App\Models\Seller::find($uniqueSellerIds[0]);

                if ($seller && $seller->address_code) {
                    $senderAddressCode = $seller->address_code;
                } else {
                    // Seller exists but has no address_code — validate default
                    $senderValidation  = $this->shipbubble->validateAddress([
                        'name'    => 'Orderer Fulfillment',
                        'email'   => config('mail.from.address'),
                        'phone'   => '08000000000',
                        'address' => 'Orderer Fulfillment Center Lagos',
                        'city'    => 'Lagos',
                        'state'   => 'Lagos',
                        'country' => 'NG',
                    ]);
                    $senderAddressCode = $senderValidation['data']['address_code'] ?? null;
                }
            } else {
                // Multiple sellers — use our default fulfillment address
                $senderValidation  = $this->shipbubble->validateAddress([
                    'name'    => 'Orderer Fulfillment',
                    'email'   => config('mail.from.address'),
                    'phone'   => '08000000000',
                    'address' => 'Orderer Fulfillment Center Lagos',
                    'city'    => 'Lagos',
                    'state'   => 'Lagos',
                    'country' => 'NG',
                ]);
                $senderAddressCode = $senderValidation['data']['address_code'] ?? null;
            }

            // ── Recipient validation ────────────────────────────────────────
            $recipientValidation = $this->shipbubble->validateAddress([
                'name'    => $request->shipping_name,
                'email'   => auth('web')->user()->email,
                'phone'   => $request->shipping_phone,
                'address' => $request->shipping_address,
                'city'    => $request->shipping_city,
                'state'   => $request->shipping_state,
                'country' => $request->shipping_country,
            ]);

            $recipientAddressCode = $recipientValidation['data']['address_code'] ?? null;

            if (!$senderAddressCode || !$recipientAddressCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not validate one or both addresses. Please provide a more detailed address.',
                ], 422);
            }

            $rates    = $this->shipbubble->getRates([
                'sender_address_code'   => $senderAddressCode,
                'reciever_address_code' => $recipientAddressCode,
                'weight'                => $totalWeight,
                'value'                 => max($subtotal, 10),
                'length'                => 20,
                'width'                 => 20,
                'height'                => 20,
                'category_id'           => 2178251,
                'item_name'             => $itemName,
            ]);

            $rateData = is_array($rates) && isset($rates['data']) ? $rates['data'] : $rates;

            if (!empty($rateData['request_token'])) {
                session(['shipbubble_request_token' => $rateData['request_token']]);
                \Log::info('Stored shipbubble_request_token', ['token' => $rateData['request_token']]);
            }

            return response()->json([
                'success' => true,
                'rates'   => $rateData['couriers'] ?? [],
            ]);

        } catch (\Exception $e) {
            $rawMessage = $e->getMessage();
            $apiMessage = null;
            if (preg_match('/\{.*\}/s', $rawMessage, $match)) {
                $decoded    = json_decode($match[0], true);
                $apiMessage = $decoded['message'] ?? null;
            }

            \Log::error('Checkout: failed to fetch courier rates', ['error' => $rawMessage]);

            return response()->json([
                'success' => false,
                'message' => $apiMessage ?? 'Could not find a courier for this address. Please check the details and try again.',
            ], 422);
        }
    }

    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('buyer.orders')->with('error', 'Invalid payment reference.');
        }

        $txn = KorapayTransaction::where('reference', $reference)->first();
        if (!$txn) {
            return redirect()->route('buyer.orders')->with('error', 'Transaction not found.');
        }

        try {
            $data = $this->korapay->verifyTransaction($reference);

            if ($data['status'] === 'success') {
                $txn->update([
                    'status'            => 'success',
                    'korapay_reference' => $data['payment_reference'] ?? null,
                    'gateway_response'  => $data,
                ]);

                $order = Order::where('payment_reference', $reference)->first();

                if ($order) {
                    $order->update(['payment_status' => 'paid']);
                    $this->walletService->holdEscrow($order);

                    $user = auth('web')->user() ?? $order->user;

                    // Book shipment after Korapay payment confirmed
                    $this->bookShipmentForOrder($order, $user, $order->package_weight ?? 0.5, $order->declared_value ?? $order->subtotal);

                    $this->sendOrderEmails($user, $order);

                    return redirect()->route('buyer.orders.show', $order->id)
                        ->with('success', "Payment successful! Order #{$order->order_number} confirmed.");
                }
            }

            return redirect()->route('buyer.orders')->with('error', 'Payment verification failed.');

        } catch (\Exception $e) {
            \Log::error('Checkout callback error: ' . $e->getMessage());
            return redirect()->route('buyer.orders')->with('error', 'Payment callback error. Contact support.');
        }
    }

    protected function sendOrderEmails($user, Order $order): void
    {
        try {
            $this->brevo->sendOrderPlacedBuyer($user, $order);

            $order->load('items');
            $sellerIds = $order->items->pluck('seller_id')->unique();

            foreach ($sellerIds as $sellerId) {
                $seller = \App\Models\Seller::find($sellerId);
                if ($seller) {
                    $this->brevo->sendOrderNotifySeller($seller, $order);
                    \App\Models\Notification::create([
                        'notifiable_type' => 'App\Models\Seller',
                        'notifiable_id'   => $sellerId,
                        'type'            => 'new_order',
                        'title'           => 'New Order Received',
                        'body'            => "New order #{$order->order_number} is waiting for you.",
                        'action_url'      => route('seller.orders.show', $order->id),
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('sendOrderEmails failed: ' . $e->getMessage());
        }
    }

    private function getCart(): \App\Models\Cart
    {
        if (auth('web')->check()) {
            return \App\Models\Cart::firstOrCreate(
                ['user_id' => auth('web')->id()],
                ['session_id' => null]
            );
        }
        return \App\Models\Cart::firstOrCreate(
            ['session_id' => session()->getId(), 'user_id' => null]
        );
    }

    // protected function fallbackRates(string $country): array
    // {
    //     $isNigeria = strtoupper($country) === 'NG';

    //     return $isNigeria ? [
    //         ['service_code' => 'standard_ng', 'courier' => ['name' => 'Standard Delivery'], 'service' => ['name' => 'Standard (3-5 days)'], 'total' => 3.50, 'delivery_eta' => '3-5 business days'],
    //         ['service_code' => 'express_ng',  'courier' => ['name' => 'Express Delivery'],  'service' => ['name' => 'Express (1-2 days)'],   'total' => 8.00, 'delivery_eta' => '1-2 business days'],
    //     ] : [
    //         ['service_code' => 'intl_standard', 'courier' => ['name' => 'DHL'], 'service' => ['name' => 'International Standard (7-14 days)'], 'total' => 25.00, 'delivery_eta' => '7-14 business days'],
    //         ['service_code' => 'intl_express',  'courier' => ['name' => 'DHL'], 'service' => ['name' => 'International Express (3-5 days)'],   'total' => 55.00, 'delivery_eta' => '3-5 business days'],
    //     ];
    // }
}