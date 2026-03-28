<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerBadge;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = SellerBadge::withCount('sellers')->get();
        return view('admin.badges.index', compact('badges'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'icon'           => ['nullable', 'string'],
            'color'          => ['nullable', 'string', 'max:20'],
            'description'    => ['nullable', 'string'],
            'criteria_type'  => ['required', 'in:manual,orders_count,rating,verified'],
            'criteria_value' => ['nullable', 'integer'],
        ]);

        SellerBadge::create([
            'name'           => $request->name,
            'slug'           => Str::slug($request->name),
            'icon'           => $request->icon ?? 'feather-award',
            'color'          => $request->color ?? '#2ECC71',
            'description'    => $request->description,
            'criteria_type'  => $request->criteria_type,
            'criteria_value' => $request->criteria_value,
            'is_active'      => true,
        ]);

        return back()->with('success', 'Badge created.');
    }

    public function award(Request $request, SellerBadge $badge)
    {
        $request->validate([
            'seller_id' => ['required', 'exists:sellers,id'],
        ]);

        $badge->sellers()->syncWithoutDetaching([
            $request->seller_id => ['awarded_at' => now(), 'awarded_by' => auth('admin')->id()]
        ]);

        $seller = Seller::find($request->seller_id);

        \App\Models\Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $seller->id,
            'type'            => 'badge_awarded',
            'title'           => 'Badge Awarded: ' . $badge->name,
            'body'            => "Congratulations! You have been awarded the \"{$badge->name}\" badge.",
            'action_url'      => route('seller.brand.index'),
        ]);

        return back()->with('success', "Badge awarded to {$seller->business_name}.");
    }

    public function revoke(SellerBadge $badge, Seller $seller)
    {
        $badge->sellers()->detach($seller->id);
        return back()->with('success', 'Badge revoked.');
    }

    /**
     * Auto-award badges based on criteria.
     * Run via: php artisan badges:auto-award
     */
    public function autoAward(): void
    {
        $badges = SellerBadge::where('is_active', true)
            ->whereIn('criteria_type', ['orders_count', 'rating', 'verified'])
            ->get();

        foreach ($badges as $badge) {
            match ($badge->criteria_type) {
                'orders_count' => $this->awardByOrderCount($badge),
                'rating'       => $this->awardByRating($badge),
                'verified'     => $this->awardByVerified($badge),
                default        => null,
            };
        }
    }

    protected function awardByOrderCount(SellerBadge $badge): void
    {
        $sellers = Seller::where('is_approved', true)
            ->whereDoesntHave('badges', fn($q) => $q->where('badge_id', $badge->id))
            ->withCount(['orderItems as completed_orders' => fn($q) => $q->where('status', 'completed')])
            ->having('completed_orders', '>=', $badge->criteria_value)
            ->get();

        foreach ($sellers as $seller) {
            $badge->sellers()->syncWithoutDetaching([
                $seller->id => ['awarded_at' => now(), 'awarded_by' => null]
            ]);
        }
    }

    protected function awardByRating(SellerBadge $badge): void
    {
        // Average product rating from reviews
        $sellers = Seller::where('is_approved', true)
            ->whereDoesntHave('badges', fn($q) => $q->where('badge_id', $badge->id))
            ->whereHas('products', fn($q) =>
                $q->where('average_rating', '>=', ($badge->criteria_value / 10))
            )
            ->get();

        foreach ($sellers as $seller) {
            $badge->sellers()->syncWithoutDetaching([
                $seller->id => ['awarded_at' => now()]
            ]);
        }
    }

    protected function awardByVerified(SellerBadge $badge): void
    {
        $sellers = Seller::where('is_verified_business', true)
            ->where('is_approved', true)
            ->whereDoesntHave('badges', fn($q) => $q->where('badge_id', $badge->id))
            ->get();

        foreach ($sellers as $seller) {
            $badge->sellers()->syncWithoutDetaching([
                $seller->id => ['awarded_at' => now()]
            ]);
        }
    }
}