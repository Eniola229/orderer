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

        // Guards
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

        // Atomic status lock — prevents double-approval on rapid clicks
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

            $withdrawal->update([
                'status'             => 'approved',
                'processed_at'       => now(),
                'processed_by'       => auth('admin')->id(),
                'korapay_reference'  => $result['reference']        ?? $reference,  // reuse column for Monnify ref
                'korapay_status'     => $result['status']           ?? 'SUCCESS',
                'payout_fee'         => $result['fee']              ?? null,
                'currency'           => 'NGN',
                'converted_amount'   => (float) $withdrawal->amount,
            ]);

            // ── Email seller ───────────────────────────────────────────────
            try {
                $this->brevo->sendWithdrawalSuccess($withdrawal);
            } catch (\Exception $mailEx) {
                // Don't fail the whole request if the email bounces
                \Log::warning('Withdrawal approval email failed', [
                    'withdrawal_id' => $withdrawal->id,
                    'error'         => $mailEx->getMessage(),
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Monnify payout exception', [
                'withdrawal_id' => $withdrawal->id,
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
            ]);

            $errorMessage = $e->getMessage();

            // Monnify server errors (5xx) — payout may have gone through; keep as processing so
            // admin can verify manually via Monnify dashboard before marking failed.
            if (str_contains($errorMessage, 'verify payout before marking failed')
                || str_contains($errorMessage, 'server error')) {

                $withdrawal->update([
                    'status'            => 'approved',
                    'processed_at'      => now(),
                    'processed_by'      => auth('admin')->id(),
                    'korapay_reference' => $reference,
                    'korapay_status'    => 'unknown',
                ]);

                return back()->with('warning', 'Payout initiated but needs manual verification in Monnify dashboard: ' . $errorMessage);
            }

            // Any other error — roll back so admin can retry
            $withdrawal->update(['status' => 'pending']);
            return back()->with('error', 'Payout failed: ' . $errorMessage);
        }

        // ── In-app notification ────────────────────────────────────────────
        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $withdrawal->seller_id,
            'type'            => 'withdrawal_approved',
            'title'           => 'Withdrawal Approved',
            'body'            => "Your withdrawal of ₦" . number_format($withdrawal->amount, 2) . " has been approved and is being processed to your bank account.",
            'action_url'      => route('seller.withdrawals.index'),
        ]);

        return back()->with('success', "Withdrawal of ₦" . number_format($withdrawal->amount, 2) . " approved and payout initiated via Monnify.");
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