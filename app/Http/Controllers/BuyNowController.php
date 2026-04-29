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

class BuyNowController extends Controller
{
    // ─── Session key we store the Buy Now item under ───────────────────────────
    const SESSION_KEY = 'buy_now_item';

    public function __construct(
        protected WalletService     $walletService,
        protected KorapayService    $korapay,
        protected BrevoMailService  $brevo,
        protected ShipbubbleService $shipbubble
    ) {}

    // ─── STEP 1 ─────────────────────────────────────────────────────────────────
    public function initiate(Request $request)
    {
        $request->validate([
            'product_id'       => ['required', 'exists:products,id'],
            'quantity'         => ['sometimes', 'integer', 'min:1', 'max:100'],
            'selected_options' => ['sometimes', 'array'],
        ]);

        if (! auth('web')->check()) {
            session(['url.intended' => back()->getTargetUrl()]);
            return redirect()->route('login')->with('info', 'Please log in to buy.');
        }

        $product = Product::with(['images', 'category', 'seller'])->findOrFail($request->product_id);

        if ($product->status !== 'approved') {
            return back()->with('error', 'This product is not available right now.');
        }

        $quantity = max(1, (int) ($request->quantity ?? 1));

        if ($product->stock < $quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        //keep the flash sale detection for price
        $flashSale = \App\Models\FlashSale::where('product_id', $product->id)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        // Use flash sale price if available
        $price = $flashSale ? (float) $flashSale->sale_price : (float) ($product->sale_price ?? $product->price);

        session([
            self::SESSION_KEY => [
                'product_id'       => $product->id,
                'quantity'         => $quantity,
                'price'            => $price,  // This is the price the user pays
                'selected_options' => $request->selected_options ?? [],
            ],
        ]);

        return redirect()->route('buy-now.checkout');
    }

    // ─── STEP 2 ─────────────────────────────────────────────────────────────────
    public function checkout()
    {
        $sessionItem = session(self::SESSION_KEY);

        if (! $sessionItem) {
            return redirect()->route('shop.index')->with('error', 'No item selected for purchase.');
        }

        $product = Product::with(['images', 'category', 'seller'])->find($sessionItem['product_id']);

        if (! $product || $product->status !== 'approved') {
            session()->forget(self::SESSION_KEY);
            return redirect()->route('shop.index')->with('error', 'Product is no longer available.');
        }

        $img = $product->images->where('is_primary', true)->first() ?? $product->images->first();

        // GET THE REGULAR PRICE FOR COMPARISON (LIKE CHECKOUT CONTROLLER)
        $regularPrice = (float) ($product->sale_price ?? $product->price);
        $flashSalePrice = (float) $sessionItem['price'];
        
        // DETERMINE IF THIS IS A FLASH SALE BY COMPARING PRICES
        $isFlashSale = $flashSalePrice < $regularPrice;

        $cartItems = [[
            'id'               => $product->id,
            'name'             => $product->name,
            'price'            => $flashSalePrice,
            'regular_price'    => $regularPrice,  // ADD REGULAR PRICE FOR VIEW
            'quantity'         => $sessionItem['quantity'],
            'image'            => $img?->image_url ?? null,
            'is_flash_sale'    => $isFlashSale,   // USE COMPARISON RESULT
            'selected_options' => $sessionItem['selected_options'] ?? [],
        ]];

        $subtotal = $sessionItem['price'] * $sessionItem['quantity'];
        $isBuyNow = true;

        return view('storefront.checkout', compact('cartItems', 'subtotal', 'isBuyNow'));
    }

    // ─── STEP 3 ─────────────────────────────────────────────────────────────────
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

        $sessionItem = session(self::SESSION_KEY);

        if (! $sessionItem) {
            return redirect()->route('shop.index')->with('error', 'Session expired. Please try again.');
        }

        $product = Product::with(['images', 'category', 'seller'])->find($sessionItem['product_id']);

        if (! $product || $product->status !== 'approved') {
            session()->forget(self::SESSION_KEY);
            return redirect()->route('shop.index')->with('error', 'Product is no longer available.');
        }

        if ($product->stock < $sessionItem['quantity']) {
            return back()->with('error', 'Sorry, not enough stock is left for this item.');
        }

        $user            = auth('web')->user();
        $quantity        = $sessionItem['quantity'];
        $unitPrice       = (float) $sessionItem['price'];
        $selectedOptions = $sessionItem['selected_options'] ?? [];
        $itemTotal       = $unitPrice * $quantity;
        $weight          = max(($product->weight_kg ?? 0.5) * $quantity, 0.5);

        $shippingFee = (float) $request->shipping_fee;
        $subtotal    = $itemTotal;
        $total       = $subtotal + $shippingFee;

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
                'package_weight'        => $weight,
                'declared_value'        => $subtotal,
                'is_multi_seller'       => false,
                'shipping_rate_data'    => json_decode($request->shipping_rate_data ?? '{}', true),
            ]);

            $commissionRate = $product->category->commission_rate ?? 10;
            $commissionAmt  = round($itemTotal * ($commissionRate / 100), 2);
            $sellerEarnings = $itemTotal - $commissionAmt;
            $primaryImg     = $product->images->where('is_primary', true)->first()
                              ?? $product->images->first();

            OrderItem::create([
                'order_id'          => $order->id,
                'seller_id'         => $product->seller_id,
                'orderable_type'    => 'App\Models\Product',
                'orderable_id'      => $product->id,
                'item_name'         => $product->name,
                'item_image'        => $primaryImg?->image_url ?? null,
                'unit_price'        => $unitPrice,
                'quantity'          => $quantity,
                'total_price'       => $itemTotal,
                'commission_rate'   => $commissionRate,
                'commission_amount' => $commissionAmt,
                'seller_earnings'   => $sellerEarnings,
                'status'            => 'pending',
                'selected_options'  => $selectedOptions,
            ]);

            $product->decrement('stock', $quantity);
            $product->increment('total_sold', $quantity);

            OrderStatusLog::create([
                'order_id'        => $order->id,
                'from_status'     => null,
                'to_status'       => 'pending',
                'changed_by_type' => 'buyer',
                'changed_by_id'   => $user->id,
                'note'            => 'Order placed via Buy Now.',
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

                $flashSale = \App\Models\FlashSale::where('product_id', $product->id)
                    ->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now())
                    ->first();

                if ($flashSale) {
                    $flashSale->increment('quantity_sold', $quantity);
                }

                $this->bookShipment($order, $user, $weight, $subtotal, $product);

                DB::commit();
                session()->forget(self::SESSION_KEY);
                $this->sendOrderEmails($user, $order);

                return redirect()->route('buyer.orders.show', $order->id)
                    ->with('success', "Order #{$order->order_number} placed successfully!");

            } else {
                // MODIFIED: Use buy-now specific reference and callback
                $reference = $this->korapay->generateReference('BNO');
                $order->update(['payment_reference' => $reference]);
                $this->korapay->createTransaction($user, $total, 'order_payment', $reference);

                DB::commit();
                
                // Store order ID in session for callback verification
                session(['buy_now_order_id' => $order->id]);
                session()->forget(self::SESSION_KEY);

                $checkoutData = $this->korapay->initializeCheckout(
                    $user->email,
                    $user->full_name,
                    $total,
                    $reference,
                    route('buy-now.callback'), // USE BUY NOW CALLBACK
                    '',
                    ['order_id' => $order->id, 'type' => 'order_payment']
                );

                return redirect($checkoutData['checkout_url']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('BuyNow place error: ' . $e->getMessage());
            return back()->with('error', 'Order could not be placed. Please try again.');
        }
    }

    // ─── NEW: BUY NOW KORAPAY CALLBACK ─────────────────────────────────────────
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('shop.index')->with('error', 'Invalid payment reference.');
        }

        $txn = KorapayTransaction::where('reference', $reference)->first();
        if (!$txn) {
            return redirect()->route('shop.index')->with('error', 'Transaction not found.');
        }

        try {
            $data = $this->korapay->verifyTransaction($reference);

            if ($data['status'] === 'success') {
                $txn->update([
                    'status'            => 'success',
                    'korapay_reference' => $data['payment_reference'] ?? null,
                    'gateway_response'  => $data,
                ]);

                // Find the order by payment reference
                $order = Order::where('payment_reference', $reference)->with(['items.seller'])->first();

                if ($order) {
                    $order->update(['payment_status' => 'paid']);

                    $orderItem = $order->items->first();
                    $product = $orderItem->orderable;
                    $quantity = $orderItem->quantity;

                    $flashSale = \App\Models\FlashSale::where('product_id', $product->id)
                        ->where('is_active', true)
                        ->where('starts_at', '<=', now())
                        ->where('ends_at', '>=', now())
                        ->first();

                    if ($flashSale) {
                        $flashSale->increment('quantity_sold', $quantity);
                    }

                    $this->walletService->holdEscrow($order);

                    $user = auth('web')->user() ?? $order->user;
                    
                    // Book shipment for the Buy Now order
                    $product = $order->items->first()->orderable;
                    $weight = $order->package_weight ?? 0.5;
                    $subtotal = $order->subtotal;
                    
                    $this->bookShipment($order, $user, $weight, $subtotal, $product);
                    $this->sendOrderEmails($user, $order);

                    // Clear any remaining session data
                    session()->forget(self::SESSION_KEY);
                    session()->forget('buy_now_order_id');

                    return redirect()->route('buyer.orders.show', $order->id)
                        ->with('success', "Payment successful! Order #{$order->order_number} confirmed.");
                } else {
                    return redirect()->route('shop.index')->with('error', 'Order not found for this payment.');
                }
            }

            return redirect()->route('shop.index')->with('error', 'Payment verification failed.');

        } catch (\Exception $e) {
            \Log::error('BuyNow callback error: ' . $e->getMessage());
            return redirect()->route('shop.index')->with('error', 'Payment callback error. Contact support.');
        }
    }

    // ─── AJAX: Get shipping rates for Buy Now item ───────────────────────────────
    // POST /buy-now/rates
    // Works exactly like CheckoutController::getRates() but pulls from session
    // instead of cart. The checkout JS can call this endpoint when $isBuyNow = true.
    // ────────────────────────────────────────────────────────────────────────────
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
 
        $sessionItem = session(self::SESSION_KEY);
 
        if (! $sessionItem) {
            return response()->json(['error' => 'Session expired'], 400);
        }
 
        $product = Product::with(['seller'])->find($sessionItem['product_id']);
 
        if (! $product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
 
        $user   = auth('web')->user();
        $weight = max(($product->weight_kg ?? 0.5) * $sessionItem['quantity'], 0.5);
 
        // Validate recipient address
        try {
            $recipientValidation = $this->shipbubble->validateAddress([
                'name'    => $request->shipping_name,
                'email'   => $user->email,
                'phone'   => $request->shipping_phone,
                'address' => $request->shipping_address,
                'city'    => $request->shipping_city,
                'state'   => $request->shipping_state,
                'country' => $request->shipping_country,
            ]);
 
            $recipientAddressCode = $recipientValidation['data']['address_code'] ?? null;
 
            if (! $recipientAddressCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not validate delivery address. Please provide a more detailed address.',
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Address validation failed.'], 422);
        }
 
        // Validate / get sender address
        try {
            $seller = $product->seller;
 
            if ($seller && $seller->address_code) {
                $senderAddressCode = $seller->address_code;
            } else {
                $fallback = $this->shipbubble->validateAddress([
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
 
            if (! $senderAddressCode) {
                return response()->json(['success' => false, 'message' => 'Could not determine pickup address.'], 422);
            }
 
            $subtotal = $sessionItem['price'] * $sessionItem['quantity'];
 
            $rates    = $this->shipbubble->getRates([
                'sender_address_code'   => $senderAddressCode,
                'reciever_address_code' => $recipientAddressCode,
                'weight'                => $weight,
                'value'                 => max($subtotal, 10),
                'length'                => 20,
                'width'                 => 20,
                'height'                => 20,
                'category_id'           => 2178251,
                'item_name'             => $product->name,
            ]);
 
            $rateData = is_array($rates) && isset($rates['data']) ? $rates['data'] : $rates;
 
            if (! empty($rateData['request_token'])) {
                // Store token under the seller id so bookShipment can find it
                session(['shipbubble_request_tokens' => [
                    (string) $product->seller_id => $rateData['request_token'],
                ]]);
            }
 
            return response()->json([
                'success'      => true,
                'multi_seller' => false,
                'seller_rates' => [[
                    'seller_id'   => (string) $product->seller_id,
                    'seller_name' => $seller->business_name ?? 'Seller',
                    'subtotal'    => $subtotal,
                    'weight'      => $weight,
                    'couriers'    => $rateData['couriers'] ?? [],
                ]],
            ]);
 
        } catch (\Exception $e) {
            \Log::error('BuyNow getRates error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Could not fetch shipping rates. Please try again.',
            ], 422);
        }
    }
 
    // ─── Private helpers (mirrors CheckoutController) ────────────────────────────
 
    protected function bookShipment(Order $order, $user, float $weight, float $declaredValue, Product $product): void
    {
        $requestTokensMap = session('shipbubble_request_tokens', []);
        $token            = $requestTokensMap[(string) $product->seller_id]
                           ?? array_values($requestTokensMap)[0]
                           ?? null;
 
        $rateDataMap = $order->shipping_rate_data ?? [];
        $rateData    = $rateDataMap[(string) $product->seller_id]
                      ?? (array_values($rateDataMap)[0] ?? $rateDataMap);
 
        $courierId   = $rateData['courier_id'] ?? $rateData['id'] ?? null;
        $serviceCode = $rateData['service_code'] ?? $order->shipping_service_code;
 
        if (! $token || ! $courierId) {
            \Log::warning("BuyNow bookShipment — missing token or courier_id for order #{$order->order_number}");
            session()->forget(['shipbubble_request_tokens', 'shipbubble_request_token']);
            return;
        }
 
        try {
            $seller   = $product->seller;
            $shipment = $this->shipbubble->createShipment(
                $serviceCode,
                (string) $courierId,
                $this->buildSenderPayload($seller),
                $this->buildRecipientPayload($order, $user),
                $this->buildParcelPayload($order->items, $weight, $declaredValue, $order->order_number),
                $token
            );
 
            $trackingNumber = $shipment['courier']['tracking_code'] ?? null;
            $trackingUrl    = $shipment['tracking_url'] ?? null;
            $shipmentId     = $shipment['order_id'] ?? null;
            $estDelivery    = $shipment['estimated_delivery_date'] ?? null;
            $shipStatus     = $shipment['status'] ?? 'pending';
 
            $order->items()->update([
                'shipbubble_shipment_id'  => $shipmentId,
                'courier_id'              => $courierId,
                'tracking_number'         => $trackingNumber,
                'tracking_url'            => $trackingUrl,
                'shipping_status'         => $shipStatus,
                'estimated_delivery_date' => $estDelivery,
            ]);
 
        } catch (\Exception $e) {
            \Log::error("BuyNow bookShipment failed for order #{$order->order_number}: " . $e->getMessage());
        }
 
        session()->forget(['shipbubble_request_tokens', 'shipbubble_request_token']);
    }
 
    protected function sendOrderEmails($user, Order $order): void
    {
        try {
            $this->brevo->sendOrderPlacedBuyer($user, $order);
            $order->load('items.seller');
 
            foreach ($order->items->pluck('seller_id')->unique() as $sellerId) {
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
            \Log::error('BuyNow sendOrderEmails failed: ' . $e->getMessage());
        }
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