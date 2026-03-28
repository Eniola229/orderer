<?php

namespace App\Helpers;

use App\Models\Ad;
use App\Models\AdBannerSlot;

class AdHelper
{
    /**
     * Fetch active ads for a given banner slot location.
     * Ordered randomly so different ads rotate on each page load.
     *
     * @param  string  $location  e.g. 'homepage_hero', 'category_page', 'product_page_sidebar', 'search_results'
     * @param  int     $limit
     * @return \Illuminate\Support\Collection
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
            ->whereHas('adCategory', fn($q) => $q->where('is_active', true))
            ->with('adCategory')
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    /**
     * Fetch top_listing sponsored products.
     * Returns a collection of Ad records whose promotable is a Product.
     *
     * @param  int     $limit
     * @param  string|null $categoryId  Filter to a specific category's products
     * @return \Illuminate\Support\Collection
     */
    public static function topListings(int $limit = 4, ?string $categoryId = null)
    {
        $query = Ad::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('adCategory', fn($q) => $q->where('type', 'top_listing')->where('is_active', true))
            ->where('promotable_type', 'App\Models\Product')
            ->with(['promotable.images', 'promotable.seller'])
            ->inRandomOrder();

        if ($categoryId) {
            $query->whereHasMorph('promotable', ['App\Models\Product'], function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)->where('status', 'approved');
            });
        } else {
            $query->whereHasMorph('promotable', ['App\Models\Product'], function ($q) {
                $q->where('status', 'approved');
            });
        }

        return $query->take($limit)->get();
    }

    /**
     * Record an ad impression (fire and forget — no exception thrown).
     */
    public static function recordImpression(string $adId, ?string $userId = null): void
    {
        try {
            \App\Models\AdImpression::create([
                'ad_id'      => $adId,
                'user_id'    => $userId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Ad::where('id', $adId)->increment('total_impressions');

        } catch (\Throwable $e) {
            // Never break the page over an ad impression failure
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
                'user_agent' => request()->userAgent(),
            ]);

            Ad::where('id', $adId)->increment('total_clicks');

        } catch (\Throwable $e) {
            \Log::warning('Ad click record failed: ' . $e->getMessage());
        }
    }
}
