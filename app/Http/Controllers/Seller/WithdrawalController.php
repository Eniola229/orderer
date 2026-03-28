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

        // Resolve account_name from whichever payout type was used
        $accountName = $request->payout_type === 'mobile_money'
            ? $request->momo_account_name
            : $request->account_name;

        $request->merge(['account_name' => $accountName]);

        $request->validate([
            'amount'                => ['required', 'numeric', 'min:10', "max:{$wallet->balance}"],
            'account_name'          => ['required', 'string', 'max:200'],
            'bank_country'          => ['required', 'string'],
            'dollar_capable'        => ['required', 'in:yes,no'],
            'payout_type'           => ['required', 'in:bank_account,mobile_money'],
            'bank_name'             => ['required_if:payout_type,bank_account', 'nullable', 'string', 'max:200'],
            'account_number'        => ['required_if:payout_type,bank_account', 'nullable', 'string', 'max:30'],
            'bank_code'             => ['nullable', 'string', 'max:20'],
            'mobile_money_operator' => ['required_if:payout_type,mobile_money', 'nullable', 'string', 'max:50'],
            'mobile_number'         => ['required_if:payout_type,mobile_money', 'nullable', 'string', 'max:20'],
            'note'                  => ['nullable', 'string', 'max:500'],
        ]);

        if ($wallet->balance < $request->amount) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        // Check no pending withdrawal
        $pending = WithdrawalRequest::where('seller_id', $seller->id)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($pending) {
            return back()->with('error', 'You already have a pending withdrawal request. Please wait for it to be processed.');
        }

        // Derive the destination currency from the country code
        // This is what Korapay will pay out in (converted from your USD balance)
        $currencyMap = [
            'NG'    => 'NGN',
            'GH'    => 'GHS',
            'KE'    => 'KES',
            'ZA'    => 'ZAR',
            'US'    => 'USD',
            'GB'    => 'GBP',
            'OTHER' => 'USD', // fallback — admin should verify
        ];

        $currency = $currencyMap[$request->bank_country] ?? 'USD';

        // If they said the account CAN receive USD, override to USD regardless of country
        if ($request->dollar_capable === 'yes') {
            $currency = 'USD';
        }

        WithdrawalRequest::create([
            'seller_id'             => $seller->id,
            'amount'                => $request->amount,
            'bank_name'             => $request->bank_name,
            'account_name'          => $request->account_name,
            'account_number'        => $request->account_number,
            'bank_code'             => $request->bank_code,
            'country_code'          => $request->bank_country,
            'currency'              => $currency,
            'payout_type'           => $request->payout_type,
            'mobile_money_operator' => $request->mobile_money_operator,
            'mobile_number'         => $request->mobile_number,
            'note'                  => $request->note,
            'status'                => 'pending',
        ]);

        // Debit wallet immediately — holds the amount while request is pending
        $this->wallet->debit(
            $seller,
            (float) $request->amount,
            'withdrawal',
            "Withdrawal request for \${$request->amount}"
        );

        return redirect()->route('seller.withdrawals.index')
            ->with('success', "Withdrawal request of \${$request->amount} submitted. We will process within 24–48 hours.");
    }

    /**
     * Return bank list for a given country code.
     * Called via AJAX when seller selects their country.
     */
    public function getBanks(Request $request)
    {
        $request->validate(['country' => ['required', 'string', 'size:2']]);
        
        try {
            $korapay = app(\App\Services\KorapayService::class);
            
            // Debug: Check if service is instantiated
            \Log::info('Korapay service instantiated', [
                'class' => get_class($korapay)
            ]);
            
            $banks = $korapay->getBanks($request->country);
            
            // Debug: Check what banks are returned
            \Log::info('Banks retrieved', [
                'count' => count($banks),
                'sample' => array_slice($banks, 0, 2)
            ]);
            
            return response()->json(['status' => 'ok', 'banks' => $banks]);
        } catch (\Exception $e) {
            \Log::error('Error in getBanks: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Unable to fetch banks: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Resolve a bank account number against Korapay.
     * Returns confirmed account_name and bank details.
     */
    public function resolveAccount(Request $request)
    {
        $request->validate([
            'bank_code'      => ['required', 'string'],
            'account_number' => ['required', 'string'],
            'country'        => ['required', 'string', 'size:2'],
        ]);

        $currencyMap = ['NG' => 'NGN', 'KE' => 'KES', 'ZA' => 'ZAR', 'GH' => 'GHS'];
        $currency    = $currencyMap[$request->country] ?? null;

        if (!$currency) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Account verification is not supported for this country.',
            ], 422);
        }

        try {
            $korapay = app(\App\Services\KorapayService::class);
            $result  = $korapay->resolveBankAccount(
                $request->bank_code,
                $request->account_number,
                $currency
            );

            return response()->json([
                'status'       => 'ok',
                'account_name' => $result['account_name'],
                'bank_name'    => $result['bank_name'],
                'bank_code'    => $result['bank_code'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(), // now clean e.g. "Invalid account."
            ], 422);
        }
    }
}