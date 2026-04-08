<?php
namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\{Product, Brand, ServiceListing, HouseListing};

class OgImageController extends Controller
{
    public function generate(string $type, string $slug)
    {
        $manager = new ImageManager(new Driver());

        [$imageUrl, $isVerified] = match($type) {
            'product' => $this->resolveProduct($slug),
            'brand'   => $this->resolveBrand($slug),
            'service' => $this->resolveService($slug),
            'house'   => $this->resolveHouse($slug),
            default   => [null, false],
        };

        // Load base image (from URL or fallback)
        $baseUrl = $imageUrl ?? asset('dashboard/assets/images/favicon.png');
        $imageData = @file_get_contents($baseUrl);
        if (!$imageData) {
            $imageData = file_get_contents(public_path('dashboard/assets/images/favicon.png'));
        }

        $img = $manager->read($imageData);

        // Resize to standard OG size
        $img->cover(1200, 630);

        // Overlay Orderer logo bottom-left (small)
        $logoPath = public_path('img/core-img/logo.png');
        if (file_exists($logoPath)) {
            $logo = $manager->read($logoPath)->scale(width: 120);
            $img->place($logo, 'bottom-left', 20, 20);
        }

        // Overlay verified badge top-right if verified
        if ($isVerified) {
            $badgePath = public_path('img/core-img/verified-badge.png');
            if (file_exists($badgePath)) {
                $badge = $manager->read($badgePath)->scale(width: 80);
                $img->place($badge, 'top-right', 20, 20);
            }
        }

        // Cache for 7 days
        $encoded = $img->toJpeg(85);

        return response($encoded)
            ->header('Content-Type', 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=604800');
    }

    private function resolveProduct(string $slug): array
    {
        $product = Product::where('slug', $slug)->with(['images', 'seller'])->first();
        $img = $product?->images->where('is_primary', true)->first() ?? $product?->images->first();
        return [$img?->image_url, $product?->seller?->is_verified_business ?? false];
    }

    private function resolveBrand(string $slug): array
    {
        $brand = Brand::where('slug', $slug)->with('seller')->first();
        return [$brand?->logo, $brand?->seller?->is_verified_business ?? false];
    }

    private function resolveService(string $slug): array
    {
        $service = ServiceListing::where('slug', $slug)->with('seller')->first();
        $images = $service?->portfolio_images ?? [];
        $img = is_array($images) && count($images) ? (is_array($images[0]) ? ($images[0]['url'] ?? null) : $images[0]) : null;
        return [$img, $service?->seller?->is_verified_business ?? false];
    }

    private function resolveHouse(string $slug): array
    {
        $house = HouseListing::where('slug', $slug)->with(['images', 'seller'])->first();
        $img = $house?->images->where('is_primary', true)->first() ?? $house?->images->first();
        return [$img?->image_url, $house?->seller?->is_verified_business ?? false];
    }
}