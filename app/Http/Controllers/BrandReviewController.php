<?php
// =====================================================
// app/Http/Controllers/BrandReviewController.php
// =====================================================
namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandReview;
use Illuminate\Http\Request;

class BrandReviewController extends Controller
{
    public function store(Request $request, Brand $brand)
    {
        if (!auth('web')->check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        // One review per user per brand
        BrandReview::updateOrCreate(
            [
                'brand_id' => $brand->id,
                'user_id'  => auth('web')->id(),
            ],
            [
                'rating'     => $request->rating,
                'review'     => $request->review,
                'is_visible' => true,
            ]
        );

        // Recalculate average
        $avg = BrandReview::where('brand_id', $brand->id)
                          ->where('is_visible', true)
                          ->avg('rating');

        $total = BrandReview::where('brand_id', $brand->id)
                             ->where('is_visible', true)
                             ->count();

        $brand->update([
            'average_rating' => round($avg, 2),
            'total_reviews'  => $total,
        ]);

        return back()->with('success', 'Review submitted. Thank you!');
    }
}
