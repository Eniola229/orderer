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

        $query = Product::with(['seller', 'category']);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhereHas('seller', fn($r) => $r->where('business_name', 'like', "%{$s}%"))
            );
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        return view('admin.products.index', compact('products'));
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