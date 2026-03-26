<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('user_id', auth('web')->id())
            ->where('wishlistable_type', 'App\Models\Product')
            ->with(['wishlistable.images', 'wishlistable.category'])
            ->latest()
            ->paginate(16);

        return view('buyer.wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $user    = auth('web')->user();
        $product = Product::findOrFail($request->product_id);

        $existing = Wishlist::where('user_id', $user->id)
            ->where('wishlistable_type', 'App\Models\Product')
            ->where('wishlistable_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['added' => false, 'message' => 'Removed from wishlist']);
        }

        Wishlist::create([
            'user_id'           => $user->id,
            'wishlistable_type' => 'App\Models\Product',
            'wishlistable_id'   => $product->id,
            'price_at_save'     => $product->sale_price ?? $product->price,
        ]);

        return response()->json(['added' => true, 'message' => 'Added to wishlist']);
    }

    public function remove(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== auth('web')->id()) abort(403);
        $wishlist->delete();
        return back()->with('success', 'Removed from wishlist.');
    }
}
