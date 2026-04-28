<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthBuyer
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('web')->check()) {
            // redirect()->guest() stores the intended URL in the session
            // so redirect()->intended() in the LoginController can pick it up
            return redirect()->guest(route('buyer.login'));
        }

        if (!auth('web')->user()->is_active) {
            auth('web')->logout();
            return redirect()->route('buyer.login')
                ->with('error', 'Your account has been suspended. Contact support.');
        }

        return $next($request);
    }
}