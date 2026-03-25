<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ServiceListing;
use App\Models\Category;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function index()
    {
        $services = ServiceListing::where('seller_id', auth('seller')->id())
            ->latest()
            ->paginate(15);

        return view('seller.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('seller.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'category_id'   => ['required', 'exists:categories,id'],
            'description'   => ['required', 'string', 'min:50'],
            'pricing_type'  => ['required', 'in:fixed,hourly,negotiable'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'delivery_time' => ['nullable', 'string', 'max:100'],
            'location'      => ['nullable', 'string', 'max:200'],
            'portfolio.*'   => ['image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        $portfolioUrls = [];
        if ($request->hasFile('portfolio')) {
            foreach ($request->file('portfolio') as $img) {
                $uploaded = $this->cloudinary->uploadImage($img, 'orderer/services');
                $portfolioUrls[] = [
                    'url'       => $uploaded['url'],
                    'public_id' => $uploaded['public_id'],
                ];
            }
        }

        ServiceListing::create([
            'seller_id'        => auth('seller')->id(),
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'description'      => $request->description,
            'pricing_type'     => $request->pricing_type,
            'price'            => $request->price,
            'delivery_time'    => $request->delivery_time,
            'location'         => $request->location,
            'portfolio_images' => $portfolioUrls,
            'status'           => 'pending',
        ]);

        return redirect()->route('seller.services.index')
            ->with('success', 'Service submitted for review.');
    }

    public function destroy(ServiceListing $service)
    {
        if ($service->seller_id !== auth('seller')->id()) abort(403);

        if ($service->portfolio_images) {
            foreach ($service->portfolio_images as $img) {
                $this->cloudinary->delete($img['public_id']);
            }
        }

        $service->delete();

        return redirect()->route('seller.services.index')
            ->with('success', 'Service deleted.');
    }
}