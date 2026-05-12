<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct(protected WalletService $wallet) {}

    public function index()
    {
        $withdrawals = WithdrawalRequest::where('seller_id', auth('seller')->id())
            ->latest()
            ->paginate(20);

        $wallet = $this->wallet->getOrCreate(auth('seller')->user());

        return view('seller.withdrawals.index', compact('withdrawals', 'wallet'));
    }

    public function create()
    {
        $wallet = $this->wallet->getOrCreate(auth('seller')->user());
        return view('seller.withdrawals.create', compact('wallet'));
    }

    public function store(Request $request)
    {
        $seller = auth('seller')->user();
        $wallet = $this->wallet->getOrCreate($seller);

        $request->validate([
            'amount'         => ['required', 'numeric', 'min:1000', "max:{$wallet->balance}"],
            'bank_code'      => ['required', 'string', 'max:20'],
            'bank_name'      => ['required', 'string', 'max:200'],
            'account_number' => ['required', 'string', 'max:10'],
            'account_name'   => ['required', 'string', 'max:200'],
            'note'           => ['nullable', 'string', 'max:500'],
        ]);

        if ($wallet->balance < $request->amount) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        // Block duplicate pending requests
        $pending = WithdrawalRequest::where('seller_id', $seller->id)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($pending) {
            return back()->with('error', 'You already have a pending withdrawal request. Please wait for it to be processed.');
        }

        WithdrawalRequest::create([
            'seller_id'      => $seller->id,
            'amount'         => $request->amount,
            'bank_name'      => $request->bank_name,
            'account_name'   => $request->account_name,
            'account_number' => $request->account_number,
            'bank_code'      => $request->bank_code,
            'country_code'   => 'NG',
            'currency'       => 'NGN',
            'payout_type'    => 'bank_account',
            'note'           => $request->note,
            'status'         => 'pending',
        ]);

        // Debit wallet immediately to hold funds while request is pending
        $this->wallet->debit(
            $seller,
            (float) $request->amount,
            'withdrawal',
            "Withdrawal request for ₦{$request->amount}"
        );

        return redirect()->route('seller.withdrawals.index')
            ->with('success', "Withdrawal request of ₦" . number_format($request->amount, 2) . " submitted. We will process within 2–3 hours.");
    }

    /**
     * Return Nigerian bank list via Monnify.
     * Called via AJAX on page load.
     */
    public function getBanks(Request $request)
    {
        // We only support Nigeria — ignore any country param
        try {
            $monnify = app(\App\Services\MonnifyService::class);
            $raw     = $monnify->getBanks();

            // Monnify returns: [{ name, code, ussdTemplate, baseUssdCode, transferUssdTemplate }, ...]
            $banks = collect($raw)->map(fn($b) => [
                'name' => $b['name'],
                'code' => $b['code'],
            ])->sortBy('name')->values()->all();

            return response()->json(['status' => 'ok', 'banks' => $banks]);

        } catch (\Exception $e) {
            \Log::error('Monnify getBanks error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Unable to fetch banks. Please refresh and try again.',
            ], 422);
        }
    }

    /**
     * Resolve a Nigerian bank account number via Monnify.
     * Returns confirmed account_name.
     */
    public function resolveAccount(Request $request)
    {
        $request->validate([
            'bank_code'      => ['required', 'string'],
            'account_number' => ['required', 'string', 'min:10', 'max:11'],
        ]);

        try {
            $monnify = app(\App\Services\MonnifyService::class);

            $result = $monnify->resolveBankAccount(
                $request->bank_code,
                $request->account_number
            );

            \Log::info('Monnify resolveAccount result', [
                'bank_code'      => $request->bank_code,
                'account_number' => $request->account_number,
                'result'         => $result,
            ]);

            // Monnify responseBody keys: accountName, accountNumber, bankCode, bankName
            // Guard against empty/null responseBody
            if (empty($result) || empty($result['accountName'])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Account not found. Check the account number and selected bank.',
                ], 422);
            }

            return response()->json([
                'status'       => 'ok',
                'account_name' => $result['accountName'],
                'bank_name'    => $result['bankName'] ?? '',
                'bank_code'    => $result['bankCode'] ?? $request->bank_code,
            ]);

        } catch (\Exception $e) {
            \Log::error('Monnify resolveAccount error', [
                'bank_code'      => $request->bank_code,
                'account_number' => $request->account_number,
                'error'          => $e->getMessage(),
            ]);

            // Monnify sends back responseMessage in the exception — pick it out cleanly
            $raw = $e->getMessage();

            $msg = match(true) {
                str_contains($raw, 'Invalid account')           => 'Account not found. Check the account number and selected bank.',
                str_contains($raw, 'Account not found')         => 'Account not found. Check the account number and selected bank.',
                str_contains($raw, 'Invalid bank')              => 'Invalid bank selected. Please try again.',
                str_contains($raw, 'Required request parameter')=> 'Verification failed — please check bank and account number.',
                str_contains($raw, 'authentication')            => 'Service temporarily unavailable. Please try again shortly.',
                str_contains($raw, '401')                       => 'Service temporarily unavailable. Please try again shortly.',
                default                                         => 'Verification failed. Please try again.',
            };

            return response()->json([
                'status'  => 'error',
                'message' => $msg,
            ], 422);
        }
    }
}