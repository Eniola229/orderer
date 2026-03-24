<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfSellerAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (auth('seller')->check()) {
            return redirect()->route('seller.dashboard');
        }
        return $next($request);
    }
}