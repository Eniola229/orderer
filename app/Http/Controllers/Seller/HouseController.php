<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\HouseListing;
use App\Models\HouseImage;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function index()
    {
        $houses = HouseListing::where('seller_id', auth('seller')->id())
            ->with('images')
            ->latest()
            ->paginate(15);

        return view('seller.houses.index', compact('houses'));
    }

    public function create()
    {
        return view('seller.houses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'min:50'],
            'property_type' => ['required', 'in:apartment,house,land,commercial,shortlet,other'],
            'listing_type'  => ['required', 'in:sale,rent,shortlet'],
            'price'         => ['required', 'numeric', 'min:0.01'],
            'location'      => ['required', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:100'],
            'bedrooms'      => ['nullable', 'integer', 'min:0'],
            'bathrooms'     => ['nullable', 'integer', 'min:0'],
            'images'        => ['required', 'array', 'min:1', 'max:10'],
            'images.*'      => ['image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'features'      => ['nullable', 'array'],
        ]);

        $house = HouseListing::create([
            'seller_id'     => auth('seller')->id(),
            'title'         => $request->title,
            'description'   => $request->description,
            'property_type' => $request->property_type,
            'listing_type'  => $request->listing_type,
            'price'         => $request->price,
            'location'      => $request->location,
            'address'       => $request->address,
            'city'          => $request->city,
            'state'         => $request->state,
            'country'       => $request->country,
            'bedrooms'      => $request->bedrooms,
            'bathrooms'     => $request->bathrooms,
            'toilets'       => $request->toilets,
            'size_sqm'      => $request->size_sqm,
            'features'      => $request->features,
            'video_tour_url'=> $request->video_tour_url,
            'status'        => 'pending',
        ]);

        foreach ($request->file('images') as $index => $imageFile) {
            $uploaded = $this->cloudinary->uploadImage(
                $imageFile,
                'orderer/properties/' . $house->id
            );
            HouseImage::create([
                'house_listing_id'     => $house->id,
                'image_url'            => $uploaded['url'],
                'cloudinary_public_id' => $uploaded['public_id'],
                'is_primary'           => $index === 0,
                'sort_order'           => $index,
            ]);
        }

        return redirect()->route('seller.houses.index')
            ->with('success', 'Property submitted for review.');
    }

    public function show(HouseListing $house)
    {
        // Ensure the seller owns this property
        if ($house->seller_id !== auth('seller')->id()) {
            abort(403, 'Unauthorized access to this property.');
        }
        
        return view('seller.houses.show', compact('house'));
    }
    public function destroy(HouseListing $house)
    {
        if ($house->seller_id !== auth('seller')->id()) abort(403);

        foreach ($house->images as $image) {
            $this->cloudinary->delete($image->cloudinary_public_id);
        }

        $house->delete();

        return redirect()->route('seller.houses.index')
            ->with('success', 'Property deleted.');
    }
}