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

        // Allow access to pending page and resubmit route if account is pending OR rejected
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