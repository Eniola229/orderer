<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    public function index()
    { 
        $flashSales = FlashSale::with('product.seller')
            ->latest()
            ->paginate(20);

        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function show(FlashSale $flashSale)
    {
        $flashSale->load(['product.seller']);
        return view('admin.flash-sales.show', compact('flashSale'));
    }

    public function create()
    {
        $products = Product::where('status', 'approved')
            ->with('seller')
            ->get();

        return view('admin.flash-sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'     => ['required', 'exists:products,id'],
            'title'          => ['required', 'string', 'max:200'],
            'sale_price'     => ['required', 'numeric', 'min:0.01'],
            'quantity_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at'      => ['required', 'date'],
            'ends_at'        => ['required', 'date', 'after:starts_at'],
        ]);

        $product = Product::find($request->product_id);

        FlashSale::create([
            'title'          => $request->title,
            'product_id'     => $request->product_id,
            'original_price' => $product->price,
            'sale_price'     => $request->sale_price,
            'quantity_limit' => $request->quantity_limit,
            'starts_at'      => $request->starts_at,
            'ends_at'        => $request->ends_at,
            'is_active'      => true,
        ]);

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash sale created.');
    }

    public function toggle(FlashSale $flashSale)
    {
        $newStatus = !$flashSale->is_active;
        
        $flashSale->update([
            'is_active' => $newStatus,
            'created_by' => $newStatus ? auth('admin')->id() : null
        ]);
        
        return back()->with('success', 'Flash sale ' . ($newStatus ? 'activated' : 'paused') . '.');
    }
    
    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();
        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash sale deleted.');
    }
}