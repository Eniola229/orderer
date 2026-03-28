<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\ReferralEarning;
use App\Models\User;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    // How much each referrer earns per successful first order
    const REFERRER_REWARD = 2.00; // USD
    const REFERRED_REWARD = 1.00; // USD — new user reward on first order

    /**
     * Register a referral when a new user signs up with a referral code.
     */
    public function registerReferral(User $newUser, string $referralCode): void
    {
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer || $referrer->id === $newUser->id) return;

        Referral::firstOrCreate([
            'referrer_type' => 'App\Models\User',
            'referrer_id'   => $referrer->id,
            'referred_type' => 'App\Models\User',
            'referred_id'   => $newUser->id,
            'referral_code' => $referralCode,
        ]);
    }

    /**
     * Process referral earnings when a buyer places their first completed order.
     * Call this after an order moves to 'completed' status.
     */
    public function processFirstOrderReward(Order $order): void
    {
        $user = $order->user;

        // Only trigger if this is their first completed order
        $completedCount = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        if ($completedCount > 1) return;

        // Find referral
        $referral = Referral::where('referred_type', 'App\Models\User')
            ->where('referred_id', $user->id)
            ->first();

        if (!$referral) return;

        // Check not already rewarded
        if (ReferralEarning::where('referral_id', $referral->id)->where('status', 'credited')->exists()) {
            return;
        }

        DB::transaction(function () use ($referral, $user) {
            $walletService = app(WalletService::class);

            // 1. Reward referrer
            $referrer = User::find($referral->referrer_id);
            if ($referrer) {
                $walletService->credit(
                    $referrer,
                    self::REFERRER_REWARD,
                    'referral_credit',
                    "Referral reward — {$user->first_name} placed their first order."
                );

                ReferralEarning::create([
                    'referral_id'  => $referral->id,
                    'amount'       => self::REFERRER_REWARD,
                    'currency'     => 'USD',
                    'triggered_by' => 'first_order',
                    'status'       => 'credited',
                    'credited_at'  => now(),
                ]);

                Notification::create([
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id'   => $referrer->id,
                    'type'            => 'referral_earned',
                    'title'           => 'Referral Reward!',
                    'body'            => "You earned \$" . self::REFERRER_REWARD . " because {$user->first_name} placed their first order.",
                    'action_url'      => route('buyer.referral'),
                ]);
            }

            // 2. Reward the new user (referred person)
            $walletService->credit(
                $user,
                self::REFERRED_REWARD,
                'referral_credit',
                'Welcome reward for first order via referral.'
            );

            Notification::create([
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $user->id,
                'type'            => 'referral_bonus',
                'title'           => 'Welcome Bonus!',
                'body'            => "You received a \$" . self::REFERRED_REWARD . " bonus for your first order.",
                'action_url'      => route('buyer.wallet'),
            ]);
        });
    }
}