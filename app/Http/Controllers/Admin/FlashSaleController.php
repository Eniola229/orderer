<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    public function index(Request $request)
    { 
        $query = FlashSale::with('product.seller');

        // Filter by status
        if ($request->filled('status')) {
            $now = now();
            
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true)
                          ->where('starts_at', '<=', $now)
                          ->where('ends_at', '>=', $now);
                    break;
                case 'paused':
                    $query->where('is_active', false);
                    break;
                case 'ended':
                    $query->where('ends_at', '<', $now);
                    break;
                case 'scheduled':
                    $query->where('is_active', true)
                          ->where('starts_at', '>', $now);
                    break;
            }
        }

        // Filter by date range (created_at)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by date range (starts_at)
        if ($request->filled('starts_from')) {
            $query->whereDate('starts_at', '>=', $request->starts_from);
        }
        if ($request->filled('starts_to')) {
            $query->whereDate('starts_at', '<=', $request->starts_to);
        }

        // Filter by date range (ends_at)
        if ($request->filled('ends_from')) {
            $query->whereDate('ends_at', '>=', $request->ends_from);
        }
        if ($request->filled('ends_to')) {
            $query->whereDate('ends_at', '<=', $request->ends_to);
        }

        // Search by product name or title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by seller
        if ($request->filled('seller_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('seller_id', $request->seller_id);
            });
        }

        // Get stats for cards
        $now = now();
        $stats = [
            'total' => FlashSale::count(),
            'active' => FlashSale::where('is_active', true)
                                ->where('starts_at', '<=', $now)
                                ->where('ends_at', '>=', $now)
                                ->count(),
            'paused' => FlashSale::where('is_active', false)->count(),
            'ended' => FlashSale::where('ends_at', '<', $now)->count(),
            'scheduled' => FlashSale::where('is_active', true)
                                   ->where('starts_at', '>', $now)
                                   ->count(),
        ];

        $flashSales = $query->latest()->paginate(20)->withQueryString();

        // For seller filter dropdown (optional)
        $sellers = \App\Models\Seller::select('id', 'business_name')->get();

        return view('admin.flash-sales.index', compact('flashSales', 'stats', 'sellers'));
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