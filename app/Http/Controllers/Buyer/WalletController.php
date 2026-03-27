<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\KorapayTransaction;
use App\Services\WalletService;
use App\Services\KorapayService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService  $walletService,
        protected KorapayService $korapay
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

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
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
                    ->with('success', "Wallet credited with \${$amount}");
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
