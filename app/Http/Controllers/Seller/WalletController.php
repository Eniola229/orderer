<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Models\KorapayTransaction;
use App\Services\WalletService;
use App\Services\KorapayService;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService   $walletService,
        protected KorapayService  $korapay,
        protected BrevoMailService $brevo
    ) {}

    public function index()
    {
        $seller = auth('seller')->user();
        $wallet = $this->walletService->getOrCreate($seller);

        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->latest()
            ->paginate(20);
        
        // Get withdrawal requests
        $withdrawals = WithdrawalRequest::where('seller_id', $seller->id)
            ->latest()
            ->paginate(10);

        $escrowBalance = \App\Models\EscrowHold::where('seller_id', $seller->id)
        ->where('status', 'held')
        ->sum('seller_amount');

        return view('seller.wallet.index', compact('seller', 'wallet', 'transactions', 'withdrawals', 'escrowBalance'));
    }
    /**
     * Initialize Korapay top-up for ads balance
     */
    public function topupAds(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

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

    /**
     * Initialize Korapay wallet top-up
     */
    public function topupWallet(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

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

    /**
     * Korapay callback after payment
     */
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
                    $message = "Ads balance topped up with \${$amount}";
                } else {
                    $this->walletService->credit(
                        $seller,
                        $amount,
                        'credit',
                        "Wallet top-up via Korapay — ref: {$reference}"
                    );
                    $message = "Wallet credited with \${$amount}";
                }

                $txn->update([
                    'status'           => 'success',
                    'korapay_reference'=> $data['payment_reference'] ?? null,
                    'gateway_response' => $data,
                ]);

                return redirect()->route('seller.wallet.index')
                    ->with('success', $message);
            }

            $txn->update(['status' => 'failed', 'gateway_response' => $data]);

            return redirect()->route('seller.wallet.index')
                ->with('error', 'Payment was not successful. Please try again.');

        } catch (\Exception $e) {
            return redirect()->route('seller.wallet.index')
                ->with('error', 'Could not verify payment. Contact support if amount was deducted.');
        }
    }

    /**
     * Korapay webhook handler
     */
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
                    'status'           => 'success',
                    'korapay_reference'=> $data['payment_reference'] ?? null,
                    'gateway_response' => $data,
                ]);
            }
        }

        return response()->json(['message' => 'OK']);
    }
}