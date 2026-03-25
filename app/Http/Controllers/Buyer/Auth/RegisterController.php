<?php

namespace App\Http\Controllers\Buyer\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'first_name'            => ['required', 'string', 'max:100'],
            'last_name'             => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'password'              => ['required', 'confirmed', Password::min(8)],
            'terms'                 => ['accepted'],
        ]);

        $referredBy = null;
        if ($request->referral_code) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        $user = User::create([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'password'       => $request->password,
            'referral_code'  => strtoupper(Str::random(8)),
            'referred_by'    => $referredBy,
            'is_active'      => true,
        ]);

        // Send welcome email via Brevo
        $brevo->sendWelcomeBuyer($user);

        // Log them in
        auth('web')->login($user);

        return redirect()->route('home')
            ->with('success', "Welcome to Orderer, {$user->first_name}!");
    }
}