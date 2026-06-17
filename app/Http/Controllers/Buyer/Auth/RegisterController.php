<?php

namespace App\Http\Controllers\Buyer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Marketer;
use App\Models\User;
use App\Models\Referral;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('buyer.auth.register');
    }

    public function register(Request $request, BrevoMailService $brevo)
    {
        $request->validate([
            'first_name'   => ['required', 'string', 'max:100'],
            'last_name'    => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'password'     => ['required', 'confirmed', Password::min(8)],
            'terms'        => ['accepted'],
        ]);

        // ── Referral / Marketer code resolution ───────────────────────────
        $referredBy   = null;
        $marketerId   = null;
        $referralCode = $request->referral_code;

        if ($referralCode) {
            if (str_starts_with(strtoupper($referralCode), 'OR-MRT-')) {
                // Marketer code — tag the marketer, no Referral record
                $marketer = Marketer::where('marketing_code', strtoupper($referralCode))
                                    ->where('is_active', true)
                                    ->first();
                if ($marketer) {
                    $marketerId = $marketer->id;
                }
            } else {
                // Buyer referral code — another user referred them
                $referrer = User::where('referral_code', $referralCode)->first();
                if ($referrer) {
                    $referredBy = $referrer->id;
                }
            }
        }

        // Capture BEFORE login regenerates the session
        $oldSessionId = session()->getId();

        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'password'      => $request->password,
            'referral_code' => strtoupper(Str::random(8)),
            'referred_by'   => $referredBy,
            'marketer_id'   => $marketerId,
            'is_active'     => true,
        ]);

        // ── Create Referral record (Buyer referrer only, not marketer) ────────
        // Check if there was a valid buyer referrer (not a marketer)
        if ($referredBy) {
            // Need to get the referrer user to access their details
            $referrerUser = User::find($referredBy);
            if ($referrerUser) {
                Referral::create([
                    'referrer_type' => 'App\Models\User',
                    'referrer_id'   => $referrerUser->id,
                    'referred_type' => 'App\Models\User',
                    'referred_id'   => $user->id,
                    'referral_code' => $referralCode, 
                ]);
            }
        }
        $brevo->sendWelcomeBuyer($user);

        auth('web')->login($user);

        app(\App\Http\Controllers\CartController::class)->mergeGuestCart($oldSessionId);

        return redirect()->route('home')
            ->with('success', "Welcome to Orderer, {$user->first_name}!");
    }
}