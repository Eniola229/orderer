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
        // CHANGED: eager-load selected_options via cart items (already on the model)
        $items = $cartModel->items()->with(['product.images'])->get();

        if ($items->isEmpty()) {
            return redirect()->route('shop.index')->with('info', 'Your cart is empty.');
        }

        $cartItems = $items->map(function ($item) {
            $product = $item->product;
            $img = $product?->images->where('is_primary', true)->first()
                   ?? $product?->images->first();

            $regularPrice = $product?->sale_price ?? $product?->price;
            $isFlashSale  = $product && (float) $item->price < (float) $regularPrice;

            return [
                'id'               => $item->product_id,
                'name'             => $product?->name,
                'price'            => (float) $item->price,
                'quantity'         => $item->quantity,
                'image'            => $img?->image_url ?? null,
                'is_flash_sale'    => $isFlashSale,
                'selected_options' => $item->selected_options ?? [], 
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
        // CHANGED: include selected_options in the cart items query
        $cartItems = $cartModel->items()->with(['product.images', 'product.category'])->get();

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
                'product'          => $product,
                'cartItem'         => [
                    'price'            => $item->price,
                    'quantity'         => $item->quantity,
                    'selected_options' => $item->selected_options, 
                ],
                'total'            => $totalPrice,
            ];
        }
        if ($totalWeight <= 0) $totalWeight = 0.5;

        $shippingFee   = (float) $request->shipping_fee;
        $total         = $subtotal + $shippingFee;
        $isMultiSeller = collect($items)->pluck('product.seller_id')->unique()->count() > 1;

        if ($request->payment_method === 'wallet' && $user->wallet_balance < $total) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
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
                'is_multi_seller'       => $isMultiSeller,
                'shipping_rate_data'    => json_decode($request->shipping_rate_data ?? '{}', true),
            ]);

            foreach ($items as $item) {
                $product        = $item['product'];
                $cartItem       = $item['cartItem'];
                $commissionRate = $product->category->commission_rate ?? 10;
                $commissionAmt  = round($item['total'] * ($commissionRate / 100), 2);
                $sellerEarnings = $item['total'] - $commissionAmt;
                $primaryImg     = $product->images->where('is_primary', true)->first()
                                  ?? $product->images->first();

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
                    'selected_options'  => $cartItem['selected_options'], 
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

                    foreach ($items as $item) {
                        $product = $item['product'];
                        $quantity = $item['cartItem']['quantity'];
                        
                        $flashSale = \App\Models\FlashSale::where('product_id', $product->id)
                            ->where('is_active', true)
                            ->where('starts_at', '<=', now())
                            ->where('ends_at', '>=', now())
                            ->first();
                        
                        if ($flashSale) {
                            $flashSale->increment('quantity_sold', $quantity);
                        }
                    }

                $this->walletService->holdEscrow($order);
                $this->bookShipmentForOrder($order, $user, $totalWeight, $subtotal);

                DB::commit();
                $this->getCart()->items()->delete();
                $this->sendOrderEmails($user, $order);

                return redirect()->route('buyer.orders.show', $order->id)
                    ->with('success', "Order #{$order->order_number} placed successfully!");

            } else {
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
            return back()->with('error', 'Order failed');
        }
    }

    // ── All methods below are UNCHANGED from your existing file ──────────

    protected function bookShipmentForOrder(Order $order, $user, float $weight, float $declaredValue): void
    {
        $requestTokensMap = session('shipbubble_request_tokens', []);
        $legacyToken      = session('shipbubble_request_token');

        \Log::info('bookShipmentForOrder called', [
            'order_id'        => $order->id,
            'is_multi_seller' => $order->is_multi_seller,
            'tokens'          => $requestTokensMap,
        ]);

        $order->load('items.product');

        $sellerGroups = $order->items->groupBy('seller_id');
        $rateDataMap  = $order->shipping_rate_data ?? [];

        foreach ($sellerGroups as $sellerId => $sellerItems) {
            $token = $requestTokensMap[(string) $sellerId]
                  ?? ($order->is_multi_seller ? null : ($legacyToken ?? array_values($requestTokensMap)[0] ?? null));

            $rateData  = $order->is_multi_seller
                ? ($rateDataMap[(string) $sellerId] ?? [])
                : (isset($rateDataMap[(string) $sellerId])
                    ? $rateDataMap[(string) $sellerId]
                    : (array_values($rateDataMap)[0] ?? $rateDataMap));

            $courierId   = $rateData['courier_id'] ?? $rateData['id'] ?? null;
            $serviceCode = $rateData['service_code'] ?? $order->shipping_service_code;

            if (!$token || !$courierId) {
                \Log::warning("bookShipmentForOrder — skipping seller {$sellerId}: missing token or courier_id");
                continue;
            }

            $sellerWeight = $sellerItems->sum(fn($i) => ($i->product->weight_kg ?? 0.5) * $i->quantity);
            $sellerValue  = $sellerItems->sum('total_price');

            try {
                $seller   = \App\Models\Seller::find($sellerId);
                $shipment = $this->shipbubble->createShipment(
                    $serviceCode,
                    (string) $courierId,
                    $this->buildSenderPayload($seller),
                    $this->buildRecipientPayload($order, $user),
                    $this->buildParcelPayload($sellerItems, max($sellerWeight, 0.5), $sellerValue, $order->order_number),
                    $token
                );

                $trackingNumber = $shipment['courier']['tracking_code'] ?? null;
                $trackingUrl    = $shipment['tracking_url'] ?? null;
                $shipmentId     = $shipment['order_id'] ?? null;
                $estDelivery    = $shipment['estimated_delivery_date'] ?? null;
                $shipStatus     = $shipment['status'] ?? 'pending';

                foreach ($sellerItems as $orderItem) {
                    $orderItem->update([
                        'shipbubble_shipment_id'  => $shipmentId,
                        'courier_id'              => $courierId,
                        'tracking_number'         => $trackingNumber,
                        'tracking_url'            => $trackingUrl,
                        'shipping_status'         => $shipStatus,
                        'estimated_delivery_date' => $estDelivery,
                    ]);
                }

            } catch (\Exception $e) {
                \Log::error("bookShipmentForOrder — failed for seller {$sellerId} on order #{$order->order_number}: " . $e->getMessage());
            }
        }

        session()->forget(['shipbubble_request_tokens', 'shipbubble_request_token']);
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

        try {
            $recipientValidation  = $this->shipbubble->validateAddress([
                'name'    => $request->shipping_name,
                'email'   => auth('web')->user()->email,
                'phone'   => $request->shipping_phone,
                'address' => $request->shipping_address,
                'city'    => $request->shipping_city,
                'state'   => $request->shipping_state,
                'country' => $request->shipping_country,
            ]);
            $recipientAddressCode = $recipientValidation['data']['address_code'] ?? null;

            if (!$recipientAddressCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not validate delivery address. Please provide a more detailed address.',
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Address validation failed.'], 422);
        }

        $sellerGroups = [];
        foreach ($cartItems as $item) {
            $product  = $item->product;
            if (!$product) continue;
            $sellerId = $product->seller_id ?? 'default';

            if (!isset($sellerGroups[$sellerId])) {
                $sellerGroups[$sellerId] = [
                    'seller'      => $product->seller,
                    'totalWeight' => 0,
                    'subtotal'    => 0,
                    'itemName'    => $product->name,
                ];
            }
            $sellerGroups[$sellerId]['totalWeight'] += ($product->weight_kg ?? 0.5) * $item->quantity;
            $sellerGroups[$sellerId]['subtotal']    += $item->price * $item->quantity;
        }

        $allSellerRates   = [];
        $requestTokensMap = [];

        foreach ($sellerGroups as $sellerId => $group) {
            $weight   = max($group['totalWeight'], 0.5);
            $subtotal = $group['subtotal'];
            $seller   = $group['seller'];

            try {
                if ($seller && $seller->address_code) {
                    $senderAddressCode = $seller->address_code;
                } else {
                    $fallback          = $this->shipbubble->validateAddress([
                        'name'    => $seller->business_name ?? 'Orderer Fulfillment',
                        'email'   => config('mail.from.address'),
                        'phone'   => '08000000000',
                        'address' => 'Orderer Fulfillment Center, Lagos',
                        'city'    => 'Lagos',
                        'state'   => 'Lagos',
                        'country' => 'NG',
                    ]);
                    $senderAddressCode = $fallback['data']['address_code'] ?? null;
                }

                if (!$senderAddressCode) continue;

                $rates    = $this->shipbubble->getRates([
                    'sender_address_code'   => $senderAddressCode,
                    'reciever_address_code' => $recipientAddressCode,
                    'weight'                => $weight,
                    'value'                 => max($subtotal, 10),
                    'length'                => 20,
                    'width'                 => 20,
                    'height'                => 20,
                    'category_id'           => 2178251,
                    'item_name'             => $group['itemName'],
                ]);

                $rateData = is_array($rates) && isset($rates['data']) ? $rates['data'] : $rates;

                if (!empty($rateData['request_token'])) {
                    $requestTokensMap[(string) $sellerId] = $rateData['request_token'];
                }

                $allSellerRates[] = [
                    'seller_id'   => (string) $sellerId,
                    'seller_name' => $seller->business_name ?? 'Seller',
                    'subtotal'    => $subtotal,
                    'weight'      => $weight,
                    'couriers'    => $rateData['couriers'] ?? [],
                ];

            } catch (\Exception $e) {
                \Log::error("getRates failed for seller {$sellerId}: " . $e->getMessage());
            }
        }

        if (empty($allSellerRates)) {
            return response()->json([
                'success' => false,
                'message' => 'Could not find courier rates. Please check your address and try again.',
            ], 422);
        }

        session(['shipbubble_request_tokens' => $requestTokensMap]);

        return response()->json([
            'success'      => true,
            'multi_seller' => count($sellerGroups) > 1,
            'seller_rates' => $allSellerRates,
        ]);
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

                $order = Order::where('payment_reference', $reference)->with(['items.seller'])->first();

                if ($order) {
                    $order->update(['payment_status' => 'paid']);

                    foreach ($order->items as $orderItem) {
                            $product = $orderItem->orderable; // Since it's a product
                            $quantity = $orderItem->quantity;
                            
                            $flashSale = \App\Models\FlashSale::where('product_id', $product->id)
                                ->where('is_active', true)
                                ->where('starts_at', '<=', now())
                                ->where('ends_at', '>=', now())
                                ->first();
                            
                            if ($flashSale) {
                                $flashSale->increment('quantity_sold', $quantity);
                            }
                        }
                    $this->walletService->holdEscrow($order);

                    $user = auth('web')->user() ?? $order->user;
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
            $order->load('items.seller');
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

    protected function buildSenderPayload(?\App\Models\Seller $seller): array
    {
        return [
            'name'    => $seller->business_name ?? config('app.name'),
            'email'   => $seller->email         ?? config('mail.from.address'),
            'phone'   => $seller->phone         ?? '08000000000',
            'address' => $seller->address       ?? 'Orderer Fulfillment Center, Lagos',
            'city'    => $seller->city          ?? 'Lagos',
            'state'   => $seller->state         ?? 'Lagos',
            'country' => 'NG',
        ];
    }

    protected function buildRecipientPayload(Order $order, $user): array
    {
        return [
            'name'    => $order->shipping_name,
            'email'   => $user->email ?? '',
            'phone'   => $order->shipping_phone,
            'address' => $order->shipping_address,
            'city'    => $order->shipping_city,
            'state'   => $order->shipping_state,
            'country' => $order->shipping_country ?? 'NG',
        ];
    }

    protected function buildParcelPayload($items, float $weight, float $value, string $orderNumber): array
    {
        return [
            'weight' => $weight,
            'length' => 20,
            'width'  => 20,
            'height' => 20,
            'items'  => $items->map(fn($i) => [
                'name'     => $i->item_name,
                'quantity' => $i->quantity,
                'value'    => max((float) $i->unit_price, 10),
            ])->toArray(),
        ];
    }
}