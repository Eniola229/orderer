<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        if (!auth('web')->check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = auth('web')->user();

        // Check if already reviewed
        if (ProductReview::where('user_id', $user->id)->where('product_id', $product->id)->exists()) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Check if verified purchase
        $isVerified = OrderItem::where('seller_id', $product->seller_id)
            ->whereHas('order', fn($q) => $q->where('user_id', $user->id)->where('status', 'completed'))
            ->where('orderable_id', $product->id)
            ->exists();

        ProductReview::create([
            'product_id'           => $product->id,
            'user_id'              => $user->id,
            'rating'               => $request->rating,
            'review'               => $request->review,
            'is_verified_purchase' => $isVerified,
            'is_visible'           => true,
        ]);

        // Recalculate product average rating
        $avg   = ProductReview::where('product_id', $product->id)->where('is_visible', true)->avg('rating');
        $total = ProductReview::where('product_id', $product->id)->where('is_visible', true)->count();

        $product->update([
            'average_rating' => round($avg, 2),
            'total_reviews'  => $total,
        ]);

        return back()->with('success', 'Review submitted. Thank you!');
    }
}