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
            'portfolio.*'   => ['image', 'mimes:jpg,jpeg,png', 'max:10096'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
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
            'portfolio_url' => $request->portfolio_url,
        ]);

        return redirect()->route('seller.services.index')
            ->with('success', 'Service submitted for review.');
    }

    public function show(ServiceListing $service)
    {
        // Check if service belongs to current seller
        $this->authorizeProduct($service);
        
        $service->load(['category']);
        
        return view('seller.services.show', compact('service'));
    }

    public function edit(ServiceListing $service)
    {
        // Check if service belongs to current seller
         $this->authorizeProduct($service);

        // If service is approved, show warning
        if ($service->status === 'approved') {
            session()->flash('warning', 'This service is approved. Any changes you make will require re-approval.');
        }

        $categories = Category::where('is_active', true)->get();

        return view('seller.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, ServiceListing $service)
    {
        // Check if service belongs to current seller
         $this->authorizeProduct($service);

        $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'category_id'    => ['required', 'exists:categories,id'],
            'description'    => ['required', 'string', 'min:50'],
            'pricing_type'   => ['required', 'in:fixed,hourly,negotiable'],
            'price'          => ['required_if:pricing_type,fixed,hourly', 'nullable', 'numeric', 'min:0.01'],
            'delivery_time'  => ['nullable', 'string', 'max:100'],
            'location'       => ['nullable', 'string', 'max:200'],
            'portfolio.*'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10096'],
            'remove_images'  => ['nullable', 'string'], 
            'portfolio_url' => ['nullable', 'url', 'max:500'],
        ]);

        // Prepare update data
        $updateData = [
            'title'         => $request->title,
            'category_id'   => $request->category_id,
            'description'   => $request->description,
            'pricing_type'  => $request->pricing_type,
            'delivery_time' => $request->delivery_time,
            'location'      => $request->location,
            'portfolio_url' => $request->portfolio_url,
        ];

        // Handle price based on pricing type
        if ($request->pricing_type === 'negotiable') {
            $updateData['price'] = null;
        } else {
            $updateData['price'] = $request->price;
        }

        // Handle portfolio images
        $currentImages = $service->portfolio_images ?? [];
        
        // Remove selected images - handle comma-separated string
        if ($request->has('remove_images') && !empty($request->remove_images)) {
            // Convert comma-separated string to array
            $removeImageIds = explode(',', $request->remove_images);
            
            foreach ($removeImageIds as $imagePublicId) {
                $imagePublicId = trim($imagePublicId);
                // Delete from Cloudinary
                //$this->cloudinary->deleteImage($imagePublicId);
                // Remove from array
                $currentImages = array_filter($currentImages, function($img) use ($imagePublicId) {
                    return $img['public_id'] !== $imagePublicId;
                });
            }
            $updateData['portfolio_images'] = array_values($currentImages);
        }

        // Upload new images
        if ($request->hasFile('portfolio')) {
            $newImages = [];
            foreach ($request->file('portfolio') as $img) {
                $uploaded = $this->cloudinary->uploadImage($img, 'orderer/services');
                $newImages[] = [
                    'url'       => $uploaded['url'],
                    'public_id' => $uploaded['public_id'],
                ];
            }
            
            // Merge existing with new
            $existingImages = $updateData['portfolio_images'] ?? $service->portfolio_images ?? [];
            $updateData['portfolio_images'] = array_merge($existingImages, $newImages);
        }

        // Reset status to pending if service was approved
        if ($service->status === 'approved') {
            $updateData['status'] = 'pending';
            $updateData['rejection_reason'] = null;
        }

        $service->update($updateData);

        $message = $service->status === 'pending' 
            ? 'Service updated and submitted for review.' 
            : 'Service updated successfully.';

        return redirect()->route('seller.services.index')
            ->with('success', $message);
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

        protected function authorizeProduct(ServiceListing $service): void
    {
        if ($service->seller_id !== auth('seller')->id()) {
            abort(403, 'Unauthorized');
        }
    }

}