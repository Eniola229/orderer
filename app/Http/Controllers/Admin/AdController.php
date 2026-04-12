<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdCategory;
use App\Models\AdBannerSlot;
use App\Services\WalletService;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdController extends Controller
{
    public function __construct(
        protected WalletService    $wallet,
        protected BrevoMailService $brevo
    ) {}

    public function index(Request $request)
    {
        $query = Ad::with(['seller', 'adCategory', 'bannerSlot']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Add the missing search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('seller', fn($s) => 
                      $s->where('business_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                  );
            });
        }

        // Get ALL for stats BEFORE paginating
        $stats = [
            'total'   => (clone $query)->count(),
            'active'  => (clone $query)->where('status', 'active')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'spent'   => (clone $query)->sum('amount_spent'),
        ];

        $pro = $query->latest()->paginate(20);

        return view('admin.ads.index', compact('pro', 'stats'));
    }
    
    public function show(Ad $ad)
    {
        if (!auth('admin')->user()->canManageAds()) abort(403);
        
        $ad->load(['seller', 'adCategory', 'bannerSlot']);
        
        return view('admin.ads.show', compact('ad'));
    }
    
    public function pending()
    {
        $ads = Ad::with(['seller', 'adCategory', 'bannerSlot'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.ads.pending', compact('ads'));
    }

    public function approve(Ad $ad)
    {
        if ($ad->status !== 'pending') {
            return back()->with('error', 'Only pending ads can be approved.');
        }

        $seller = $ad->seller;

        // Check ads balance
        $wallet = $this->wallet->getOrCreate($seller);
        if ($wallet->ads_balance < $ad->budget) {
            return back()->with('error', "Seller has insufficient ads balance ($" . number_format($wallet->ads_balance, 2) . ").");
        }

        // Deduct budget from seller ads balance
        $this->wallet->debitAdsBalance($seller, $ad->budget, $ad->id);

        $ad->update([
            'status'      => 'active',
            'approved_by' => auth('admin')->id(),
        ]);

        // Notify seller
        \App\Models\Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $seller->id,
            'type'            => 'ad_approved',
            'title'           => 'Ad Approved',
            'body'            => "Your ad \"{$ad->title}\" has been approved and is now live.",
            'action_url'      => route('seller.ads.index'),
        ]);

        return back()->with('success', "Ad \"{$ad->title}\" approved and activated.");
    }

    public function reject(Request $request, Ad $ad)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $ad->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        \App\Models\Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $ad->seller_id,
            'type'            => 'ad_rejected',
            'title'           => 'Ad Not Approved',
            'body'            => "Your ad \"{$ad->title}\" was not approved: {$request->rejection_reason}",
            'action_url'      => route('seller.ads.index'),
        ]);

        return back()->with('success', 'Ad rejected.');
    }

    public function suspend(Ad $ad)
    {
        $ad->update(['status' => 'paused']);
        return back()->with('success', 'Ad suspended.');
    }

    // Ad Categories
    public function categories()
    {
        $categories = AdCategory::latest()->get();
        return view('admin.ads.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'type'        => ['required', 'in:banner_image,banner_video,top_listing,cpc'],
            'description' => ['nullable', 'string'],
        ]);

        AdCategory::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'type'        => $request->type,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Ad category created.');
    }

    public function toggleCategory(AdCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return back()->with('success', 'Category status updated.');
    }

    // Banner Slots
    public function slots()
    {
        $slots = AdBannerSlot::latest()->get();
        return view('admin.ads.slots', compact('slots'));
    }

    public function storeSlot(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'location'     => ['required', 'in:homepage_hero,category_page,product_page_sidebar,search_results'],
            'price_per_day'=> ['required', 'numeric', 'min:0.01'],
            'max_ads'      => ['required', 'integer', 'min:1'],
            'dimensions'   => ['nullable', 'string', 'max:50'],
        ]);

        AdBannerSlot::create([
            'name'          => $request->name,
            'slug'          => Str::slug($request->name),
            'location'      => $request->location,
            'price_per_day' => $request->price_per_day,
            'max_ads'       => $request->max_ads,
            'dimensions'    => $request->dimensions,
            'is_active'     => true,
        ]);

        return back()->with('success', 'Banner slot created.');
    }

    public function updateSlotPrice(Request $request, AdBannerSlot $slot)
    {
        $request->validate([
            'price_per_day' => ['required', 'numeric', 'min:0.01'],
        ]);

        $slot->update(['price_per_day' => $request->price_per_day]);
        return back()->with('success', 'Slot pricing updated.');
    }

    public function toggleSlot(AdBannerSlot $slot)
    {
        $slot->update(['is_active' => !$slot->is_active]);
        return back()->with('success', 'Slot status updated.');
    }
}