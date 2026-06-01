<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use App\Services\BrevoMailService;

class KorapayWebhookController extends Controller
{
    public function __construct(
        protected WalletService    $wallet,
        protected BrevoMailService $brevo
    ) {}

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

        if ($status === 'success') {
            $withdrawal->update([
                'korapay_status' => 'success',
            ]);

            // Send success email if not already sent
            try {
                $this->brevo->sendWithdrawalSuccess($withdrawal);
            } catch (\Exception $e) {
                \Log::warning('Webhook: withdrawal success email failed', [
                    'withdrawal_id' => $withdrawal->id,
                    'error'         => $e->getMessage(),
                ]);
            }

            Notification::create([
                'notifiable_type' => 'App\Models\Seller',
                'notifiable_id'   => $withdrawal->seller_id,
                'type'            => 'withdrawal_approved',
                'title'           => 'Withdrawal Successful',
                'body'            => "Your withdrawal of ₦" . number_format($withdrawal->amount, 2) . " has been successfully sent to your bank account.",
                'action_url'      => route('seller.withdrawals.index'),
            ]);

            \Log::info('Korapay webhook: payout success', [
                'withdrawal_id' => $withdrawal->id,
                'reference'     => $reference,
            ]);
        }

        if ($status === 'failed') {
            $withdrawal->update([
                'korapay_status' => 'failed',
                'status'         => 'rejected',
            ]);

            $this->wallet->credit(
                $withdrawal->seller,
                (float) $withdrawal->amount,
                'refund',
                "Payout failed — withdrawal #{$withdrawal->id} refunded",
                WithdrawalRequest::class,
                (string) $withdrawal->id
            );

            Notification::create([
                'notifiable_type' => 'App\Models\Seller',
                'notifiable_id'   => $withdrawal->seller_id,
                'type'            => 'withdrawal_failed',
                'title'           => 'Withdrawal Failed',
                'body'            => "Your withdrawal of ₦" . number_format($withdrawal->amount, 2) . " could not be processed. The amount has been returned to your wallet — please try again.",
                'action_url'      => route('seller.withdrawals.index'),
            ]);

            \Log::warning('Korapay webhook: payout failed', [
                'withdrawal_id' => $withdrawal->id,
                'reference'     => $reference,
                'data'          => $data,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}