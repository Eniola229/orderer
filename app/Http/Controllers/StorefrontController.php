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
use App\Models\Ad;

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

        // ── Best Sellers ──────────────────────────────────────────
        $bestSellers = Product::where('status', 'approved')
            ->with(['images', 'seller'])
            ->orderBy('total_sold', 'desc')
            ->take(8)
            ->get();

        // ── Top Rated ─────────────────────────────────────────────
        $topRatedProducts = Product::where('status', 'approved')
            ->where('total_reviews', '>=', 1)
            ->with(['images', 'seller'])
            ->orderBy('average_rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->take(8)
            ->get();

        $flashSales = FlashSale::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->with(['product.images', 'product.seller'])
            ->take(4)
            ->get();

        // ── Brands sorted by rating (highest first) ───────────────
        $brands = Brand::where('is_active', true)
            ->whereNotNull('logo')
            ->orderBy('average_rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->take(6) 
            ->get();

        // ── Ads ──────────────────────────────────────────────────
        $heroBannerAds  = AdHelper::forSlot('homepage_hero', 5);
        $topListingAds  = AdHelper::topListings(4);

        foreach ($heroBannerAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }
        
        return view('storefront.home', compact(
            'categories', 'featuredProducts', 'newArrivals',
            'bestSellers', 'topRatedProducts',
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
        $brands = Brand::where('is_active', true)->take(5)->get();

        // Base query for products (for paginated results)
        $query = Product::where('status', 'approved')
            ->with(['images', 'seller', 'category']);

        // Apply filters to main query
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

        // Sort main query
        switch ($request->sort) {
            case 'price_asc':  $query->orderBy('price', 'asc');  break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'rating':     $query->orderBy('average_rating', 'desc'); break;
            case 'popular':    $query->orderBy('total_sold', 'desc'); break;
            default:           $query->latest(); break;
        }

        $products = $query->paginate(30)->withQueryString();

        // ── Best Sellers & Top Rated with filters applied ───────────
        $bestSellersQuery = Product::where('status', 'approved')
            ->with(['images', 'seller']);
        
        if ($request->min_price) $bestSellersQuery->where('price', '>=', $request->min_price);
        if ($request->max_price) $bestSellersQuery->where('price', '<=', $request->max_price);
        if ($request->condition) $bestSellersQuery->whereIn('condition', (array) $request->condition);
        if ($request->q) {
            $q = $request->q;
            $bestSellersQuery->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }
        
        $bestSellers = $bestSellersQuery->orderBy('total_sold', 'desc')->take(8)->get();

        $topRatedQuery = Product::where('status', 'approved')
            ->where('total_reviews', '>=', 1)
            ->with(['images', 'seller']);
        
        if ($request->min_price) $topRatedQuery->where('price', '>=', $request->min_price);
        if ($request->max_price) $topRatedQuery->where('price', '<=', $request->max_price);
        if ($request->condition) $topRatedQuery->whereIn('condition', (array) $request->condition);
        if ($request->q) {
            $q = $request->q;
            $topRatedQuery->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }
        
        $topRatedProducts = $topRatedQuery->orderBy('average_rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->take(8)
            ->get();

        // ── Sponsored Top Listings with filters applied ───────────
        $sponsoredQuery = Ad::where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('promotable_type', Product::class)
            ->whereHasMorph('promotable', [Product::class], function ($q) use ($request) {
                $q->where('status', 'approved');
                
                // Apply same filters to sponsored products
                if ($request->min_price) $q->where('price', '>=', $request->min_price);
                if ($request->max_price) $q->where('price', '<=', $request->max_price);
                if ($request->condition) $q->whereIn('condition', (array) $request->condition);
                if ($request->q) {
                    $search = $request->q;
                    $q->where(function($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                }
            })
            ->with(['promotable.images', 'promotable.seller']);
        
        $topListingAds = $sponsoredQuery->take(4)->get();
        // ────────────────────────────────────────────────────────────

        // ── Ads ──────────────────────────────────────────────────
        $searchBannerAds = AdHelper::forSlot('search_results', 1);

        foreach ($searchBannerAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }
        foreach ($topListingAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        return view('storefront.shop', compact(
            'products', 'allCategories', 'currentCategory', 'brands',
            'searchBannerAds', 'topListingAds',
            'bestSellers', 'topRatedProducts'
        ));
    }

    public function shopCategory(Request $request, string $categorySlug)
    {
        $allCategories   = Category::where('is_active', true)->withCount('products')->with('subcategories')->get();
        $currentCategory = Category::where('slug', $categorySlug)->where('is_active', true)->firstOrFail();
        $brands          = Brand::where('is_active', true)->take(10)->get();

        // Base query for products
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

        // ── Best Sellers & Top Rated with filters applied ───────────
        $bestSellersQuery = Product::where('status', 'approved')
            ->where('category_id', $currentCategory->id)
            ->with(['images', 'seller']);
        
        if ($request->min_price) $bestSellersQuery->where('price', '>=', $request->min_price);
        if ($request->max_price) $bestSellersQuery->where('price', '<=', $request->max_price);
        if ($request->condition) $bestSellersQuery->whereIn('condition', (array) $request->condition);
        
        $bestSellers = $bestSellersQuery->orderBy('total_sold', 'desc')->take(8)->get();

        $topRatedQuery = Product::where('status', 'approved')
            ->where('category_id', $currentCategory->id)
            ->where('total_reviews', '>=', 1)
            ->with(['images', 'seller']);
        
        if ($request->min_price) $topRatedQuery->where('price', '>=', $request->min_price);
        if ($request->max_price) $topRatedQuery->where('price', '<=', $request->max_price);
        if ($request->condition) $topRatedQuery->whereIn('condition', (array) $request->condition);
        
        $topRatedProducts = $topRatedQuery->orderBy('average_rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->take(8)
            ->get();

        // ── Sponsored Top Listings with filters applied ───────────
        $sponsoredQuery = Ad::where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('promotable_type', Product::class)
            ->whereHasMorph('promotable', [Product::class], function ($q) use ($request, $currentCategory) {
                $q->where('status', 'approved')
                  ->where('category_id', $currentCategory->id);
                
                if ($request->min_price) $q->where('price', '>=', $request->min_price);
                if ($request->max_price) $q->where('price', '<=', $request->max_price);
                if ($request->condition) $q->whereIn('condition', (array) $request->condition);
            })
            ->with(['promotable.images', 'promotable.seller']);
        
        $topListingAds = $sponsoredQuery->take(4)->get();
        // ────────────────────────────────────────────────────────────

        // ── Ads ──────────────────────────────────────────────────
        $categoryBannerAds = AdHelper::forSlot('category_page', 1);

        foreach ($categoryBannerAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }
        foreach ($topListingAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        return view('storefront.shop', compact(
            'products', 'allCategories', 'currentCategory', 'brands',
            'categoryBannerAds', 'topListingAds',
            'bestSellers', 'topRatedProducts'
        ));
    }

    public function product(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'approved')
            ->with(['images', 'videos', 'seller', 'category', 'subcategory', 'reviews.user', 'options.values'])
            ->firstOrFail();

        $product->increment('views');

        // ── Sponsored related products using AdHelper ─────────────────────
        // Get sponsored ads for products in the SAME category
        $sponsoredRelatedAds = AdHelper::topListings(2, $product->category_id);
        
        // If no sponsored ads in this category, try with subcategory
        if ($sponsoredRelatedAds->isEmpty() && $product->subcategory_id) {
            $sponsoredRelatedAds = AdHelper::topListings(2, null, $product->subcategory_id);
        }
        
        // If STILL no sponsored ads, get any sponsored products as fallback
        if ($sponsoredRelatedAds->isEmpty()) {
            $sponsoredRelatedAds = AdHelper::topListings(2);
        }

        foreach ($sponsoredRelatedAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        $sponsoredRelatedIds = $sponsoredRelatedAds->map(fn($ad) => $ad->promotable_id)->all();

        // Get regular related products from same category
        $relatedProducts = Product::where('status', 'approved')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereNotIn('id', $sponsoredRelatedIds)
            ->with(['images', 'seller'])
            ->inRandomOrder()
            ->take(4)
            ->get();
        
        // If not enough, get from subcategory
        if ($relatedProducts->count() < 4 && $product->subcategory_id) {
            $moreProducts = Product::where('status', 'approved')
                ->where('subcategory_id', $product->subcategory_id)
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $sponsoredRelatedIds)
                ->whereNotIn('id', $relatedProducts->pluck('id')->toArray())
                ->with(['images', 'seller'])
                ->inRandomOrder()
                ->take(4 - $relatedProducts->count())
                ->get();
            
            $relatedProducts = $relatedProducts->merge($moreProducts);
        }

        $inWishlist = false;
        if (auth('web')->check()) {
            $inWishlist = Wishlist::where('user_id', auth('web')->id())
                ->where('wishlistable_type', 'App\Models\Product')
                ->where('wishlistable_id', $product->id)
                ->exists();
        }

        $sidebarAds = AdHelper::forSlot('product_page_sidebar', 2);

        $flashSale = \App\Models\FlashSale::where('product_id', $product->id)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->where(function ($q) {
                $q->whereNull('quantity_limit')
                  ->orWhereColumn('quantity_sold', '<', 'quantity_limit');
            })
            ->first();

        foreach ($sidebarAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        return view('storefront.product', compact(
            'product', 'relatedProducts', 'inWishlist',
            'sidebarAds', 'flashSale', 'sponsoredRelatedAds'
        ));
    }
    public function search(Request $request)
    {
        return $this->shop($request);
    }

    public function brands()
    {
        $searchQuery = request('search');
        
        // ── Sponsored brand ads with search filter ──────────────────
        $sponsoredBrandAdsQuery = Ad::where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('promotable_type', Brand::class)
            ->whereHasMorph('promotable', [Brand::class], function ($q) use ($searchQuery) {
                $q->where('is_active', true);
                
                if ($searchQuery) {
                    $q->where('name', 'like', '%' . $searchQuery . '%');
                }
            })
            ->with(['promotable.seller']);
        
        $sponsoredBrandAds = $sponsoredBrandAdsQuery
            ->get()
            ->sortByDesc(function ($ad) {
                return $ad->promotable ? $ad->promotable->average_rating : 0;
            })
            ->take(4)
            ->values();
        
        foreach ($sponsoredBrandAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }
        // ────────────────────────────────────────────────────────────

        $query = Brand::where('is_active', true)->with('seller');

        if ($searchQuery) {
            $query->where('name', 'like', '%' . $searchQuery . '%');
        }

        $query->orderBy('average_rating', 'desc')
              ->orderBy('total_reviews', 'desc');

        $brands = $query->paginate(20);

        return view('storefront.brands', compact('brands', 'sponsoredBrandAds'));
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

        // ── Sponsored service ads with filters applied ─────────────
        $sponsoredServiceAdsQuery = Ad::where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('promotable_type', ServiceListing::class)
            ->whereHasMorph('promotable', [ServiceListing::class], function ($q) use ($request) {
                $q->where('status', 'approved');
                
                if ($request->filled('search')) {
                    $search = $request->search;
                    $q->where(function($sub) use ($search) {
                        $sub->where('title', 'LIKE', "%{$search}%")
                            ->orWhere('description', 'LIKE', "%{$search}%");
                    });
                }
                
                if ($request->filled('category_id')) {
                    $q->where('category_id', $request->category_id);
                }
                
                if ($request->filled('pricing_type')) {
                    $q->where('pricing_type', $request->pricing_type);
                }
                
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->min_price);
                }
                
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->max_price);
                }
                
                if ($request->filled('rating')) {
                    $q->where('average_rating', '>=', $request->rating);
                }
            })
            ->with(['promotable.seller', 'promotable.category']);
        
        $sponsoredServiceAds = $sponsoredServiceAdsQuery->take(3)->get();
        // ────────────────────────────────────────────────────────────

        foreach ($sponsoredServiceAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        $perPage  = $request->get('per_page', 12);
        $services = $query->paginate($perPage);

        $categoryIds = \App\Models\ServiceListing::where('status', 'approved')
                        ->whereNotNull('category_id')
                        ->distinct()
                        ->pluck('category_id');

        $categories = \App\Models\Category::whereIn('id', $categoryIds)->get();

        return view('storefront.services', compact('services', 'categories', 'sponsoredServiceAds'));
    }

    public function serviceShow($slug)
    {
        $service = \App\Models\ServiceListing::where('slug', $slug)
                            ->where('status', 'approved')
                            ->with(['seller', 'category'])
                            ->firstOrFail();

        $service->increment('views');

        // ── Sponsored related services using AdHelper ────────────────────
        // Get sponsored ads for services in the SAME category
        $sponsoredRelatedServiceAds = AdHelper::topServiceListings(2, $service->category_id);
        
        // If no sponsored ads in this category, get any sponsored services as fallback
        if ($sponsoredRelatedServiceAds->isEmpty()) {
            $sponsoredRelatedServiceAds = AdHelper::topServiceListings(2);
        }

        foreach ($sponsoredRelatedServiceAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        $sponsoredRelatedServiceIds = $sponsoredRelatedServiceAds
            ->map(fn($ad) => $ad->promotable_id)->all();

        // Get regular related services from same category
        $relatedServices = \App\Models\ServiceListing::where('status', 'approved')
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->whereNotIn('id', $sponsoredRelatedServiceIds)
            ->with(['seller', 'category'])
            ->inRandomOrder()
            ->take(3)
            ->get();
        
        // If not enough, get recent services
        if ($relatedServices->count() < 3) {
            $recentServices = \App\Models\ServiceListing::where('status', 'approved')
                ->where('id', '!=', $service->id)
                ->whereNotIn('id', $sponsoredRelatedServiceIds)
                ->whereNotIn('id', $relatedServices->pluck('id')->toArray())
                ->with(['seller', 'category'])
                ->latest()
                ->take(3 - $relatedServices->count())
                ->get();
            
            $relatedServices = $relatedServices->merge($recentServices);
        }

        return view('storefront.services-show', compact(
            'service', 'relatedServices', 'sponsoredRelatedServiceAds'
        ));
    }

    public function houses(Request $request)
    {
        $query = HouseListing::where('status', 'approved');

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

        if ($request->filled('listing_type'))  $query->where('listing_type', $request->listing_type);
        if ($request->filled('property_type')) $query->where('property_type', $request->property_type);
        if ($request->filled('min_price'))     $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price'))     $query->where('price', '<=', $request->max_price);
        if ($request->filled('bedrooms'))      $query->where('bedrooms', '>=', $request->bedrooms);
        if ($request->filled('bathrooms'))     $query->where('bathrooms', '>=', $request->bathrooms);
        if ($request->filled('city'))          $query->where('city', 'LIKE', "%{$request->city}%");
        if ($request->filled('state'))         $query->where('state', 'LIKE', "%{$request->state}%");

        switch($request->get('sort', 'latest')) {
            case 'price_asc':  $query->orderBy('price', 'asc');  break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'oldest':     $query->orderBy('created_at', 'asc'); break;
            default:           $query->orderBy('created_at', 'desc');
        }

        // ── Sponsored house ads with filters applied ───────────────
        $sponsoredHouseAdsQuery = Ad::where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('promotable_type', HouseListing::class)
            ->whereHasMorph('promotable', [HouseListing::class], function ($q) use ($request) {
                $q->where('status', 'approved');
                
                if ($request->filled('search')) {
                    $search = $request->search;
                    $q->where(function($sub) use ($search) {
                        $sub->where('title', 'LIKE', "%{$search}%")
                            ->orWhere('description', 'LIKE', "%{$search}%")
                            ->orWhere('address', 'LIKE', "%{$search}%")
                            ->orWhere('city', 'LIKE', "%{$search}%")
                            ->orWhere('state', 'LIKE', "%{$search}%");
                    });
                }
                
                if ($request->filled('listing_type')) {
                    $q->where('listing_type', $request->listing_type);
                }
                
                if ($request->filled('property_type')) {
                    $q->where('property_type', $request->property_type);
                }
                
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->min_price);
                }
                
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->max_price);
                }
                
                if ($request->filled('bedrooms')) {
                    $q->where('bedrooms', '>=', $request->bedrooms);
                }
                
                if ($request->filled('bathrooms')) {
                    $q->where('bathrooms', '>=', $request->bathrooms);
                }
                
                if ($request->filled('city')) {
                    $q->where('city', 'LIKE', "%{$request->city}%");
                }
                
                if ($request->filled('state')) {
                    $q->where('state', 'LIKE', "%{$request->state}%");
                }
            })
            ->with(['promotable.images', 'promotable.seller']);
        
        $sponsoredHouseAds = $sponsoredHouseAdsQuery->take(3)->get();
        // ────────────────────────────────────────────────────────────

        foreach ($sponsoredHouseAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        $perPage = $request->get('per_page', 12);
        $houses  = $query->with(['seller', 'images'])->paginate($perPage);
        
        return view('storefront.houses', compact('houses', 'sponsoredHouseAds'));
    }

    public function housesshow($slug)
    {
        $house = HouseListing::where('slug', $slug)
                            ->where('status', 'approved')
                            ->with(['seller', 'images'])
                            ->firstOrFail();
                            
        // ── Sponsored similar houses using AdHelper ──────────────────────
        // Get sponsored ads for similar houses (AdHelper might not support filtering by type)
        // So we'll get sponsored houses and then filter them
        $sponsoredSimilarAds = AdHelper::topHouseListings(4);
        
        // Filter to only show houses similar to current one
        $sponsoredSimilarAds = $sponsoredSimilarAds->filter(function($ad) use ($house) {
            $promotable = $ad->promotable;
            if (!$promotable) return false;
            
            return $promotable->listing_type == $house->listing_type ||
                   $promotable->property_type == $house->property_type ||
                   $promotable->city == $house->city;
        })->take(2);
        
        // If no matching sponsored houses, get any sponsored houses
        if ($sponsoredSimilarAds->isEmpty()) {
            $sponsoredSimilarAds = AdHelper::topHouseListings(2);
        }

        foreach ($sponsoredSimilarAds as $ad) {
            AdHelper::recordImpression($ad->id, auth('web')->id());
        }

        $sponsoredSimilarIds = $sponsoredSimilarAds->map(fn($ad) => $ad->promotable_id)->all();

        // Get regular similar houses
        $similarHouses = HouseListing::where('status', 'approved')
            ->where('id', '!=', $house->id)
            ->whereNotIn('id', $sponsoredSimilarIds)
            ->where(function($query) use ($house) {
                $query->where('listing_type', $house->listing_type)
                      ->orWhere('property_type', $house->property_type)
                      ->orWhere('city', $house->city);
            })
            ->inRandomOrder()
            ->take(4)
            ->get();
        
        // If not enough, get recent houses
        if ($similarHouses->count() < 4) {
            $recentHouses = HouseListing::where('status', 'approved')
                ->where('id', '!=', $house->id)
                ->whereNotIn('id', $sponsoredSimilarIds)
                ->whereNotIn('id', $similarHouses->pluck('id')->toArray())
                ->with(['seller', 'images'])
                ->latest()
                ->take(4 - $similarHouses->count())
                ->get();
            
            $similarHouses = $similarHouses->merge($recentHouses);
        }

        $house->increment('views');

        return view('storefront.houses-details', compact('house', 'similarHouses', 'sponsoredSimilarAds'));
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
        
        $hasReviewed = \App\Models\ProductReview::where('product_id', $product->id)
            ->where('user_id', auth()->id())
            ->exists();
        
        if ($hasReviewed) {
            return back()->with('error', 'You have already reviewed this product.');
        }
        
        $imagePaths = [];
        if ($request->hasFile('images')) {
            $cloudinary = app(\App\Services\CloudinaryService::class);
            foreach ($request->file('images') as $index => $image) {
                $uploaded = $cloudinary->uploadImage(
                    $image,
                    'orderer/reviews/' . $product->id . '/' . auth()->id()
                );
                $imagePaths[] = $uploaded['url'];
            }
        } 
        
        \App\Models\ProductReview::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'review' => $request->review,
            'images' => $imagePaths,
            'is_verified_purchase' => true,
            'is_visible' => true,
        ]);
        
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