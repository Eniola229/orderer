<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\ReferralEarning;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth('web')->user();

        $referrals = Referral::where('referrer_type', 'App\Models\User')
            ->where('referrer_id', $user->id)
            ->with(['referred', 'earnings'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_referrals'   => Referral::where('referrer_type', 'App\Models\User')
                                            ->where('referrer_id', $user->id)->count(),
            'total_earned'      => ReferralEarning::whereHas('referral', function($q) use ($user) {
                                        $q->where('referrer_type', 'App\Models\User')
                                          ->where('referrer_id', $user->id);
                                    })->where('status', 'credited')->sum('amount'),
            'pending_earnings'  => ReferralEarning::whereHas('referral', function($q) use ($user) {
                                        $q->where('referrer_type', 'App\Models\User')
                                          ->where('referrer_id', $user->id);
                                    })->where('status', 'pending')->sum('amount'),
        ];

        return view('buyer.referral.index', compact('user', 'referrals', 'stats'));
    }
}
