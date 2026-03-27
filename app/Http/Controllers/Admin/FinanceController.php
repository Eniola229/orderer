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

        $transactions = WalletTransaction::with('wallet.owner')
            ->latest()
            ->paginate(25);

        $stats = [
            'total_volume'   => KorapayTransaction::where('status', 'success')->sum('amount'),
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