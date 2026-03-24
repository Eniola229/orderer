<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAdminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (auth('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return $next($request);
    }
}