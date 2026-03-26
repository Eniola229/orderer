<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('status', 'approved')
            ->firstOrFail();

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $newQty = $cart[$product->id]['quantity'] + $request->quantity;
            $cart[$product->id]['quantity'] = min($newQty, $product->stock);
        } else {
            $primaryImg = $product->images->where('is_primary', true)->first()
                          ?? $product->images->first();
            $cart[$product->id] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $product->sale_price ?? $product->price,
                'quantity' => min($request->quantity, $product->stock),
                'image'    => $primaryImg->image_url ?? null,
                'seller_id'=> $product->seller_id,
            ];
        }

        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count'   => count($cart),
                'message' => 'Added to cart',
            ]);
        }

        return back()->with('success', 'Added to cart!');
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => ['required']]);

        $cart = session()->get('cart', []);
        unset($cart[$request->product_id]);
        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'count' => count($cart)]);
        }

        return back()->with('success', 'Removed from cart.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => ['required'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return response()->json(['success' => true]);
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared.');
    }
}
