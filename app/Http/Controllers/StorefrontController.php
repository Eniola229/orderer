<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\FlashSale;
use App\Models\Wishlist;
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

        return view('storefront.home', compact(
            'categories', 'featuredProducts', 'newArrivals',
            'flashSales', 'brands'
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

        return view('storefront.shop', compact(
            'products', 'allCategories', 'currentCategory', 'brands'
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

        return view('storefront.shop', compact(
            'products', 'allCategories', 'currentCategory', 'brands'
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

        return view('storefront.product', compact('product', 'relatedProducts', 'inWishlist'));
    }

    public function search(Request $request)
    {
        return $this->shop($request);
    }

    public function brands()
    {
        $brands = Brand::where('is_active', true)->with('seller')->paginate(20);
        return view('storefront.brands', compact('brands'));
    }

    public function brandShow(string $slug)
    {
        $brand    = Brand::where('slug', $slug)->where('is_active', true)->with(['seller', 'reviews.user'])->firstOrFail();
        $products = Product::where('seller_id', $brand->seller_id)
            ->where('status', 'approved')
            ->with('images')
            ->paginate(12);
        return view('storefront.brand-show', compact('brand', 'products'));
    }

    public function services(Request $request)
    {
        $services = \App\Models\ServiceListing::where('status', 'approved')
            ->with(['seller', 'category'])
            ->paginate(12);
        return view('storefront.services', compact('services'));
    }

    public function houses(Request $request)
    {
        $houses = \App\Models\HouseListing::where('status', 'approved')
            ->with(['seller', 'images'])
            ->paginate(12);
        return view('storefront.houses', compact('houses'));
    }

    public function contact()
    {
        return view('storefront.contact');
    }

    public function newsletterSubscribe(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        // Store subscriber - simple for now
        return back()->with('success', 'Thank you for subscribing!');
    }
}
