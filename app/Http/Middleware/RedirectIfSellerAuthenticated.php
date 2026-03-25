<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfSellerAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (auth('seller')->check()) {
            $seller = auth('seller')->user();

            // If not approved yet, send to pending instead of dashboard
            if (!$seller->is_approved) {
                return redirect()->route('seller.pending');
            }

            return redirect()->route('seller.dashboard');
        }

        return $next($request);
    }
}