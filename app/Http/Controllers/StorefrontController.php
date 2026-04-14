<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\FlashSale;
use App\Models\Wishlist;
use App\Models\HouseListing;
use App\Models\ServiceListing;
use App\Models\ProductReview;
use App\Models\NewsletterSubscriber;
use App\Helpers\AdHelper;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{ 
    public function home()
    {
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = Product::where('status', 'approved')
            ->where('is_featured', true)
            ->with(['images', 'seller'])
            ->latest()
            ->take(8)
            ->get();

        $newArrivals = Product::where('status', 'approved')
            ->with(['images', 'seller'])
            ->latest()
            ->take(8)
            ->get();

        $flashSales = FlashSale::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->with(['product.images', 'product.seller'])
            ->take(4)
            ->get();

        $brands = Brand::where('is_active', true)
            ->whereNotNull('logo')
            ->take(6)
            ->get();

        // ── Ads ──────────────────────────────────────────────────
        $heroBannerAds  = AdHelper::forSlot('homepage_hero', 5);   // rotating hero
        $topListingAds  = AdHelper::topListings(4);                 // sponsored products

        // Record impressions for hero ads (they are always visible)
        foreach ($heroBannerAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        return view('storefront.home', compact(
            'categories', 'featuredProducts', 'newArrivals',
            'flashSales', 'brands',
            'heroBannerAds', 'topListingAds'
        ));
    }

    public function shop(Request $request)
    {
        $allCategories = Category::where('is_active', true)
            ->withCount('products')
            ->with('subcategories')
            ->get();

        $currentCategory = null;
        $brands = Brand::where('is_active', true)->take(10)->get();

        $query = Product::where('status', 'approved')
            ->with(['images', 'seller', 'category']);

        // Filters
        if ($request->min_price) $query->where('price', '>=', $request->min_price);
        if ($request->max_price) $query->where('price', '<=', $request->max_price);
        if ($request->condition) $query->whereIn('condition', (array) $request->condition);
        if ($request->q) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Sort
        switch ($request->sort) {
            case 'price_asc':  $query->orderBy('price', 'asc');  break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'rating':     $query->orderBy('average_rating', 'desc'); break;
            case 'popular':    $query->orderBy('total_sold', 'desc'); break;
            default:           $query->latest(); break;
        }

        $products = $query->paginate(12)->withQueryString();

        // ── Ads ──────────────────────────────────────────────────
        // Search results banner (shows on shop/search pages)
        $searchBannerAds = AdHelper::forSlot('search_results', 1);
        // Top listing sponsored products
        $topListingAds   = AdHelper::topListings(4);

        foreach ($searchBannerAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }
        foreach ($topListingAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        return view('storefront.shop', compact(
            'products', 'allCategories', 'currentCategory', 'brands',
            'searchBannerAds', 'topListingAds'
        ));
    }

    public function shopCategory(Request $request, string $categorySlug)
    {
        $allCategories   = Category::where('is_active', true)->withCount('products')->with('subcategories')->get();
        $currentCategory = Category::where('slug', $categorySlug)->where('is_active', true)->firstOrFail();
        $brands          = Brand::where('is_active', true)->take(10)->get();

        $query = Product::where('status', 'approved')
            ->where('category_id', $currentCategory->id)
            ->with(['images', 'seller', 'category']);

        if ($request->min_price)  $query->where('price', '>=', $request->min_price);
        if ($request->max_price)  $query->where('price', '<=', $request->max_price);
        if ($request->condition)  $query->whereIn('condition', (array) $request->condition);

        switch ($request->sort) {
            case 'price_asc':  $query->orderBy('price', 'asc');  break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'rating':     $query->orderBy('average_rating', 'desc'); break;
            case 'popular':    $query->orderBy('total_sold', 'desc'); break;
            default:           $query->latest(); break;
        }

        $products = $query->paginate(12)->withQueryString();

        // ── Ads ──────────────────────────────────────────────────
        $categoryBannerAds = AdHelper::forSlot('category_page', 1);
        $topListingAds     = AdHelper::topListings(4, $currentCategory->id);

        foreach ($categoryBannerAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }
        foreach ($topListingAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        return view('storefront.shop', compact(
            'products', 'allCategories', 'currentCategory', 'brands',
            'categoryBannerAds', 'topListingAds'
        ));
    }

    public function product(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'approved')
            ->with(['images', 'videos', 'seller', 'category', 'subcategory', 'reviews.user'])
            ->firstOrFail();

        // Increment views
        $product->increment('views');

        $relatedProducts = Product::where('status', 'approved')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['images', 'seller'])
            ->take(4)
            ->get();

        $inWishlist = false;
        if (auth('web')->check()) {
            $inWishlist = Wishlist::where('user_id', auth('web')->id())
                ->where('wishlistable_type', 'App\Models\Product')
                ->where('wishlistable_id', $product->id)
                ->exists();
        }

        // ── Ads ──────────────────────────────────────────────────
        $sidebarAds = AdHelper::forSlot('product_page_sidebar', 2);

            // ── Flash sale check ─────────────────────────────────────────────────
        $flashSale = \App\Models\FlashSale::where('product_id', $product->id)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->where(function ($q) {
                $q->whereNull('quantity_limit')
                  ->orWhereColumn('quantity_sold', '<', 'quantity_limit');
            })
            ->first();
        // ────────────────────────────────────────────────────────────────────


        foreach ($sidebarAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        return view('storefront.product', compact(
            'product', 'relatedProducts', 'inWishlist',
            'sidebarAds', 'flashSale'
        ));
    }

    public function search(Request $request)
    {
        return $this->shop($request);
    }

    public function brands()
    {
        $query = Brand::where('is_active', true)->with('seller');

        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        $brands = $query->paginate(20);

        return view('storefront.brands', compact('brands'));
    }

    public function brandShow(string $slug)
    {
        $brand    = Brand::where('slug', $slug)->where('is_active', true)->with(['seller', 'reviews.user'])->firstOrFail();
        $products = Product::where('seller_id', $brand->seller_id)
            ->where('status', 'approved')
            ->with('images', 'seller')
            ->paginate(12, ['*'], 'products_page');
        $services = ServiceListing::where('seller_id', $brand->seller_id)
            ->where('status', 'approved')
            ->with('seller')
            ->paginate(12, ['*'], 'services_page');
        $houses = HouseListing::where('seller_id', $brand->seller_id)
            ->where('status', 'approved')
            ->with('images', 'seller')
            ->paginate(12, ['*'], 'houses_page');
        return view('storefront.brand-show', compact('brand', 'products', 'services', 'houses'));
    }

    public function services(Request $request)
    {
        $query = \App\Models\ServiceListing::where('status', 'approved')
            ->with(['seller', 'category']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('pricing_type')) {
            $query->where('pricing_type', $request->pricing_type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('delivery_time')) {
            switch($request->delivery_time) {
                case '24h':
                    $query->where('delivery_time', '<=', '24 hours');
                    break;
                case '3days':
                    $query->where('delivery_time', 'LIKE', '%3 days%')
                          ->orWhere('delivery_time', 'LIKE', '%1-3 days%');
                    break;
                case 'week':
                    $query->where('delivery_time', 'LIKE', '%1 week%');
                    break;
                case '2weeks':
                    $query->where('delivery_time', 'LIKE', '%2 week%')
                          ->orWhere('delivery_time', '>', '1 week');
                    break;
            }
        }

        if ($request->filled('rating')) {
            $query->where('average_rating', '>=', $request->rating);
        }

        // Sorting
        switch($request->get('sort', 'latest')) {
            case 'price_asc':  $query->orderBy('price', 'asc');  break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'rating':     $query->orderBy('average_rating', 'desc'); break;
            case 'oldest':     $query->orderBy('created_at', 'asc'); break;
            default:           $query->orderBy('created_at', 'desc');
        }

        $perPage  = $request->get('per_page', 12);
        $services = $query->paginate($perPage);

        // Get categories that have approved services
        $categoryIds = \App\Models\ServiceListing::where('status', 'approved')
                        ->whereNotNull('category_id')
                        ->distinct()
                        ->pluck('category_id');

        $categories = \App\Models\Category::whereIn('id', $categoryIds)->get();

        return view('storefront.services', compact('services', 'categories'));
    }

    public function serviceShow($slug)
    {
        $service = \App\Models\ServiceListing::where('slug', $slug)
                            ->where('status', 'approved')
                            ->with(['seller', 'category'])
                            ->firstOrFail();

        $service->increment('views');

        return view('storefront.services-show', compact('service'));
    }

    public function houses(Request $request)
    {
        $query = HouseListing::where('status', 'approved');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('state', 'LIKE', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('listing_type'))  $query->where('listing_type', $request->listing_type);
        if ($request->filled('property_type')) $query->where('property_type', $request->property_type);
        if ($request->filled('min_price'))     $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price'))     $query->where('price', '<=', $request->max_price);
        if ($request->filled('bedrooms'))      $query->where('bedrooms', '>=', $request->bedrooms);
        if ($request->filled('bathrooms'))     $query->where('bathrooms', '>=', $request->bathrooms);
        if ($request->filled('city'))          $query->where('city', 'LIKE', "%{$request->city}%");
        if ($request->filled('state'))         $query->where('state', 'LIKE', "%{$request->state}%");

        // Sorting
        switch($request->get('sort', 'latest')) {
            case 'price_asc':  $query->orderBy('price', 'asc');  break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'oldest':     $query->orderBy('created_at', 'asc'); break;
            default:           $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->get('per_page', 12);
        $houses  = $query->paginate($perPage);

        return view('storefront.houses', compact('houses'));
    }

    public function housesshow($slug)
    {
        $house = HouseListing::where('slug', $slug)
                            ->where('status', 'approved')
                            ->with(['seller', 'images'])
                            ->firstOrFail();

        $similarHouses = HouseListing::where('status', 'approved')
            ->where('id', '!=', $house->id)
            ->where(function($query) use ($house) {
                $query->where('listing_type', $house->listing_type)
                      ->orWhere('property_type', $house->property_type)
                      ->orWhere('city', $house->city);
            })
            ->take(3)
            ->get();

        return view('storefront.houses-details', compact('house', 'similarHouses'));
    }

    public function contact()
    {
        return view('storefront.contact');
    }

    public function newsletterSubscribe(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            NewsletterSubscriber::firstOrCreate(
                ['email' => $request->email],
                ['subscribed_at' => now()]
            );

            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    public function submitReview(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);
        
        // Check if user has purchased this product
        $hasPurchased = \App\Models\OrderItem::whereHas('order', function($q) {
            $q->where('user_id', auth()->id())
              ->whereIn('payment_status', ['paid', 'completed'])
              ->where('status', '!=', 'cancelled');
        })->where('orderable_id', $product->id)
          ->where('orderable_type', 'App\Models\Product')
          ->exists();
        
        if (!$hasPurchased) {
            return back()->with('error', 'You can only review products you have purchased.');
        }
        
        // Check if already reviewed
        $hasReviewed = \App\Models\ProductReview::where('product_id', $product->id)
            ->where('user_id', auth()->id())
            ->exists();
        
        if ($hasReviewed) {
            return back()->with('error', 'You have already reviewed this product.');
        }
        
        // Handle image uploads to Cloudinary
        $imagePaths = [];
        if ($request->hasFile('images')) {
            $cloudinary = app(\App\Services\CloudinaryService::class);
            foreach ($request->file('images') as $index => $image) {
                $uploaded = $cloudinary->uploadImage(
                    $image,
                    'orderer/reviews/' . $product->id . '/' . auth()->id()
                );
                $imagePaths[] = $uploaded['url']; // Store only the URL
            }
        }
        
        // Create review
        \App\Models\ProductReview::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'review' => $request->review,
            'images' => $imagePaths, // This will be automatically JSON encoded
            'is_verified_purchase' => true,
            'is_visible' => true,
        ]);
        
        // Update product average rating
        $avg = \App\Models\ProductReview::where('product_id', $product->id)
            ->where('is_visible', true)
            ->avg('rating');
        $product->average_rating = round($avg, 1);
        $product->total_reviews = \App\Models\ProductReview::where('product_id', $product->id)
            ->where('is_visible', true)
            ->count();
        $product->save();
        
        return back()->with('success', 'Thank you for your review!');
    }
}
