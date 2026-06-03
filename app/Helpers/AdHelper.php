<?php

namespace App\Helpers;

use App\Models\Ad;
use App\Models\AdBannerSlot;

class AdHelper
{
    /**
     * Fetch active ads for a given banner slot location.
     */
    public static function forSlot(string $location, int $limit = 5)
    {
        $slot = AdBannerSlot::where('location', $location)
            ->where('is_active', true)
            ->first();

        if (!$slot) {
            return collect();
        }

        return Ad::where('ad_banner_slot_id', $slot->id)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('adCategory')
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    /**
     * Fetch top listing sponsored Products.
     */
    public static function topListings(int $limit = 4, ?string $categoryId = null)
    {
        $query = Ad::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('adCategory', fn($q) => $q->where('type', 'top_listing'))
            ->where('promotable_type', 'App\Models\Product')
            ->with(['promotable.images', 'promotable.seller'])
            ->inRandomOrder();

        if ($categoryId) {
            $query->whereHasMorph('promotable', ['App\Models\Product'], function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->where('status', 'approved');
            });
        } else {
            $query->whereHasMorph('promotable', ['App\Models\Product'], function ($q) {
                $q->where('status', 'approved');
            });
        }

        return $query->take($limit)->get()->filter(fn($ad) => $ad->promotable !== null)->values();
    }

    /**
     * Fetch top listing sponsored Services.
     */
    public static function topServiceListings(int $limit = 3)
    {
        return Ad::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('adCategory', fn($q) => $q->where('type', 'top_listing'))
            ->where('promotable_type', 'App\Models\ServiceListing')
            ->whereHasMorph('promotable', ['App\Models\ServiceListing'], function ($q) {
                $q->where('status', 'approved');
            })
            ->with(['promotable.seller', 'promotable.category'])
            ->inRandomOrder()
            ->take($limit)
            ->get()
            ->filter(fn($ad) => $ad->promotable !== null)
            ->values();
    }

    /**
     * Fetch top listing sponsored Houses.
     */
    public static function topHouseListings(int $limit = 3)
    {
        return Ad::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('adCategory', fn($q) => $q->where('type', 'top_listing'))
            ->where('promotable_type', 'App\Models\HouseListing')
            ->whereHasMorph('promotable', ['App\Models\HouseListing'], function ($q) {
                $q->where('status', 'approved');
            })
            ->with(['promotable.seller', 'promotable.images'])
            ->inRandomOrder()
            ->take($limit)
            ->get()
            ->filter(fn($ad) => $ad->promotable !== null)
            ->values();
    }

    /**
     * Fetch top listing sponsored Brands.
     */
    public static function topBrandListings(int $limit = 4)
    {
        return Ad::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('adCategory', fn($q) => $q->where('type', 'top_listing'))
            ->where('promotable_type', 'App\Models\Brand')
            ->whereHasMorph('promotable', ['App\Models\Brand'], function ($q) {
                $q->where('is_active', true);
            })
            ->with(['promotable.seller'])
            ->inRandomOrder()
            ->take($limit)
            ->get()
            ->filter(fn($ad) => $ad->promotable !== null)
            ->values();
    }

    /**
     * Record an ad impression.
     */
    public static function recordImpression(string $adId, ?string $userId = null): void
    {
        try {
            \App\Models\AdImpression::create([
                'ad_id'      => $adId,
                'user_id'    => $userId,
                'ip_address' => request()->ip(),
            ]);
            Ad::where('id', $adId)->increment('total_impressions');
        } catch (\Throwable $e) {
            \Log::warning('Ad impression record failed: ' . $e->getMessage());
        }
    }

    /**
     * Record an ad click.
     */
    public static function recordClick(string $adId, ?string $userId = null): void
    {
        try {
            \App\Models\AdClick::create([
                'ad_id'      => $adId,
                'user_id'    => $userId,
                'ip_address' => request()->ip(),
            ]);
            Ad::where('id', $adId)->increment('total_clicks');
        } catch (\Throwable $e) {
            \Log::warning('Ad click record failed: ' . $e->getMessage());
        }
    }
}