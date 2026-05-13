<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\Notification;
use App\Services\WalletService;
use Illuminate\Http\Request;
use App\Services\BrevoMailService;

class WithdrawalController extends Controller
{
    public function __construct(
        protected WalletService    $wallet,
        protected BrevoMailService $brevo
    ) {}

    public function index(Request $request)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);

        $query = WithdrawalRequest::with('seller');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'pending'    => WithdrawalRequest::where('status', 'pending')->count(),
            'approved'   => WithdrawalRequest::where('status', 'approved')->count(),
            'total_paid' => WithdrawalRequest::where('status', 'approved')->sum('amount'),
        ];

        $statusColors = [
            'pending'    => 'warning',
            'approved'   => 'success',
            'rejected'   => 'danger',
            'completed'  => 'info',
            'failed'     => 'dark',
            'processing' => 'primary',
            'paid'       => 'success',
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'stats', 'statusColors'));
    }

    public function approve(Request $request, WithdrawalRequest $wd)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);

        $withdrawal = $wd;

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawals can be approved.');
        }

        if ((float) $withdrawal->amount <= 0) {
            return back()->with('error', 'Cannot process payout: invalid amount.');
        }

        if (empty($withdrawal->bank_code)) {
            return back()->with('error', 'Cannot process payout: missing bank code.');
        }

        if (empty($withdrawal->account_number)) {
            return back()->with('error', 'Cannot process payout: missing account number.');
        }

        if (empty($withdrawal->account_name)) {
            return back()->with('error', 'Cannot process payout: missing account name.');
        }

        // Atomic lock — prevents double-approval on rapid clicks
        $locked = WithdrawalRequest::where('id', $withdrawal->id)
            ->where('status', 'pending')
            ->update(['status' => 'processing']);

        if (!$locked) {
            return back()->with('error', 'Withdrawal is already being processed.');
        }

        $monnify   = app(\App\Services\MonnifyService::class);
        $reference = $monnify->generateReference('PAY');

        try {
            $result = $monnify->disbursePayout(
                reference:                $reference,
                amount:                   (float) $withdrawal->amount,
                destinationBankCode:      $withdrawal->bank_code,
                destinationAccountNumber: $withdrawal->account_number,
                destinationAccountName:   $withdrawal->account_name,
                narration:                "Seller withdrawal – {$withdrawal->seller->business_name}",
                sourceAccountNumber:      config('services.monnify.source_account'),
                async:                    false,
                metadata: [
                    'withdrawal_id' => (string) $withdrawal->id,
                    'seller_id'     => (string) $withdrawal->seller_id,
                ]
            );

            \Log::info('Monnify disbursePayout result', [
                'withdrawal_id' => $withdrawal->id,
                'result'        => $result,
            ]);

            $monnifyStatus = $result['status'] ?? 'UNKNOWN';

            // ── MFA enabled — Monnify is waiting for OTP before processing ──
            if ($monnifyStatus === 'PENDING_AUTHORIZATION') {

                // Save the reference so authorizeOtp() can find and complete it
                $withdrawal->update([
                    'korapay_reference' => $result['reference'] ?? $reference,
                    'korapay_status'    => 'PENDING_AUTHORIZATION',
                    'processed_by'      => auth('admin')->id(),
                    // status stays 'processing' — not approved yet
                ]);

                // Flash the withdrawal ID so the blade can auto-open the OTP modal
                return back()
                    ->with('otp_required', $withdrawal->id)
                    ->with('otp_info', "OTP sent to your Monnify registered email. Enter it below to complete the ₦" . number_format($withdrawal->amount, 2) . " payout to {$withdrawal->account_name}.");
            }

            // ── MFA disabled — transfer went straight through ───────────────
            $this->markApproved($withdrawal, $result, $reference);

        } catch (\Exception $e) {
            \Log::error('Monnify payout exception', [
                'withdrawal_id' => $withdrawal->id,
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
            ]);

            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'verify payout before marking failed')
                || str_contains($errorMessage, 'server error')) {

                $withdrawal->update([
                    'korapay_reference' => $reference,
                    'korapay_status'    => 'UNKNOWN',
                    'processed_by'      => auth('admin')->id(),
                ]);

                return back()->with('warning', 'Payout submitted but status is unknown. Verify in your Monnify dashboard before retrying: ' . $errorMessage);
            }

            $withdrawal->update(['status' => 'pending']);
            return back()->with('error', 'Payout failed: ' . $errorMessage);
        }

        return back()->with('success', "Withdrawal of ₦" . number_format($withdrawal->amount, 2) . " approved and payout sent successfully.");
    }

    /**
     * Authorize a PENDING_AUTHORIZATION transfer with the OTP from Monnify email.
     */
    public function authorizeOtp(Request $request, WithdrawalRequest $wd)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);

        $request->validate([
            'otp' => ['required', 'string', 'min:4', 'max:10'],
        ]);

        $withdrawal = $wd;

        if ($withdrawal->status !== 'processing') {
            return back()->with('error', 'This withdrawal is not awaiting OTP authorization.');
        }

        if ($withdrawal->korapay_status !== 'PENDING_AUTHORIZATION') {
            return back()->with('error', 'This withdrawal does not require OTP authorization.');
        }

        if (empty($withdrawal->korapay_reference)) {
            return back()->with('error', 'No Monnify reference found for this withdrawal. Please contact support.');
        }

        $monnify = app(\App\Services\MonnifyService::class);

        try {
            $result = $monnify->authorizeTransfer(
                $withdrawal->korapay_reference,
                $request->otp
            );

            \Log::info('Monnify authorizeOtp result', [
                'withdrawal_id' => $withdrawal->id,
                'result'        => $result,
            ]);

            $monnifyStatus = $result['status'] ?? 'UNKNOWN';

            if ($monnifyStatus === 'FAILED') {
                // Transfer was definitively rejected by the bank network — refund the seller
                $withdrawal->update([
                    'status'         => 'rejected',
                    'processed_at'   => now(),
                    'processed_by'   => auth('admin')->id(),
                    'korapay_status' => 'FAILED',
                    'payout_fee'     => $result['totalFee'] ?? null,
                ]);

                // Refund wallet so seller can request again
                $this->wallet->credit(
                    $withdrawal->seller,
                    $withdrawal->amount,
                    'refund',
                    "Withdrawal payout failed — amount refunded. Reference: {$withdrawal->korapay_reference}"
                );

                // Notify seller
                Notification::create([
                    'notifiable_type' => 'App\Models\Seller',
                    'notifiable_id'   => $withdrawal->seller_id,
                    'type'            => 'withdrawal_failed',
                    'title'           => 'Withdrawal Failed',
                    'body'            => "Your withdrawal of ₦" . number_format($withdrawal->amount, 2) . " could not be processed. The amount has been returned to your wallet — please try again.",
                    'action_url'      => route('seller.withdrawals.index'),
                ]);

                \Log::warning('Monnify transfer FAILED after OTP', [
                    'withdrawal_id' => $withdrawal->id,
                    'reference'     => $withdrawal->korapay_reference,
                    'result'        => $result,
                ]);

                return back()->with('error',
                    "Transfer failed after OTP authorization. The ₦" . number_format($withdrawal->amount, 2) . " has been refunded to the seller's wallet. Check your Monnify dashboard for the reason (reference: {$withdrawal->korapay_reference})."
                );
            }

            if (!in_array($monnifyStatus, ['SUCCESS', 'COMPLETED'])) {
                // Ambiguous — still processing or unknown. Don't mark either way, let admin check dashboard.
                return back()->with('warning',
                    "OTP accepted but transfer status is: {$monnifyStatus}. Check your Monnify dashboard for reference {$withdrawal->korapay_reference} before taking further action."
                );
            }

            $this->markApproved($withdrawal, $result, $withdrawal->korapay_reference);

        } catch (\Exception $e) {
            \Log::error('Monnify authorizeOtp exception', [
                'withdrawal_id' => $withdrawal->id,
                'error'         => $e->getMessage(),
            ]);

            // Don't roll back to pending — the reference is still valid and can be retried
            return back()
                ->with('otp_required', $withdrawal->id)
                ->with('error', 'OTP authorization failed: ' . $e->getMessage() . ' — please try again.');
        }

        return back()->with('success', "OTP verified! Payout of ₦" . number_format($withdrawal->amount, 2) . " to {$withdrawal->account_name} completed successfully.");
    }

    /**
     * Shared helper — mark a withdrawal as fully approved, send email + notification.
     */
    private function markApproved(WithdrawalRequest $withdrawal, array $result, string $reference): void
    {
        $withdrawal->update([
            'status'            => 'approved',
            'processed_at'      => now(),
            'processed_by'      => auth('admin')->id(),
            'korapay_reference' => $result['reference'] ?? $reference,
            'korapay_status'    => $result['status']    ?? 'SUCCESS',
            'payout_fee'        => $result['totalFee']  ?? $result['fee'] ?? null,
            'currency'          => 'NGN',
            'converted_amount'  => (float) $withdrawal->amount,
        ]);

        try {
            $this->brevo->sendWithdrawalSuccess($withdrawal);
        } catch (\Exception $mailEx) {
            \Log::warning('Withdrawal approval email failed', [
                'withdrawal_id' => $withdrawal->id,
                'error'         => $mailEx->getMessage(),
            ]);
        }

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $withdrawal->seller_id,
            'type'            => 'withdrawal_approved',
            'title'           => 'Withdrawal Approved',
            'body'            => "Your withdrawal of ₦" . number_format($withdrawal->amount, 2) . " has been approved and processed to your bank account.",
            'action_url'      => route('seller.withdrawals.index'),
        ]);
    }

    public function reject(Request $request, WithdrawalRequest $wd)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);

        $withdrawal = $wd;

        $request->validate(['reason' => ['required', 'string']]);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawals can be rejected.');
        }

        $withdrawal->update([
            'status'           => 'rejected',
            'processed_at'     => now(),
            'processed_by'     => auth('admin')->id(),
            'rejection_reason' => $request->reason,
        ]);

        // Refund wallet
        $this->wallet->credit(
            $withdrawal->seller,
            $withdrawal->amount,
            'refund',
            "Withdrawal rejected — amount refunded. Reason: {$request->reason}"
        );

        // ── Email seller ───────────────────────────────────────────────────
        try {
            $this->brevo->sendWithdrawalRejected($withdrawal, $request->reason);
        } catch (\Exception $mailEx) {
            \Log::warning('Withdrawal rejection email failed', [
                'withdrawal_id' => $withdrawal->id,
                'error'         => $mailEx->getMessage(),
            ]);
        }

        // ── In-app notification ────────────────────────────────────────────
        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $withdrawal->seller_id,
            'type'            => 'withdrawal_rejected',
            'title'           => 'Withdrawal Rejected',
            'body'            => "Your withdrawal of ₦" . number_format($withdrawal->amount, 2) . " was rejected. Reason: {$request->reason}. The amount has been returned to your wallet.",
            'action_url'      => route('seller.withdrawals.index'),
        ]);

        return back()->with('success', 'Withdrawal rejected and amount refunded to seller wallet.');
    }

    /**
     * Change withdrawal status (processing → pending or approved).
     * Used when a Monnify server error left a withdrawal in an ambiguous state.
     */
    public function changeStatus(Request $request, WithdrawalRequest $wd)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);

        $request->validate([
            'status' => ['required', 'in:pending,approved'],
        ]);

        $withdrawal = $wd;
        $oldStatus  = $withdrawal->status;

        if ($oldStatus !== 'processing') {
            return back()->with('error', 'Only withdrawals in "processing" status can be manually changed.');
        }

        $newStatus = $request->status;

        $withdrawal->update([
            'status'        => $newStatus,
            'processed_at'  => $newStatus === 'approved' ? now() : null,
            'processed_by'  => auth('admin')->id(),
            'korapay_status'=> $newStatus === 'pending' ? null : $withdrawal->korapay_status,
        ]);

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $withdrawal->seller_id,
            'type'            => 'withdrawal_status_changed',
            'title'           => 'Withdrawal Status Updated',
            'body'            => "Your withdrawal of ₦" . number_format($withdrawal->amount, 2) . " has been moved from {$oldStatus} to {$newStatus}.",
            'action_url'      => route('seller.withdrawals.index'),
        ]);

        if ($newStatus === 'approved') {
            try {
                $this->brevo->sendWithdrawalSuccess($withdrawal);
            } catch (\Exception $e) {
                \Log::warning('Email failed on status change to approved', ['error' => $e->getMessage()]);
            }
        }

        return back()->with('success', "Withdrawal status changed from {$oldStatus} to {$newStatus}.");
    }
}