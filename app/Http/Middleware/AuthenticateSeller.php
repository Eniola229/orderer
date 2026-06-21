<?php

namespace App\Http\Middleware; 

use Closure;
use Illuminate\Http\Request;

class AuthenticateSeller
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('seller')->check()) {
            return redirect()->route('seller.login')
                ->with('error', 'Please login to access your seller dashboard.');
        }

        $seller = auth('seller')->user();

        if (!$seller->is_active) {
            auth('seller')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('seller.login')
                ->with('error', 'Your seller account has been suspended.');
        }

        $verificationRoutes = [
            'seller.verification.notice',
            'seller.verification.verify',
            'seller.verification.resend',
            'seller.verification.update-email',
            'seller.phone-verification.notice',
            'seller.phone-verification.send',
            'seller.phone-verification.verify',
            'seller.phone-verification.update-phone',
            'seller.logout',
        ];

        if (!$seller->email_verified_at && !in_array($request->route()->getName(), $verificationRoutes)) {
            return redirect()->route('seller.verification.notice');
        }

        if (!$seller->phone_verified_at && !in_array($request->route()->getName(), $verificationRoutes)) {
            return redirect()->route('seller.phone-verification.notice');
        }

        $allowedRoutes = ['seller.pending', 'seller.resubmit'];

        if ((!$seller->is_approved || $seller->verification_status === 'rejected') && !in_array($request->route()->getName(), $allowedRoutes)) {
            return redirect()->route('seller.pending')
                ->with('info', $seller->verification_status === 'rejected'
                    ? 'Your account was not approved. Please update your information and resubmit.'
                    : 'Your account is awaiting approval.');
        }

        return $next($request);
    }
}