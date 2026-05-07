<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Models\KorapayTransaction;
use App\Models\MonnifyTransaction;
use App\Services\WalletService;
use App\Services\KorapayService;
use App\Services\MonnifyService;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService    $walletService,
        protected KorapayService   $korapay,
        protected MonnifyService   $monnify,
        protected BrevoMailService $brevo
    ) {}

    public function index()
    {
        $seller = auth('seller')->user();
        $wallet = $this->walletService->getOrCreate($seller);

        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->latest()
            ->paginate(20);

        $withdrawals = WithdrawalRequest::where('seller_id', $seller->id)
            ->latest()
            ->paginate(10);

        $escrowBalance = \App\Models\EscrowHold::where('seller_id', $seller->id)
            ->where('status', 'held')
            ->sum('seller_amount');

        return view('seller.wallet.index', compact(
            'seller', 'wallet', 'transactions', 'withdrawals', 'escrowBalance'
        ));
    }

    // -------------------------------------------------------------------------
    // Monnify — Step 1: AJAX before SDK modal opens
    // -------------------------------------------------------------------------

    public function monnifyInit(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'type'   => ['required', 'in:wallet_topup,ads_topup'],
        ]);

        $seller    = auth('seller')->user();
        $amount    = (float) $request->amount;
        $type      = $request->type;
        $reference = $this->monnify->generateReference('SEL');

        // Save pending record — SDK initializes the transaction itself
        $this->monnify->createTransaction($seller, $amount, $type, $reference);

        return response()->json([
            'paymentReference' => $reference,
            'amount'           => $amount,
            'customerName'     => $seller->full_name,
            'email'            => $seller->email,
            'apiKey'           => config('services.monnify.api_key'),
            'contractCode'     => config('services.monnify.contract_code'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Monnify — Step 2: AJAX after SDK onComplete fires
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

        // Already credited — idempotent response
        if ($txn->status === 'success') {
            return response()->json([
                'success' => true,
                'message' => '₦' . number_format($txn->amount, 2) . ' has been added to your wallet.',
            ]);
        }

        try {
            $data = $this->monnify->verifyTransaction($txn->reference);

            \Log::info('Seller Monnify verify response', [
                'reference'     => $reference,
                'paymentStatus' => $data['paymentStatus'] ?? 'unknown',
            ]);

            if (($data['paymentStatus'] ?? '') === 'PAID') {
                $seller = auth('seller')->user();
                $amount = (float) ($data['amountPaid'] ?? $txn->amount);

                if ($txn->type === 'ads_topup') {
                    $this->walletService->topupAdsBalance($seller, $amount);
                    $message = '₦' . number_format($amount, 2) . ' has been added to your ads balance.';
                } else {
                    $this->walletService->credit(
                        $seller,
                        $amount,
                        'credit',
                        "Wallet top-up via Monnify — ref: {$txn->reference}"
                    );
                    $message = '₦' . number_format($amount, 2) . ' has been added to your wallet.';
                }

                $txn->update([
                    'status'            => 'success',
                    'monnify_reference' => $data['transactionReference'] ?? null,
                    'gateway_response'  => $data,
                ]);

                return response()->json(['success' => true, 'message' => $message]);
            }

            $txn->update(['status' => 'failed', 'gateway_response' => $data]);

            return response()->json([
                'success' => false,
                'message' => 'Payment status: ' . ($data['paymentStatus'] ?? 'unknown'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Seller Monnify verify error', [
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
    // Korapay — existing flows (unchanged)
    // -------------------------------------------------------------------------

    public function topupAds(Request $request)
    {
        $request->validate(['amount' => ['required', 'numeric', 'min:1000']]);

        $seller    = auth('seller')->user();
        $amount    = (float) $request->amount;
        $reference = $this->korapay->generateReference('ADS');

        $this->korapay->createTransaction($seller, $amount, 'ads_topup', $reference);

        $checkoutData = $this->korapay->initializeCheckout(
            $seller->email,
            $seller->full_name,
            $amount,
            $reference,
            route('seller.wallet.topup.callback'),
            '',
            ['type' => 'ads_topup', 'seller_id' => $seller->id]
        );

        return redirect($checkoutData['checkout_url']);
    }

    public function topupWallet(Request $request)
    {
        $request->validate(['amount' => ['required', 'numeric', 'min:1']]);

        $seller    = auth('seller')->user();
        $amount    = (float) $request->amount;
        $reference = $this->korapay->generateReference('WAL');

        $this->korapay->createTransaction($seller, $amount, 'wallet_topup', $reference);

        $checkoutData = $this->korapay->initializeCheckout(
            $seller->email,
            $seller->full_name,
            $amount,
            $reference,
            route('seller.wallet.topup.callback'),
            '',
            ['type' => 'wallet_topup', 'seller_id' => $seller->id]
        );

        return redirect($checkoutData['checkout_url']);
    }

    public function topupCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('seller.wallet.index')
                ->with('error', 'Invalid payment reference.');
        }

        $txn = KorapayTransaction::where('reference', $reference)->first();

        if (!$txn || $txn->status === 'success') {
            return redirect()->route('seller.wallet.index')
                ->with('info', 'Transaction already processed.');
        }

        try {
            $data = $this->korapay->verifyTransaction($reference);

            if ($data['status'] === 'success') {
                $seller = auth('seller')->user();
                $amount = (float) $data['amount'];

                if ($txn->type === 'ads_topup') {
                    $this->walletService->topupAdsBalance($seller, $amount);
                    $message = '₦' . number_format($amount, 2) . ' added to ads balance.';
                } else {
                    $this->walletService->credit(
                        $seller,
                        $amount,
                        'credit',
                        "Wallet top-up via Korapay — ref: {$reference}"
                    );
                    $message = '₦' . number_format($amount, 2) . ' added to your wallet.';
                }

                $txn->update([
                    'status'            => 'success',
                    'korapay_reference' => $data['payment_reference'] ?? null,
                    'gateway_response'  => $data,
                ]);

                return redirect()->route('seller.wallet.index')->with('success', $message);
            }

            $txn->update(['status' => 'failed', 'gateway_response' => $data]);

            return redirect()->route('seller.wallet.index')
                ->with('error', 'Payment was not successful. Please try again.');

        } catch (\Exception $e) {
            return redirect()->route('seller.wallet.index')
                ->with('error', 'Could not verify payment. Contact support if amount was deducted.');
        }
    }

    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('x-korapay-signature', '');

        if (!$this->korapay->verifyWebhookSignature($payload, $signature)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $event = $request->json('event');
        $data  = $request->json('data');

        if ($event === 'charge.success') {
            $txn = KorapayTransaction::where('reference', $data['reference'])->first();
            if ($txn && $txn->status !== 'success') {
                $txn->update([
                    'status'            => 'success',
                    'korapay_reference' => $data['payment_reference'] ?? null,
                    'gateway_response'  => $data,
                ]);
            }
        }

        return response()->json(['message' => 'OK']);
    }
}