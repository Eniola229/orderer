<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMarketer
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('marketer')->check()) {
            return redirect()->route('marketer.login');
        }

        if (!auth('marketer')->user()->is_active) {
            auth('marketer')->logout();
            return redirect()->route('marketer.login')
                ->with('error', 'Your account has been deactivated. Contact the admin.');
        }

        return $next($request);
    }
}