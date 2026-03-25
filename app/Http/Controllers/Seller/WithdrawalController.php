<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct(
        protected WalletService   $walletService,
        protected BrevoMailService $brevo
    ) {}

    public function index()
    {
        $withdrawals = WithdrawalRequest::where('seller_id', auth('seller')->id())
            ->latest()
            ->paginate(15);

        return view('seller.withdrawals.index', compact('withdrawals'));
    }

    public function create()
    {
        $seller = auth('seller')->user();
        $wallet = $this->walletService->getOrCreate($seller);

        if ($wallet->balance < 10) {
            return redirect()->route('seller.wallet.index')
                ->with('error', 'Minimum $10.00 balance required to withdraw.');
        }

        // Prevent multiple pending requests
        $hasPending = WithdrawalRequest::where('seller_id', $seller->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return redirect()->route('seller.withdrawals.index')
                ->with('error', 'You already have a pending withdrawal request.');
        }

        return view('seller.withdrawals.create', compact('wallet'));
    }

    public function store(Request $request)
    {
        $seller = auth('seller')->user();

        $request->validate([
            'amount'          => ['required', 'numeric', 'min:10'],
            'bank_name'       => ['required', 'string', 'max:200'],
            'account_number'  => ['required', 'string', 'max:30'],
            'account_name'    => ['required', 'string', 'max:200'],
            'bank_country'    => ['required', 'string', 'max:5'],
            'dollar_capable'  => ['required', 'in:yes,no'],
            'swift_code'      => ['nullable', 'string', 'max:20'],
        ]);

        $wallet = $this->walletService->getOrCreate($seller);

        if ($wallet->balance < $request->amount) {
            return back()->withErrors(['amount' => 'Amount exceeds your available balance.']);
        }

        // Debit the wallet immediately and hold
        $this->walletService->debit(
            $seller,
            $request->amount,
            'withdrawal',
            "Withdrawal request — pending processing"
        );

        WithdrawalRequest::create([
            'seller_id'       => $seller->id,
            'amount'          => $request->amount,
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'account_name'    => $request->account_name,
            'bank_country'    => $request->bank_country,
            'dollar_capable'  => $request->dollar_capable === 'yes',
            'swift_code'      => $request->swift_code,
            'status'          => 'pending',
        ]);

        return redirect()->route('seller.withdrawals.index')
            ->with('success', 'Withdrawal request submitted. Processing within 1-3 business days.');
    }
}