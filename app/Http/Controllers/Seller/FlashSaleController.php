<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    /**
     */
    public function index()
    {
        $seller = auth('seller')->user();

        $flashSales = FlashSale::with('product.seller')
            ->whereHas('product', fn($q) => $q->where('seller_id', $seller->id))
            ->latest()
            ->paginate(20);

        return view('seller.flash-sales.index', compact('flashSales'));
    }

    public function create()
    {
        $seller = auth('seller')->user();

        // Only the seller's own approved products
        $products = Product::where('status', 'approved')
            ->where('seller_id', $seller->id)
            ->get();

        return view('seller.flash-sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $seller = auth('seller')->user();

        $request->validate([
            'product_id'     => ['required', 'exists:products,id'],
            'title'          => ['required', 'string', 'max:200'],
            'sale_price'     => ['required', 'numeric', 'min:0.01'],
            'quantity_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at'      => ['required', 'date'],
            'ends_at'        => ['required', 'date', 'after:starts_at'],
        ]);

        // Make sure the product actually belongs to this seller
        $product = Product::where('id', $request->product_id)
            ->where('seller_id', $seller->id)
            ->where('status', 'approved')
            ->firstOrFail();

        FlashSale::create([
            'title'          => $request->title,
            'product_id'     => $product->id,
            'original_price' => $product->price,
            'sale_price'     => $request->sale_price,
            'quantity_limit' => $request->quantity_limit,
            'starts_at'      => $request->starts_at,
            'ends_at'        => $request->ends_at,
            'is_active'      => false,   // pending admin approval / starts scheduled
            'created_by'     => null,    // seller-created; admin sees created_by = null
        ]);

        return redirect()->route('seller.flash-sales.index')
            ->with('success', 'Flash sale submitted for admin approval. Once approved, you can activate it.');
    }

    public function toggle(FlashSale $flashSale)
    {
        $this->authoriseSale($flashSale);

        // Only allow toggle if admin has approved (created_by is not null)
        if (is_null($flashSale->created_by)) {
            return back()->with('error', 'Cannot activate this flash sale. It requires approval first.');
        }

        $flashSale->update(['is_active' => !$flashSale->is_active]);

        return back()->with('success', 'Flash sale ' . ($flashSale->is_active ? 'activated' : 'paused') . '.');
    }

    public function destroy(FlashSale $flashSale)
    {
        $this->authoriseSale($flashSale);

        return back()->with('error', 'Sorry!!!!!.');
    }

    // ── Helpers ────────────────────────────────────────────────────────

    private function authoriseSale(FlashSale $sale): void
    {
        $seller = auth('seller')->user();

        abort_unless(
            $sale->product && $sale->product->seller_id === $seller->id,
            403,
            'Unauthorised.'
        );
    }
}