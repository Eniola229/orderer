<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminRole
{
    /**
     * Usage in routes:
     *   ->middleware('admin.role:super_admin')
     *   ->middleware('admin.role:finance_admin,super_admin')
     *   ->middleware('admin.role:canManageFinance')   ← method name on Admin model
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return redirect()->route('admin.login');
        }

        foreach ($roles as $role) {
            // If it looks like a method name (camelCase starting with 'can')
            if (str_starts_with($role, 'can') && method_exists($admin, $role)) {
                if ($admin->$role()) {
                    return $next($request);
                }
                continue;
            }

            // Direct role match
            if ($admin->role === $role) {
                return $next($request);
            }
        }

        // Denied
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Insufficient permissions.'], 403);
        }

        abort(403, 'You do not have permission to access this area.');
    }
}