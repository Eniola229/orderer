<?php

namespace App\Http\Controllers;

use App\Helpers\AdHelper;
use App\Models\Ad;
use Illuminate\Http\Request;

class AdClickController extends Controller
{
    /**
     * Record a click then redirect to the ad destination.
     * Route: GET /ads/{ad}/click
     */
    public function click(Ad $ad, Request $request)
    {
        // Only count clicks on active ads
        if ($ad->status === 'active') {
            AdHelper::recordClick(
                $ad->id,
                auth('web')->id() ?? null
            );
        }

        // Determine redirect target
        if ($ad->click_url) {
            return redirect($ad->click_url);
        }

        // Fall back to the product / brand page
        if ($ad->promotable) {
            $promotable = $ad->promotable;

            if ($promotable instanceof \App\Models\Product) {
                return redirect()->route('product.show', $promotable->slug);
            }

            if ($promotable instanceof \App\Models\Brand) {
                return redirect()->route('brands.show', $promotable->slug);
            }

            if ($promotable instanceof \App\Models\ServiceListing) {
                return redirect()->route('services.show', $promotable->slug);
            }

            if ($promotable instanceof \App\Models\HouseListing) {
                return redirect()->route('houses.show', $promotable->slug);
            }
        }

        return redirect()->route('home');
    }
}
