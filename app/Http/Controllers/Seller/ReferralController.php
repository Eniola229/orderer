<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\ReferralEarning;

class ReferralController extends Controller
{
    public function index()
    {
        $seller = auth('seller')->user();

        $referrals = Referral::where('referrer_type', 'App\Models\Seller')
            ->where('referrer_id', $seller->id)
            ->with(['referred', 'earnings'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_referrals'  => Referral::where('referrer_type', 'App\Models\Seller')
                                           ->where('referrer_id', $seller->id)
                                           ->count(),
            'total_earned'     => ReferralEarning::whereHas('referral', function ($q) use ($seller) {
                                        $q->where('referrer_type', 'App\Models\Seller')
                                          ->where('referrer_id', $seller->id);
                                    })->where('status', 'credited')->sum('amount'),
            'pending_earnings' => ReferralEarning::whereHas('referral', function ($q) use ($seller) {
                                        $q->where('referrer_type', 'App\Models\Seller')
                                          ->where('referrer_id', $seller->id);
                                    })->where('status', 'pending')->sum('amount'),
        ];

        return view('seller.referral.index', compact('seller', 'referrals', 'stats'));
    }
}