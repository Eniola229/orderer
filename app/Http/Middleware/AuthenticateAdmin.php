<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            return redirect()->route('admin.login')
                ->with('error', 'Admin access required.');
        }

        $admin = auth('admin')->user();

        if (!$admin->is_active) {
            auth('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')
                ->with('error', 'This admin account has been disabled.');
        }

        return $next($request);
    }
}