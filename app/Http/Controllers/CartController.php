<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // ── Get or create the cart for this visitor ────────────────

    private function getCart(): Cart
    {
        if (auth('web')->check()) {
            // Logged-in user: find by user_id, merge session cart if any
            $cart = Cart::firstOrCreate(
                ['user_id' => auth('web')->id()],
                ['session_id' => null]
            );

            // If they had a guest cart, merge it in then delete it
            $sessionId = session()->getId();
            $guestCart = Cart::where('session_id', $sessionId)
                             ->whereNull('user_id')
                             ->first();

            if ($guestCart && $guestCart->id !== $cart->id) {
                foreach ($guestCart->items as $guestItem) {
                    $existing = $cart->items()->where('product_id', $guestItem->product_id)->first();
                    if ($existing) {
                        $existing->update([
                            'quantity' => min(
                                $existing->quantity + $guestItem->quantity,
                                $guestItem->product->stock ?? 9999
                            )
                        ]);
                    } else {
                        $cart->items()->create([
                            'product_id' => $guestItem->product_id,
                            'quantity'   => $guestItem->quantity,
                            'price'      => $guestItem->price,
                        ]);
                    }
                }
                $guestCart->delete();
            }

            return $cart;
        }

        // Guest: find by session_id
        $sessionId = session()->getId();
        return Cart::firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => null]
        );
    }

    // ── Add ───────────────────────────────────────────────────
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);

        // ── Flash sale price check ───────────────────────────────────────────
        $price = $product->sale_price ?? $product->price;

        if ($request->boolean('flash_sale')) {
            $flashSale = \App\Models\FlashSale::where('product_id', $product->id)
                ->where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now())
                ->where(function ($q) {
                    $q->whereNull('quantity_limit')
                      ->orWhereColumn('quantity_sold', '<', 'quantity_limit');
                })
                ->first();

            if ($flashSale) {
                $price = $flashSale->sale_price;
            }
        }
        // ────────────────────────────────────────────────────────────────────

        $cart = $this->getCart();

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $newQty = $item->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return response()->json(['success' => false, 'message' => 'Not enough stock.']);
            }
            $item->update(['quantity' => $newQty, 'price' => $price]);
        } else {
            if ($request->quantity > $product->stock) {
                return response()->json(['success' => false, 'message' => 'Not enough stock.']);
            }
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $price,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Added to cart.']);
    }

    // ── Remove ────────────────────────────────────────────────

    public function remove(Request $request)
    {
        $request->validate(['product_id' => ['required']]);

        $cart = $this->getCart();
        $cart->items()->where('product_id', $request->product_id)->delete();

        $count = $cart->items()->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'count' => $count]);
        }

        return back()->with('success', 'Removed from cart.');
    }

    // ── Update ────────────────────────────────────────────────

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => ['required'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->getCart();
        $cart->items()
             ->where('product_id', $request->product_id)
             ->update(['quantity' => $request->quantity]);

        return response()->json(['success' => true]);
    }

    // ── Clear ─────────────────────────────────────────────────

    public function clear()
    {
        $this->getCart()->items()->delete();
        return back()->with('success', 'Cart cleared.');
    }

    // ── View (optional, for cart page) ────────────────────────

    public function index()
    {
        $cart  = $this->getCart();
        $items = $cart->items()->with(['product.images'])->get();

        return view('storefront.cart', compact('cart', 'items'));
    }

    // ── Cart count (for header badges) ───────────────────────────
    public function count()
    {
        $cart  = $this->getCart();
        $count = $cart->items()->sum('quantity');

        return response()->json(['count' => $count]);
    }

    // ── Cart sidebar data ─────────────────────────────────────────
    public function sidebar()
    {
        $cart  = $this->getCart();
        $items = $cart->items()->with(['product.images'])->get();

        $subtotal = $items->sum(fn($item) => $item->price * $item->quantity);

        $data = $items->map(function ($item) {
            $product     = $item->product;
            $primaryImg  = $product?->images->where('is_primary', true)->first()
                           ?? $product?->images->first();

            return [
                'product_id' => $item->product_id,
                'name'       => $product?->name ?? 'Product',
                'price'      => (float) $item->price,
                'quantity'   => $item->quantity,
                'subtotal'   => (float) ($item->price * $item->quantity),
                'image'      => $primaryImg?->image_url ?? null,
            ];
        });

        return response()->json([
            'items'    => $data,
            'subtotal' => $subtotal,
            'total'    => $subtotal, // extend later for shipping/discounts
            'count'    => $items->sum('quantity'),
        ]);
    }

    /**
     * Merge the current session's guest cart into the logged-in user's cart.
     * Safe to call even if there's no guest cart — it does nothing in that case.
     */
    public function mergeGuestCart(string $sessionId): void
    {
        if (!auth('web')->check()) {
            return;
        }

        $guestCart = Cart::where('session_id', $sessionId)
                         ->whereNull('user_id')
                         ->first();

       
        if (!$guestCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(
            ['user_id' => auth('web')->id()],
            ['session_id' => null]
        );

        if ($guestCart->id === $userCart->id) {
            return;
        }

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()
                                 ->where('product_id', $guestItem->product_id)
                                 ->first();

            if ($existing) {
                $existing->update([
                    'quantity' => min(
                        $existing->quantity + $guestItem->quantity,
                        $guestItem->product->stock ?? 9999
                    ),
                ]);
            } else {
                $userCart->items()->create([
                    'product_id' => $guestItem->product_id,
                    'quantity'   => $guestItem->quantity,
                    'price'      => $guestItem->price,
                ]);
            }
        }

        $guestCart->delete();
    }
}