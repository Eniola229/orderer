<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketer;
use App\Models\Product;
use App\Models\HouseListing;
use App\Models\ServiceListing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\Order;
use App\Models\DeliveryBooking;

class MarketerController extends Controller
{
    public function index()
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $marketers = Marketer::withCount('referredSellers')->latest()->paginate(20);

        return view('admin.marketers.index', compact('marketers'));
    }

    public function create()
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        return view('admin.marketers.create');
    }

    public function show(Request $request, Marketer $marketer)
    {
        // ── Seller filters ─────────────────────────────────────────────────
        $sellerSearch       = $request->input('seller_search', '');
        $sellerDateFrom     = $request->input('seller_date_from', '');
        $sellerDateTo       = $request->input('seller_date_to', '');
        $verificationFilter = $request->input('verification_filter', 'all');
        $statusFilter       = $request->input('status_filter', 'all');

        $sellerQuery = $marketer->referredSellers()->with('documents');

        if ($sellerSearch) {
            $sellerQuery->where(function ($q) use ($sellerSearch) {
                $q->where('first_name',     'like', "%{$sellerSearch}%")
                  ->orWhere('last_name',    'like', "%{$sellerSearch}%")
                  ->orWhere('email',        'like', "%{$sellerSearch}%")
                  ->orWhere('business_name','like', "%{$sellerSearch}%");
            });
        }
        if ($sellerDateFrom) $sellerQuery->whereDate('created_at', '>=', $sellerDateFrom);
        if ($sellerDateTo)   $sellerQuery->whereDate('created_at', '<=', $sellerDateTo);

        if ($verificationFilter !== 'all') {
            $sellerQuery->where('verification_status', $verificationFilter);
        }
        if ($statusFilter === 'approved') {
            $sellerQuery->where('is_approved', true);
        } elseif ($statusFilter === 'pending') {
            $sellerQuery->where('is_approved', false);
        }

        $sellers = $sellerQuery->latest()->get();

        // Seller listing counts
        $sellerIds      = $sellers->pluck('id');
        $productCounts  = \App\Models\Product::whereIn('seller_id',  $sellerIds)
                            ->selectRaw('seller_id, count(*) as total')
                            ->groupBy('seller_id')->pluck('total', 'seller_id');
        $propertyCounts = \App\Models\HouseListing::whereIn('seller_id', $sellerIds)
                            ->selectRaw('seller_id, count(*) as total')
                            ->groupBy('seller_id')->pluck('total', 'seller_id');
        $serviceCounts  = \App\Models\ServiceListing::whereIn('seller_id',  $sellerIds)
                            ->selectRaw('seller_id, count(*) as total')
                            ->groupBy('seller_id')->pluck('total', 'seller_id');

        // ── Seller stats ──────────────────────────────────────────────────
        $allSellers = $marketer->referredSellers();
        $stats = [
            'total'            => (clone $allSellers)->count(),
            'approved'         => (clone $allSellers)->where('is_approved', true)->count(),
            'pending'          => (clone $allSellers)->where('is_approved', false)->count(),
            'verified'         => (clone $allSellers)->where('verification_status', 'approved')->count(),
            'unverified'       => (clone $allSellers)->where('verification_status', '!=', 'approved')->count(),
            'total_products'   => $productCounts->sum(),
            'total_properties' => $propertyCounts->sum(),
            'total_services'   => $serviceCounts->sum(),
        ];

        // ── Buyer filters (NEW!) ─────────────────────────────────────────
        $buyerSearch    = $request->input('buyer_search', '');
        $buyerDateFrom  = $request->input('buyer_date_from', '');
        $buyerDateTo    = $request->input('buyer_date_to', '');
        $activityFilter = $request->input('activity_filter', 'all'); // all, ordered, booked, both

        $buyerQuery = $marketer->referredBuyers();

        if ($buyerSearch) {
            $buyerQuery->where(function ($q) use ($buyerSearch) {
                $q->where('first_name', 'like', "%{$buyerSearch}%")
                  ->orWhere('last_name', 'like', "%{$buyerSearch}%")
                  ->orWhere('email', 'like', "%{$buyerSearch}%");
            });
        }
        if ($buyerDateFrom) $buyerQuery->whereDate('created_at', '>=', $buyerDateFrom);
        if ($buyerDateTo)   $buyerQuery->whereDate('created_at', '<=', $buyerDateTo);

        // Get buyer IDs for activity filtering
        $buyerIds = (clone $buyerQuery)->pluck('id');
        
        if ($activityFilter !== 'all') {
            $buyerIdsWithOrders   = Order::whereIn('user_id', $buyerIds)->distinct('user_id')->pluck('user_id');
            $buyerIdsWithBookings = DeliveryBooking::whereIn('user_id', $buyerIds)->distinct('user_id')->pluck('user_id');
            
            if ($activityFilter === 'ordered') {
                $buyerQuery->whereIn('id', $buyerIdsWithOrders);
            } elseif ($activityFilter === 'booked') {
                $buyerQuery->whereIn('id', $buyerIdsWithBookings);
            } elseif ($activityFilter === 'both') {
                $buyerQuery->whereIn('id', $buyerIdsWithOrders->intersect($buyerIdsWithBookings));
            } elseif ($activityFilter === 'inactive') {
                $activeBuyers = $buyerIdsWithOrders->merge($buyerIdsWithBookings)->unique();
                $buyerQuery->whereNotIn('id', $activeBuyers);
            }
        }

        $buyers = $buyerQuery->latest()->paginate(20, ['*'], 'buyers_page');

        // ── Buyer stats (filtered) ────────────────────────────────────────
        $allBuyerIds = $marketer->referredBuyers()->pluck('id');
        $filteredBuyerIds = $buyers->pluck('id');

        $buyerStats = [
            'total'           => $allBuyerIds->count(),
            'this_month'      => $marketer->referredBuyers()
                                  ->whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count(),
            'with_orders'     => Order::whereIn('user_id', $allBuyerIds)->distinct('user_id')->count('user_id'),
            'with_bookings'   => DeliveryBooking::whereIn('user_id', $allBuyerIds)->distinct('user_id')->count('user_id'),
            'total_orders'    => Order::whereIn('user_id', $allBuyerIds)->count(),
            'total_bookings'  => DeliveryBooking::whereIn('user_id', $allBuyerIds)->count(),
        ];

        // Activity counts for filtered buyers
        $buyerOrderCounts   = Order::whereIn('user_id', $filteredBuyerIds)
                                ->selectRaw('user_id, count(*) as total')
                                ->groupBy('user_id')->pluck('total', 'user_id');
        $buyerBookingCounts = DeliveryBooking::whereIn('user_id', $filteredBuyerIds)
                                ->selectRaw('user_id, count(*) as total')
                                ->groupBy('user_id')->pluck('total', 'user_id');

        return view('admin.marketers.show', compact(
            'marketer',
            'sellers', 'stats',
            'productCounts', 'propertyCounts', 'serviceCounts',
            'sellerSearch', 'sellerDateFrom', 'sellerDateTo', 'verificationFilter', 'statusFilter',
            'buyers', 'buyerStats', 'buyerOrderCounts', 'buyerBookingCounts',
            'buyerSearch', 'buyerDateFrom', 'buyerDateTo', 'activityFilter'
        ));
    }
    
    public function edit(Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        return view('admin.marketers.edit', compact('marketer'));
    }

    public function store(Request $request)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:marketers,email'],
            'password'   => ['required', 'confirmed', Password::min(8)],
            'notes'      => ['nullable', 'string', 'max:500'],
            'is_active'  => ['required', 'in:0,1'],
        ]);

        $marketer = Marketer::create([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'email'           => $request->email,
            'password'        => $request->password,
            'notes'           => $request->notes,
            'is_active'       => $request->is_active,
            'marketing_code'  => Marketer::generateMarketingCode(),
        ]);

        return redirect()
            ->route('admin.marketers.show', $marketer)
            ->with('success', "Marketer created. Code: {$marketer->marketing_code}");
    }
    
    public function update(Request $request, Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:marketers,email,' . $marketer->id],
            'notes'      => ['nullable', 'string', 'max:500'],
            'is_active'  => ['required', 'in:0,1'],
            'password'   => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'notes'      => $request->notes,
            'is_active'  => $request->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $marketer->update($data);

        return back()->with('success', 'Marketer updated successfully.');
    }

    public function suspend(Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $marketer->update(['is_active' => false]);

        return back()->with('success', 'Marketer suspended.');
    }

    public function activate(Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $marketer->update(['is_active' => true]);

        return back()->with('success', 'Marketer activated.');
    }

    public function regenerateCode(Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $marketer->update([
            'marketing_code' => Marketer::generateMarketingCode(),
        ]);

        return back()->with('success', "New code generated: {$marketer->marketing_code}");
    }
}