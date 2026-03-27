<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;

class KorapayWebhookController extends Controller
{
    public function __construct(protected WalletService $wallet) {}

    public function handlePayout(Request $request)
    {
        $payload   = json_decode($request->getContent(), true);
        $signature = $request->header('x-korapay-signature');
        $korapay   = app(\App\Services\KorapayService::class);

        if (!$korapay->verifyWebhookSignature($payload['data'], $signature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data      = $payload['data'];
        $reference = $data['reference'];
        $status    = $data['status']; // 'success' or 'failed'

        $withdrawal = WithdrawalRequest::where('korapay_reference', $reference)->first();

        if (!$withdrawal) {
            return response()->json(['status' => 'ok']); // unknown reference, ignore silently
        }

        // Guard: don't process the same webhook twice
        if ($withdrawal->korapay_status === $status) {
            return response()->json(['status' => 'ok']);
        }

        $withdrawal->update(['korapay_status' => $status]);

        if ($status === 'failed') {
            $withdrawal->update(['status' => 'failed']);

            // Use WalletService so the transaction log, balance_before/after,
            // and denormalized wallet_balance on the seller are all properly recorded
            $this->wallet->credit(
                $withdrawal->seller,
                (float) $withdrawal->amount,
                'withdrawal_refund',
                "Payout failed — withdrawal #{$withdrawal->id} refunded",
                WithdrawalRequest::class,
                (string) $withdrawal->id
            );

            Notification::create([
                'notifiable_type' => 'App\Models\Seller',
                'notifiable_id'   => $withdrawal->seller_id,
                'type'            => 'withdrawal_failed',
                'title'           => 'Withdrawal Failed',
                'body'            => "Your withdrawal of \${$withdrawal->amount} could not be processed. Your balance has been refunded.",
                'action_url'      => route('seller.withdrawals.index'),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}