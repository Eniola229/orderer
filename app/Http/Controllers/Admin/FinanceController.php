<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\KorapayTransaction;
use App\Models\EscrowHold;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function transactions(Request $request)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);

        $query = WalletTransaction::with(['wallet.walletable']);

        // Filter by transaction type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by owner type (Seller or Buyer)
        if ($request->owner_type) {
            if ($request->owner_type === 'seller') {
                $query->whereHas('wallet', function($q) {
                    $q->where('walletable_type', 'App\Models\Seller');
                });
            } elseif ($request->owner_type === 'buyer') {
                $query->whereHas('wallet', function($q) {
                    $q->where('walletable_type', 'App\Models\User');
                });
            }
        }

        // Search by reference or description
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(25)->withQueryString();

        // Calculate stats based on the same filters (excluding pagination)
        $statsQuery = clone $query;
        $stats = [
            'total_volume'   => $statsQuery->where('status', 'completed')->sum('amount'),
            'in_escrow'      => EscrowHold::where('status', 'held')->sum('amount'),
            'total_released' => EscrowHold::where('status', 'released')->sum('amount'),
        ];

        return view('admin.finance.transactions', compact('transactions', 'stats'));
    }
    public function escrow(Request $request)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);

        $holds = EscrowHold::with('order.user')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return view('admin.finance.escrow', compact('holds'));
    }
}