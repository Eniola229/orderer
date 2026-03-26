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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        protected WalletService    $walletService,
        protected KorapayService   $korapay,
        protected BrevoMailService $brevo
    ) {}

    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')
                ->with('info', 'Your cart is empty.');
        }

        $cartItems = [];
        $subtotal  = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if (!$product || $product->status !== 'approved') continue;

            $cartItems[] = [
                'id'       => $productId,
                'name'     => $item['name'],
                'price'    => $item['price'],
                'quantity' => $item['quantity'],
                'image'    => $item['image'] ?? null,
            ];

            $subtotal += $item['price'] * $item['quantity'];
        }

        return view('storefront.checkout', compact('cartItems', 'subtotal'));
    }

    public function place(Request $request)
    {
        $request->validate([
            'shipping_name'    => ['required', 'string', 'max:200'],
            'shipping_phone'   => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string'],
            'shipping_city'    => ['required', 'string', 'max:100'],
            'shipping_state'   => ['required', 'string', 'max:100'],
            'shipping_country' => ['required', 'string', 'max:5'],
            'payment_method'   => ['required', 'in:wallet,korapay,mixed'],
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Cart is empty.');
        }

        $user     = auth('web')->user();
        $subtotal = 0;
        $items    = [];

        foreach ($cart as $productId => $cartItem) {
            $product = Product::with('category')->find($productId);
            if (!$product || $product->status !== 'approved') continue;

            $totalPrice = $cartItem['price'] * $cartItem['quantity'];
            $subtotal  += $totalPrice;
            $items[]    = ['product' => $product, 'cartItem' => $cartItem, 'total' => $totalPrice];
        }

        // Validate wallet balance if paying with wallet
        if ($request->payment_method === 'wallet') {
            if ($user->wallet_balance < $subtotal) {
                return back()->with('error', 'Insufficient wallet balance.');
            }
        }

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'order_number'    => 'ORD-' . strtoupper(Str::random(10)),
                'user_id'         => $user->id,
                'subtotal'        => $subtotal,
                'shipping_fee'    => 0,
                'total'           => $subtotal,
                'payment_method'  => $request->payment_method,
                'payment_status'  => $request->payment_method === 'wallet' ? 'paid' : 'pending',
                'status'          => 'pending',
                'shipping_name'   => $request->shipping_name,
                'shipping_phone'  => $request->shipping_phone,
                'shipping_address'=> $request->shipping_address,
                'shipping_city'   => $request->shipping_city,
                'shipping_state'  => $request->shipping_state,
                'shipping_country'=> $request->shipping_country,
                'shipping_zip'    => $request->shipping_zip,
                'notes'           => $request->notes,
            ]);

            // Create order items
            foreach ($items as $item) {
                $product         = $item['product'];
                $cartItem        = $item['cartItem'];
                $commissionRate  = $product->category->commission_rate ?? 10;
                $commissionAmt   = round($item['total'] * ($commissionRate / 100), 2);
                $sellerEarnings  = $item['total'] - $commissionAmt;

                // Get primary image
                $primaryImg = $product->images->where('is_primary', true)->first()
                              ?? $product->images->first();

                OrderItem::create([
                    'order_id'         => $order->id,
                    'seller_id'        => $product->seller_id,
                    'orderable_type'   => 'App\Models\Product',
                    'orderable_id'     => $product->id,
                    'item_name'        => $product->name,
                    'item_image'       => $primaryImg->image_url ?? null,
                    'unit_price'       => $cartItem['price'],
                    'quantity'         => $cartItem['quantity'],
                    'total_price'      => $item['total'],
                    'commission_rate'  => $commissionRate,
                    'commission_amount'=> $commissionAmt,
                    'seller_earnings'  => $sellerEarnings,
                    'status'           => 'pending',
                ]);

                // Decrement stock
                $product->decrement('stock', $cartItem['quantity']);
            }

            // Log status
            OrderStatusLog::create([
                'order_id'        => $order->id,
                'from_status'     => null,
                'to_status'       => 'pending',
                'changed_by_type' => 'buyer',
                'changed_by_id'   => $user->id,
                'note'            => 'Order placed.',
            ]);

            // Process payment
            if ($request->payment_method === 'wallet') {
                // Deduct from wallet
                $this->walletService->debit(
                    $user,
                    $subtotal,
                    'debit',
                    "Payment for order #{$order->order_number}",
                    'order',
                    $order->id
                );

                // Hold in escrow
                $this->walletService->holdEscrow($order);

            } elseif ($request->payment_method === 'korapay') {
                // Initiate Korapay checkout
                $reference = $this->korapay->generateReference('ORD');

                $order->update(['payment_reference' => $reference]);

                $this->korapay->createTransaction(
                    $user, $subtotal, 'order_payment', $reference
                );

                DB::commit();
                session()->forget('cart');

                // Redirect to Korapay
                $checkoutData = $this->korapay->initializeCheckout(
                    $user->email,
                    $subtotal,
                    $reference,
                    route('checkout.callback'),
                    ['order_id' => $order->id, 'type' => 'order_payment']
                );

                return redirect($checkoutData['checkout_url']);
            }

            DB::commit();
            session()->forget('cart');

            // Send emails
            $this->brevo->sendOrderPlacedBuyer($user, $order);

            // Notify each seller
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

            return redirect()->route('buyer.orders.show', $order->id)
                ->with('success', "Order #{$order->order_number} placed successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('buyer.orders')
                ->with('error', 'Invalid payment reference.');
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

                // Find the order
                $order = Order::where('payment_reference', $reference)->first();

                if ($order) {
                    $order->update(['payment_status' => 'paid']);

                    // Hold in escrow
                    $this->walletService->holdEscrow($order);

                    $user = auth('web')->user();
                    $this->brevo->sendOrderPlacedBuyer($user, $order);

                    return redirect()->route('buyer.orders.show', $order->id)
                        ->with('success', "Payment successful! Order #{$order->order_number} confirmed.");
                }
            }

            return redirect()->route('buyer.orders')
                ->with('error', 'Payment verification failed.');

        } catch (\Exception $e) {
            return redirect()->route('buyer.orders')
                ->with('error', 'Payment callback error. Contact support.');
        }
    }
}
