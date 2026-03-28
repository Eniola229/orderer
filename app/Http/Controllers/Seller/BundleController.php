<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\BundleItem;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BundleController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index()
    {
        $bundles = ProductBundle::where('seller_id', auth('seller')->id())
            ->with('items.product')
            ->latest()
            ->paginate(15);

        return view('seller.bundles.index', compact('bundles'));
    }

    public function create()
    {
        $products = Product::where('seller_id', auth('seller')->id())
            ->where('status', 'approved')
            ->get();

        return view('seller.bundles.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:200'],
            'description'  => ['nullable', 'string'],
            'bundle_price' => ['required', 'numeric', 'min:0.01'],
            'products'     => ['required', 'array', 'min:2'],
            'products.*'   => ['exists:products,id'],
            'quantities'   => ['required', 'array'],
            'quantities.*' => ['integer', 'min:1'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $seller = auth('seller')->user();

        // Validate all products belong to this seller
        $products = Product::whereIn('id', $request->products)
            ->where('seller_id', $seller->id)
            ->where('status', 'approved')
            ->get();

        if ($products->count() !== count($request->products)) {
            return back()->with('error', 'All products must be your approved listings.');
        }

        // Calculate original total
        $originalTotal = 0;
        foreach ($products as $product) {
            $qty            = $request->quantities[$product->id] ?? 1;
            $originalTotal += ($product->sale_price ?? $product->price) * $qty;
        }

        if ($request->bundle_price >= $originalTotal) {
            return back()->with('error', 'Bundle price must be less than the total individual prices.');
        }

        DB::transaction(function () use ($request, $seller, $products, $originalTotal) {
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $u = $this->cloudinary->uploadImage($request->file('image'), 'orderer/bundles');
                $imageUrl = $u['url'];
            }

            $bundle = ProductBundle::create([
                'name'           => $request->name,
                'description'    => $request->description,
                'seller_id'      => $seller->id,
                'bundle_price'   => $request->bundle_price,
                'original_total' => $originalTotal,
                'bundle_image'   => $imageUrl,
                'is_active'      => true,
                'status'         => 'pending',
            ]);

            foreach ($request->products as $productId) {
                BundleItem::create([
                    'bundle_id'  => $bundle->id,
                    'product_id' => $productId,
                    'quantity'   => $request->quantities[$productId] ?? 1,
                ]);
            }
        });

        return redirect()->route('seller.bundles.index')
            ->with('success', 'Bundle created and submitted for review.');
    }

    public function destroy(ProductBundle $bundle)
    {
        if ($bundle->seller_id !== auth('seller')->id()) abort(403);
        $bundle->delete();
        return back()->with('success', 'Bundle deleted.');
    }
}