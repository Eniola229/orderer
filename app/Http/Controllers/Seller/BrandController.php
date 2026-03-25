<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index()
    {
        $seller = auth('seller')->user();
        $brand  = Brand::where('seller_id', $seller->id)->with('reviews')->first();
        return view('seller.brand.index', compact('seller', 'brand'));
    }

    public function store(Request $request)
    {
        $seller = auth('seller')->user();

        if (Brand::where('seller_id', $seller->id)->exists()) {
            return back()->with('error', 'You already have a brand. Edit it instead.');
        }

        $request->validate([
            'name'        => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'website'     => ['nullable', 'url'],
            'logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'banner'      => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        $logoUrl   = null;
        $bannerUrl = null;

        if ($request->hasFile('logo')) {
            $u = $this->cloudinary->uploadImage($request->file('logo'), 'orderer/brands/logos');
            $logoUrl = $u['url'];
        }

        if ($request->hasFile('banner')) {
            $u = $this->cloudinary->uploadImage($request->file('banner'), 'orderer/brands/banners');
            $bannerUrl = $u['url'];
        }

        Brand::create([
            'seller_id'   => $seller->id,
            'name'        => $request->name,
            'description' => $request->description,
            'website'     => $request->website,
            'logo'        => $logoUrl,
            'banner'      => $bannerUrl,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Brand created successfully.');
    }

    public function update(Request $request, Brand $brand)
    {
        if ($brand->seller_id !== auth('seller')->id()) abort(403);

        $request->validate([
            'name'        => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'website'     => ['nullable', 'url'],
            'logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'banner'      => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        $data = $request->only(['name', 'description', 'website']);

        if ($request->hasFile('logo')) {
            $u = $this->cloudinary->uploadImage($request->file('logo'), 'orderer/brands/logos');
            $data['logo'] = $u['url'];
        }

        if ($request->hasFile('banner')) {
            $u = $this->cloudinary->uploadImage($request->file('banner'), 'orderer/brands/banners');
            $data['banner'] = $u['url'];
        }

        $brand->update($data);

        return back()->with('success', 'Brand updated.');
    }
}