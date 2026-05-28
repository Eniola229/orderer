<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    // Show the "please verify your email" page
    public function notice()
    {
        $seller = auth('seller')->user();

        if ($seller->email_verified_at) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.auth.verify-email');
    }

    // Handle the verification link click
    public function verify(Request $request, string $id, string $hash)
    {
        $seller = \App\Models\Seller::findOrFail($id);

        // Make sure the hash matches
        if (! hash_equals(sha1($seller->email), $hash)) {
            abort(403, 'Invalid verification link.');
        }

        if ($seller->email_verified_at) {
            return redirect()->route('seller.dashboard')->with('info', 'Email already verified.');
        }

        $seller->email_verified_at = now();
        $seller->save();

        // Log them in if they aren't already
        auth('seller')->login($seller);

        return redirect()->route('seller.dashboard')->with('success', 'Email verified! Welcome to Orderer.');
    }

    // Resend the verification email
    public function resend(Request $request)
    {
        $seller = auth('seller')->user();

        if ($seller->email_verified_at) {
            return back()->with('info', 'Your email is already verified.');
        }

        $this->sendVerificationEmail($seller);

        return back()->with('success', 'Verification email resent! Check your inbox.');
    }

    
    public static function sendVerificationEmail($seller): void
    {
        app(\App\Services\BrevoMailService::class)->sendSellerVerification($seller);
    }
}