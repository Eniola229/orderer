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

        if (!$seller->is_approved && !$request->routeIs('seller.pending')) { // ← add this check
            return redirect()->route('seller.pending')
                ->with('info', 'Your account is awaiting approval.');
        }

        return $next($request);
    }
}