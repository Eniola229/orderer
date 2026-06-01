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

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search: seller name, email, account number, account name, reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_number',    'like', "%{$search}%")
                  ->orWhere('account_name',    'like', "%{$search}%")
                  ->orWhere('korapay_reference','like', "%{$search}%")
                  ->orWhereHas('seller', function ($sq) use ($search) {
                      $sq->where('business_name', 'like', "%{$search}%")
                         ->orWhere('email',        'like', "%{$search}%");
                  });
            });
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Amount range
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', (float) $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', (float) $request->amount_max);
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

        $korapay  = app(\App\Services\KorapayService::class);
        $reference = $korapay->generateReference('PAY');

        try {
            $result = $korapay->disbursePayout(
                reference: $reference,
                destination: [
                    'type'      => 'bank_account',
                    'amount'    => (float) $withdrawal->amount,
                    'currency'  => 'NGN',
                    'narration' => "Seller withdrawal – {$withdrawal->seller->business_name}",
                    'bank_account' => [
                        'bank'    => $withdrawal->bank_code,
                        'account' => $withdrawal->account_number,
                    ],
                    'customer' => [
                        'name'  => $withdrawal->account_name,
                        'email' => $withdrawal->seller->email,
                    ],
                ],
                metadata: [
                    'withdrawal-id' => (string) $withdrawal->id,
                    'seller-id'     => (string) $withdrawal->seller_id,
                ]
            );

            \Log::info('Korapay disbursePayout result', [
                'withdrawal_id' => $withdrawal->id,
                'result'        => $result,
            ]);

            // Korapay statuses: processing, success, failed
            $korapayStatus = $result['status'] ?? 'unknown';

            if ($korapayStatus === 'failed') {
                // Payout was definitively rejected — refund the seller
                $withdrawal->update([
                    'status'            => 'rejected',
                    'processed_at'      => now(),
                    'processed_by'      => auth('admin')->id(),
                    'korapay_reference' => $result['reference'] ?? $reference,
                    'korapay_status'    => 'failed',
                    'payout_fee'        => $result['fee'] ?? null,
                ]);

                $this->wallet->credit(
                    $withdrawal->seller,
                    $withdrawal->amount,
                    'refund',
                    "Withdrawal payout failed — amount refunded. Reference: {$reference}"
                );

                Notification::create([
                    'notifiable_type' => 'App\Models\Seller',
                    'notifiable_id'   => $withdrawal->seller_id,
                    'type'            => 'withdrawal_failed',
                    'title'           => 'Withdrawal Failed',
                    'body'            => "Your withdrawal of ₦" . number_format($withdrawal->amount, 2) . " could not be processed. The amount has been returned to your wallet — please try again.",
                    'action_url'      => route('seller.withdrawals.index'),
                ]);

                return back()->with('error',
                    "Payout failed for ₦" . number_format($withdrawal->amount, 2) . " to {$withdrawal->account_name}. Amount has been refunded to the seller's wallet. Reference: {$reference}"
                );
            }

            // Status is 'processing' or 'success' — mark approved
            $this->markApproved($withdrawal, $result, $reference);

        } catch (\Exception $e) {
            \Log::error('Korapay payout exception', [
                'withdrawal_id' => $withdrawal->id,
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
            ]);

            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'verify payout before marking failed')
                || str_contains($errorMessage, 'server error')) {

                $withdrawal->update([
                    'korapay_reference' => $reference,
                    'korapay_status'    => 'unknown',
                    'processed_by'      => auth('admin')->id(),
                ]);

                return back()->with('warning', 'Payout submitted but status is unknown. Verify in your Korapay dashboard before retrying: ' . $errorMessage);
            }

            $withdrawal->update(['status' => 'pending']);
            return back()->with('error', 'Payout failed: ' . $errorMessage);
        }

        return back()->with('success', "Withdrawal of ₦" . number_format($withdrawal->amount, 2) . " approved and payout sent successfully.");
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
            'korapay_status'    => $result['status']    ?? 'processing',
            'payout_fee'        => $result['fee']       ?? null,
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

        try {
            $this->brevo->sendWithdrawalRejected($withdrawal, $request->reason);
        } catch (\Exception $mailEx) {
            \Log::warning('Withdrawal rejection email failed', [
                'withdrawal_id' => $withdrawal->id,
                'error'         => $mailEx->getMessage(),
            ]);
        }

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
     * Used when a Korapay server error left a withdrawal in an ambiguous state.
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
            'status'         => $newStatus,
            'processed_at'   => $newStatus === 'approved' ? now() : null,
            'processed_by'   => auth('admin')->id(),
            'korapay_status' => $newStatus === 'pending' ? null : $withdrawal->korapay_status,
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