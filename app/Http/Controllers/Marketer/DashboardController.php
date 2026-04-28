<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $marketer = auth('marketer')->user();

        // ── Date filter ───────────────────────────────────────────────────────
        $from = $request->from ? Carbon::parse($request->from)->startOfDay() : null;
        $to   = $request->to   ? Carbon::parse($request->to)->endOfDay()     : null;

        // ── Base query ────────────────────────────────────────────────────────
        $query = $marketer->referredSellers()
            ->with('documents')  // eager load if you show verification status
            ->latest();

        if ($from) $query->where('created_at', '>=', $from);
        if ($to)   $query->where('created_at', '<=', $to);

        // ── Stats ──────────────────────────────────────────────────────────────
        $totalQuery    = $marketer->referredSellers();
        $approvedQuery = (clone $totalQuery)->where('is_approved', true);
        $pendingQuery  = (clone $totalQuery)->where('is_approved', false);
        $thisMonthQuery= (clone $totalQuery)->whereMonth('created_at', now()->month)
                                            ->whereYear('created_at', now()->year);

        // Apply date filter to stats too when set
        if ($from || $to) {
            foreach ([$totalQuery, $approvedQuery, $pendingQuery] as $q) {
                if ($from) $q->where('created_at', '>=', $from);
                if ($to)   $q->where('created_at', '<=', $to);
            }
        }

        $stats = [
            'total'      => $totalQuery->count(),
            'approved'   => $approvedQuery->count(),
            'pending'    => $pendingQuery->count(),
            'this_month' => $thisMonthQuery->count(),
        ];

        $sellers = $query->paginate(20)->withQueryString();

        return view('marketer.dashboard', compact('marketer', 'sellers', 'stats', 'from', 'to'));
    }

    /**
     * Generate (or regenerate) the marketer's marketing code.
     */
    public function generateCode()
    {
        $marketer = auth('marketer')->user();

        // Allow regeneration only if they don't have one yet,
        // or you can allow it always — your call.
        if (!$marketer->marketing_code) {
            $marketer->update([
                'marketing_code' => \App\Models\Marketer::generateMarketingCode(),
            ]);
        }

        return back()->with('success', 'Your marketing code is ready: ' . $marketer->marketing_code);
    }

    /**
     * Regenerate — always generates a fresh code.
     */
    public function regenerateCode()
    {
        $marketer = auth('marketer')->user();

        $marketer->update([
            'marketing_code' => \App\Models\Marketer::generateMarketingCode(),
        ]);

        return back()->with('success', 'New marketing code generated: ' . $marketer->marketing_code);
    }

    public function profile()
    {
        $marketer = auth('marketer')->user();
        return view('marketer.profile', compact('marketer'));
    }
}