<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\KorapayTransaction;
use App\Models\MonnifyTransaction;
use App\Services\WalletService;
use App\Services\KorapayService;
use App\Services\MonnifyService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService  $walletService,
        protected KorapayService $korapay,
        protected MonnifyService $monnify
    ) {}

    public function index()
    {
        $user   = auth('web')->user();
        $wallet = $this->walletService->getOrCreate($user);

        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->latest()
            ->paginate(20);

        return view('buyer.wallet.index', compact('user', 'wallet', 'transactions'));
    }

    // -------------------------------------------------------------------------
    // Monnify — Step 1: called via AJAX before the SDK modal opens
    // -------------------------------------------------------------------------

    public function monnifyInit(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
        ]);

        $user      = auth('web')->user();
        $amount    = (float) $request->amount;
        $reference = $this->monnify->generateReference('BUY');

        // Save pending record — but do NOT call Monnify's init-transaction API
        // The SDK will initialize the transaction itself using your reference
        $this->monnify->createTransaction($user, $amount, 'wallet_topup', $reference);

        // Just return the config the SDK needs
        return response()->json([
            'paymentReference' => $reference,
            'amount'           => $amount,
            'customerName'     => $user->full_name,
            'email'            => $user->email,
            'apiKey'           => config('services.monnify.api_key'),
            'contractCode'     => config('services.monnify.contract_code'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Monnify — Step 2: called via AJAX after SDK onComplete fires
    // -------------------------------------------------------------------------

    public function monnifyVerify(Request $request)
    {
        $request->validate([
            'reference' => ['required', 'string'],
        ]);

        $reference = $request->reference;

        $txn = MonnifyTransaction::where('reference', $reference)->first()
            ?? MonnifyTransaction::where('monnify_reference', $reference)->first();

        if (!$txn) {
            return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
        }

        // ── Already credited — return success so the UI updates correctly ──
        if ($txn->status === 'success') {
            return response()->json([
                'success' => true,
                'message' => '₦' . number_format($txn->amount, 2) . ' has been added to your wallet.',
            ]);
        }

        try {
            $data = $this->monnify->verifyTransaction($txn->reference);

            \Log::info('Monnify verify response', [
                'reference'     => $reference,
                'paymentStatus' => $data['paymentStatus'] ?? 'unknown',
                'data'          => $data,
            ]);

            if (($data['paymentStatus'] ?? '') === 'PAID') {
                $user   = auth('web')->user();
                $amount = (float) ($data['amountPaid'] ?? $txn->amount);

                $this->walletService->credit(
                    $user,
                    $amount,
                    'credit',
                    "Wallet top-up via Monnify — ref: {$txn->reference}"
                );

                $txn->update([
                    'status'            => 'success',
                    'monnify_reference' => $data['transactionReference'] ?? null,
                    'gateway_response'  => $data,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => '₦' . number_format($amount, 2) . ' has been added to your wallet.',
                ]);
            }

            $txn->update(['status' => 'failed', 'gateway_response' => $data]);

            return response()->json([
                'success' => false,
                'message' => 'Payment status: ' . ($data['paymentStatus'] ?? 'unknown'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Monnify verify error', [
                'error'     => $e->getMessage(),
                'reference' => $reference,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not verify payment. Contact support if you were charged.',
            ], 500);
        }
    }
    // -------------------------------------------------------------------------
    // Korapay — redirect flow (unchanged)
    // -------------------------------------------------------------------------

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
        ]);

        $user      = auth('web')->user();
        $amount    = (float) $request->amount;
        $reference = $this->korapay->generateReference('BUY');

        $this->korapay->createTransaction($user, $amount, 'wallet_topup', $reference);

        $checkoutData = $this->korapay->initializeCheckout(
            $user->email,
            $user->full_name,
            $amount,
            $reference,
            route('buyer.wallet.callback'),
            '',
            ['type' => 'wallet_topup', 'user_id' => $user->id]
        );

        return redirect($checkoutData['checkout_url']);
    }

    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('buyer.wallet')
                ->with('error', 'Invalid payment reference.');
        }

        $txn = KorapayTransaction::where('reference', $reference)->first();

        if (!$txn || $txn->status === 'success') {
            return redirect()->route('buyer.wallet')
                ->with('info', 'Transaction already processed.');
        }

        try {
            $data = $this->korapay->verifyTransaction($reference);

            if ($data['status'] === 'success') {
                $user   = auth('web')->user();
                $amount = (float) $data['amount'];

                $this->walletService->credit(
                    $user,
                    $amount,
                    'credit',
                    "Wallet top-up via Korapay — ref: {$reference}"
                );

                $txn->update([
                    'status'            => 'success',
                    'korapay_reference' => $data['payment_reference'] ?? null,
                    'gateway_response'  => $data,
                ]);

                return redirect()->route('buyer.wallet')
                    ->with('success', '₦' . number_format($amount, 2) . ' has been added to your wallet.');
            }

            $txn->update(['status' => 'failed', 'gateway_response' => $data]);

            return redirect()->route('buyer.wallet')
                ->with('error', 'Payment was not successful.');

        } catch (\Exception $e) {
            return redirect()->route('buyer.wallet')
                ->with('error', 'Could not verify payment. Contact support if charged.');
        }
    }
}