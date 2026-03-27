<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        if (!auth('admin')->user()->canViewLogs()) abort(403);

        $query = ActivityLog::with('admin');

        if ($request->guard) {
            $query->where('guard_type', $request->guard);
        }

        if ($request->search) {
            $query->where('url', 'like', "%{$request->search}%");
        }

        $logs = $query->latest()->paginate(30)->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }
}