<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;

class SearchSuggestController extends Controller
{
    /**
     * Return live search suggestions as JSON.
     * Called via: GET /search/suggestions?q=keyword
     */
    public function __invoke(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Products — name + primary image + price
        $products = Product::where('status', 'approved')
            ->where('name', 'like', "%{$q}%")
            ->with(['images' => fn($i) => $i->where('is_primary', true)->limit(1)])
            ->orderByDesc('total_sold')
            ->limit(6)
            ->get()
            ->map(fn($p) => [
                'type'  => 'product',
                'label' => $p->name,
                'url'   => route('product.show', $p->slug),
                'image' => optional($p->images->first())->image_url,
                'price' => '₦' . number_format($p->sale_price ?? $p->price, 2),
            ]);

        // Brands — name + logo
        $brands = Brand::where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->limit(3)
            ->get()
            ->map(fn($b) => [
                'type'  => 'brand',
                'label' => $b->name,
                'url'   => route('brands.show', $b->slug),
                'image' => $b->logo,
                'price' => null,
            ]);

        // Merge: products first, then brands
        $results = $products->concat($brands)->values();

        return response()->json($results);
    }
}