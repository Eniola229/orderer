<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestMarketer
{
    public function handle(Request $request, Closure $next)
    {
        if (auth('marketer')->check()) {
            return redirect()->route('marketer.dashboard');
        }

        return $next($request);
    }
}