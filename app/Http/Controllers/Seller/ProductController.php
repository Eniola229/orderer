<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVideo;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\Category;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function index()
    {
        $products = Product::with(['images', 'category'])
            ->where('seller_id', auth('seller')->id())
            ->latest()
            ->paginate(15);

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)
                               ->with('subcategories')
                               ->get();

        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'category_id'     => ['required', 'exists:categories,id'],
            'subcategory_id'  => ['nullable', 'exists:subcategories,id'],
            'description'     => ['required', 'string', 'min:30'],
            'price'           => ['required', 'numeric', 'min:0.01'],
            'sale_price'      => ['nullable', 'numeric', 'lt:price'],
            'stock'           => ['required', 'integer', 'min:0'],
            'condition'       => ['required', 'in:new,used,refurbished'],
            'weight_kg'       => ['nullable', 'numeric', 'min:0'],
            'location'        => ['nullable', 'string', 'max:200'],
            'sku'             => ['nullable', 'string', 'max:100',
                                   Rule::unique('products', 'sku')
                                       ->ignore(auth('seller')->id(), 'seller_id')],
            'images'          => ['required', 'array', 'min:1', 'max:8'],
            'images.*'        => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096'],
            'video'           => ['nullable', 'file', 'mimes:mp4,mov,avi', 'max:10240'],

            // Options (optional)
            'options'                        => ['nullable', 'array', 'max:5'],
            'options.*.name'                 => ['required_with:options', 'string', 'max:100'],
            'options.*.values'               => ['required_with:options', 'array', 'min:1'],
            'options.*.values.*.value'       => ['required', 'string', 'max:100'],
            'options.*.values.*.image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:2048'],
        ]);

        $product = Product::create([
            'seller_id'      => auth('seller')->id(),
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'name'           => $request->name,
            'description'    => $request->description,
            'price'          => $request->price,
            'sale_price'     => $request->sale_price,
            'stock'          => $request->stock,
            'condition'      => $request->condition,
            'weight_kg'      => $request->weight_kg,
            'location'       => $request->location,
            'sku'            => $request->sku,
            'status'         => 'pending',
        ]);

        // Upload images to Cloudinary
        foreach ($request->file('images') as $index => $imageFile) {
            $uploaded = $this->cloudinary->uploadImage(
                $imageFile,
                'orderer/products/' . $product->id
            );

            ProductImage::create([
                'product_id'           => $product->id,
                'image_url'            => $uploaded['url'],
                'cloudinary_public_id' => $uploaded['public_id'],
                'is_primary'           => $index === 0,
                'sort_order'           => $index,
            ]);
        }

        // Upload video if provided
        if ($request->hasFile('video')) {
            $uploadedVideo = $this->cloudinary->uploadVideo(
                $request->file('video'),
                'orderer/product-videos/' . $product->id
            );

            ProductVideo::create([
                'product_id'           => $product->id,
                'video_url'            => $uploadedVideo['url'],
                'cloudinary_public_id' => $uploadedVideo['public_id'],
            ]);
        }

        // Save options
        $this->saveOptions($request, $product);

        return redirect()->route('seller.products.index')
            ->with('success', 'Product submitted for review. We will notify you once it is approved.');
    }

    public function show(Product $product)
    {
        $this->authorizeProduct($product);

        $product->load(['images', 'videos', 'category', 'subcategory', 'options.values']);

        return view('seller.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);

        if ($product->status === 'approved') {
            return redirect()->route('seller.products.index')
                ->with('error', 'Approved products must be re-submitted for review after editing. Contact support to unlock.');
        }

        $categories = Category::where('is_active', true)
                               ->with('subcategories')
                               ->get();

        $product->load(['images', 'videos', 'category', 'subcategory', 'options.values']);

        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'category_id'    => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'description'    => ['required', 'string', 'min:30'],
            'price'          => ['required', 'numeric', 'min:0.01'],
            'sale_price'     => ['nullable', 'numeric', 'lt:price'],
            'stock'          => ['required', 'integer', 'min:0'],
            'condition'      => ['required', 'in:new,used,refurbished'],
            'new_images.*'   => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'options'                        => ['nullable', 'array', 'max:5'],
            'options.*.name'                 => ['required_with:options', 'string', 'max:100'],
            'options.*.values'               => ['required_with:options', 'array', 'min:1'],
            'options.*.values.*.value'       => ['required', 'string', 'max:100'],
            'options.*.values.*.image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $product->update([
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'name'           => $request->name,
            'description'    => $request->description,
            'price'          => $request->price,
            'sale_price'     => $request->sale_price,
            'stock'          => $request->stock,
            'condition'      => $request->condition,
            'weight_kg'      => $request->weight_kg,
            'location'       => $request->location,
            'sku'            => $request->sku,
            'status'         => 'pending',
        ]);

        // Upload new images if any
        if ($request->hasFile('new_images')) {
            $currentCount = $product->images()->count();
            foreach ($request->file('new_images') as $index => $imageFile) {
                if ($currentCount >= 8) break;
                $uploaded = $this->cloudinary->uploadImage(
                    $imageFile,
                    'orderer/products/' . $product->id
                );
                ProductImage::create([
                    'product_id'           => $product->id,
                    'image_url'            => $uploaded['url'],
                    'cloudinary_public_id' => $uploaded['public_id'],
                    'is_primary'           => false,
                    'sort_order'           => $currentCount + $index,
                ]);
                $currentCount++;
            }
        }

        // Replace options entirely on update
        $this->deleteOptions($product);
        $this->saveOptions($request, $product);

        return redirect()->route('seller.products.index')
            ->with('success', 'Product updated and re-submitted for review.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);

        foreach ($product->images as $image) {
            $this->cloudinary->delete($image->cloudinary_public_id);
        }

        foreach ($product->videos as $video) {
            $this->cloudinary->delete($video->cloudinary_public_id, 'video');
        }

        // Delete option value images from Cloudinary
        foreach ($product->options as $option) {
            foreach ($option->values as $val) {
                if ($val->cloudinary_public_id) {
                    $this->cloudinary->delete($val->cloudinary_public_id);
                }
            }
        }

        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Listing deleted.');
    }

    public function deleteImage(ProductImage $image)
    {
        $product = $image->product;
        $this->authorizeProduct($product);

        if ($product->images()->count() <= 1) {
            return back()->with('error', 'A product must have at least one image.');
        }

        $this->cloudinary->delete($image->cloudinary_public_id);
        $image->delete();

        if ($image->is_primary) {
            $product->images()->oldest()->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image removed.');
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Persist the options[] payload (new product or full replacement on update).
     */
    protected function saveOptions(Request $request, Product $product): void
    {
        $optionsInput = $request->input('options', []);

        if (empty($optionsInput)) {
            return;
        }

        foreach ($optionsInput as $optionIndex => $optionData) {
            $optionName = trim($optionData['name'] ?? '');
            if (!$optionName) continue;

            $option = ProductOption::create([
                'product_id' => $product->id,
                'name'       => $optionName,
                'sort_order' => $optionIndex,
            ]);

            $valuesInput = $optionData['values'] ?? [];

            foreach ($valuesInput as $valueIndex => $valueData) {
                $valueName = trim($valueData['value'] ?? '');
                if (!$valueName) continue;

                $imageUrl        = null;
                $cloudinaryPubId = null;

                // Check if there is an uploaded image file for this value
                $imageFile = $request->file("options.{$optionIndex}.values.{$valueIndex}.image");
                if ($imageFile) {
                    $uploaded        = $this->cloudinary->uploadImage(
                        $imageFile,
                        'orderer/product-options/' . $product->id
                    );
                    $imageUrl        = $uploaded['url'];
                    $cloudinaryPubId = $uploaded['public_id'];
                }

                ProductOptionValue::create([
                    'product_option_id'    => $option->id,
                    'value'                => $valueName,
                    'image_url'            => $imageUrl,
                    'cloudinary_public_id' => $cloudinaryPubId,
                    'sort_order'           => $valueIndex,
                ]);
            }
        }
    }

    /**
     * Delete all options (and their Cloudinary images) for a product.
     */
    protected function deleteOptions(Product $product): void
    {
        $product->load('options.values');

        foreach ($product->options as $option) {
            foreach ($option->values as $val) {
                if ($val->cloudinary_public_id) {
                    $this->cloudinary->delete($val->cloudinary_public_id);
                }
            }
        }

        $product->options()->delete(); // cascades to values via DB
    }

    protected function authorizeProduct(Product $product): void
    {
        if ($product->seller_id !== auth('seller')->id()) {
            abort(403, 'Unauthorized');
        }
    }
}