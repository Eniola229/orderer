<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\DeliveryBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $marketer = auth('marketer')->user();

        // ── Date filter ───────────────────────────────────────────────────
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : null;
        $to   = $request->to   ? Carbon::parse($request->to)->endOfDay()     : null;

        // ── Seller queries ────────────────────────────────────────────────
        $sellerQuery = $marketer->referredSellers()->with('documents')->latest();
        if ($from) $sellerQuery->where('created_at', '>=', $from);
        if ($to)   $sellerQuery->where('created_at', '<=', $to);

        $totalSellerQ    = $marketer->referredSellers();
        $approvedQ       = (clone $totalSellerQ)->where('is_approved', true);
        $pendingQ        = (clone $totalSellerQ)->where('is_approved', false);
        $thisMonthSellerQ= (clone $totalSellerQ)->whereMonth('created_at', now()->month)
                                                ->whereYear('created_at',  now()->year);

        if ($from || $to) {
            foreach ([$totalSellerQ, $approvedQ, $pendingQ] as $q) {
                if ($from) $q->where('created_at', '>=', $from);
                if ($to)   $q->where('created_at', '<=', $to);
            }
        }

        $sellerStats = [
            'total'      => $totalSellerQ->count(),
            'approved'   => $approvedQ->count(),
            'pending'    => $pendingQ->count(),
            'this_month' => $thisMonthSellerQ->count(),
        ];

        $sellers = $sellerQuery->paginate(20, ['*'], 'sellers_page')->withQueryString();

        // ── Buyer queries ─────────────────────────────────────────────────
        $buyerBaseQuery = $marketer->referredBuyers();

        $buyerQuery = $marketer->referredBuyers()->latest();
        if ($from) $buyerQuery->where('created_at', '>=', $from);
        if ($to)   $buyerQuery->where('created_at', '<=', $to);

        // IDs of ALL referred buyers (unfiltered) for order/booking counts
        $allBuyerIds = (clone $buyerBaseQuery)->pluck('id');

        // IDs of date-filtered buyers
        $filteredBuyerQuery = $marketer->referredBuyers();
        if ($from) $filteredBuyerQuery->where('created_at', '>=', $from);
        if ($to)   $filteredBuyerQuery->where('created_at', '<=', $to);
        $filteredBuyerIds = $filteredBuyerQuery->pluck('id');

        $buyerStats = [
            'total'            => $filteredBuyerIds->count(),
            'this_month'       => $marketer->referredBuyers()
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at',  now()->year)
                                    ->count(),
            // How many of those buyers have placed at least one order
            'with_orders'      => Order::whereIn('user_id', $filteredBuyerIds)
                                    ->distinct('user_id')
                                    ->count('user_id'),
            // How many of those buyers have booked at least one delivery
            'with_bookings'    => DeliveryBooking::whereIn('user_id', $filteredBuyerIds)
                                    ->distinct('user_id')
                                    ->count('user_id'),
            // Total orders placed by referred buyers
            'total_orders'     => Order::whereIn('user_id', $filteredBuyerIds)->count(),
            // Total bookings placed by referred buyers
            'total_bookings'   => DeliveryBooking::whereIn('user_id', $filteredBuyerIds)->count(),
        ];

        $buyers = $buyerQuery->paginate(20, ['*'], 'buyers_page')->withQueryString();

        // For the buyers table — attach order & booking counts per buyer
        $buyerIds        = $buyers->pluck('id');
        $orderCounts     = Order::whereIn('user_id', $buyerIds)
                                ->selectRaw('user_id, count(*) as total')
                                ->groupBy('user_id')
                                ->pluck('total', 'user_id');
        $bookingCounts   = DeliveryBooking::whereIn('user_id', $buyerIds)
                                ->selectRaw('user_id, count(*) as total')
                                ->groupBy('user_id')
                                ->pluck('total', 'user_id');

        return view('marketer.dashboard', compact(
            'marketer',
            'sellers', 'sellerStats',
            'buyers',  'buyerStats',
            'orderCounts', 'bookingCounts',
            'from', 'to'
        ));
    }

    public function generateCode()
    {
        $marketer = auth('marketer')->user();

        if (!$marketer->marketing_code) {
            $marketer->update([
                'marketing_code' => \App\Models\Marketer::generateMarketingCode(),
            ]);
        }

        return back()->with('success', 'Your marketing code is ready: ' . $marketer->marketing_code);
    }

    public function regenerateCode()
    {
        $marketer = auth('marketer')->user();

        $marketer->update([
            'marketing_code' => \App\Models\Marketer::generateMarketingCode(),
        ]);

        return back()->with('success', 'New marketing code generated: ' . $marketer->marketing_code);
    }

    public function profile()
    {
        $marketer = auth('marketer')->user();
        return view('marketer.profile', compact('marketer'));
    }
}