<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Seller; 
use App\Models\Admin;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        if (!auth('admin')->user()->canViewLogs()) abort(403);

        $query = ActivityLog::query();

        // Apply guard filter
        if ($request->guard && $request->guard !== '') {
            $query->where('guard_type', $request->guard);
        }

        // Apply method filter
        if ($request->method && $request->method !== '') {
            $query->where('method', $request->method);
        }

        // Apply status code filter
        if ($request->status_code && $request->status_code !== '') {
            $query->where('status_code', $request->status_code);
        }

        // Search by URL, user email, or user ID
        if ($request->search) {
            $search = $request->search;
            
            // Check if search is a UUID (user ID)
            $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $search);
            
            if ($isUuid) {
                // Search by guard ID directly
                $query->where('guard_id', $search);
            } else {
                // Search by URL or find users by email/name
                $userIds = collect();
                
                // Search in users (buyers)
                $buyers = User::where('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->get();
                
                // Search in sellers
                $sellers = Seller::where('email', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->get();
                
                // Search in admins
                $admins = Admin::where('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->get();
                
                // Collect all IDs
                $userIds = $buyers->pluck('id')->merge($sellers->pluck('id'))->merge($admins->pluck('id'));
                
                if ($userIds->isNotEmpty()) {
                    $query->where(function($q) use ($userIds, $search) {
                        $q->whereIn('guard_id', $userIds)
                          ->orWhere('url', 'like', "%{$search}%");
                    });
                } else {
                    $query->where('url', 'like', "%{$search}%");
                }
            }
        }

        // Get paginated logs for display
        $logs = $query->latest()->paginate(30)->withQueryString();

        // Calculate stats from the SAME filtered query (without pagination)
        $statsQuery = clone $query;
        $stats = [
            'total'  => $statsQuery->count(),
            'admin'  => (clone $statsQuery)->where('guard_type', 'admin')->count(),
            'seller' => (clone $statsQuery)->where('guard_type', 'seller')->count(),
            'buyer'  => (clone $statsQuery)->where('guard_type', 'buyer')->count(),
        ];

        return view('admin.logs.index', compact('logs', 'stats'));
    }
}