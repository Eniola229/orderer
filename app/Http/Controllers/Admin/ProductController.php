<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Notification;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if (!auth('admin')->user()->canView()) abort(403);

        $query = Product::with(['seller', 'category', 'images', 'subcategory']);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->stock_status) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock', '=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock', '>', 0)->where('stock', '<=', 5);
            }
        }
        
        if ($request->featured === 'yes') {
            $query->where('is_featured', true);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhereHas('seller', fn($r) => $r->where('business_name', 'like', "%{$s}%"));
            });
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        
        $featuredCount = Product::where('is_featured', true)->count();
        $lowStockCount = Product::where('stock', '>', 0)->where('stock', '<=', 5)->count();
        $outOfStockCount = Product::where('stock', '=', 0)->count();
        $totalProductsCount = Product::count();
        $pendingProductsCount = Product::where('status', 'pending')->count();

        return view('admin.products.index', compact('products', 'categories', 'featuredCount', 'lowStockCount', 'outOfStockCount', 'totalProductsCount', 'pendingProductsCount'));
    }
    public function toggleFeatured(Product $product)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $product->update(['is_featured' => !$product->is_featured]);

        $status = $product->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Product \"{$product->name}\" {$status}.");
    }
    public function show(Product $product)
    {
        if (!auth('admin')->user()->canView()) abort(403);
        
        $product->load(['seller', 'category', 'images', 'videos', 'options.values']);
        
        return view('admin.products.show', compact('product'));
    }

    public function pending()
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $products = Product::where('status', 'pending')
            ->with(['seller', 'category', 'images'])
            ->latest()
            ->paginate(20);

        return view('admin.products.pending', compact('products'));
    }

    public function approve(Product $product)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $product->update(['status' => 'approved', 'approved_by' => auth('admin')->id()]);

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $product->seller_id,
            'type'            => 'product_approved',
            'title'           => 'Product Approved',
            'body'            => "Your product \"{$product->name}\" is now live on Orderer.",
            'action_url'      => route('seller.products.index'),
        ]);

        return back()->with('success', "Product \"{$product->name}\" approved.");
    }

    public function reject(Request $request, Product $product)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $product->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $product->seller_id,
            'type'            => 'product_rejected',
            'title'           => 'Product Not Approved',
            'body'            => "Your product \"{$product->name}\" was not approved. Reason: {$request->reason}",
            'action_url'      => route('seller.products.index'),
        ]);

        return back()->with('success', 'Product rejected.');
    }

    public function suspend(Product $product)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);
        $product->update(['status' => 'suspended']);
        return back()->with('success', 'Product suspended.');
    }
}