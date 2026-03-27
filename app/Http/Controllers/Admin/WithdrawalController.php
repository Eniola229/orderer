<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\Notification;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct(protected WalletService $wallet) {}

    public function index(Request $request)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);

        $query = WithdrawalRequest::with('seller');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'pending'   => WithdrawalRequest::where('status', 'pending')->count(),
            'approved'  => WithdrawalRequest::where('status', 'approved')->count(),
            'total_paid'=> WithdrawalRequest::where('status', 'approved')->sum('amount'),
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'stats'));
    }

    public function approve(Request $request, WithdrawalRequest $withdrawal)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawals can be approved.');
        }

        // Guard: currency and amount must be present
        if (empty($withdrawal->currency)) {
            return back()->with('error', 'Cannot process payout: withdrawal has no currency set. Edit the record first.');
        }

        if ((float) $withdrawal->amount <= 0) {
            return back()->with('error', 'Cannot process payout: invalid amount.');
        }

        // Guard: bank_code required for bank_account payouts
        if ($withdrawal->payout_type !== 'mobile_money' && empty($withdrawal->bank_code)) {
            return back()->with('error', 'Cannot process payout: missing bank code.');
        }

        // Atomic status lock — prevents double-approval on rapid clicks
        $locked = WithdrawalRequest::where('id', $withdrawal->id)
            ->where('status', 'pending')
            ->update(['status' => 'processing']);

        if (!$locked) {
            return back()->with('error', 'Withdrawal is already being processed.');
        }

        $korapay   = app(\App\Services\KorapayService::class);
        $reference = $korapay->generateReference('PAY');

        if ($withdrawal->payout_type === 'mobile_money') {
            if (empty($withdrawal->mobile_money_operator) || empty($withdrawal->mobile_number)) {
                // Roll back the lock
                $withdrawal->update(['status' => 'pending']);
                return back()->with('error', 'Cannot process payout: missing mobile money details.');
            }

            $destination = [
                'type'         => 'mobile_money',
                'amount'       => (float) $withdrawal->amount,
                'currency'     => $withdrawal->currency,
                'narration'    => 'Withdrawal payout',
                'mobile_money' => [
                    'operator'      => $withdrawal->mobile_money_operator,
                    'mobile_number' => $withdrawal->mobile_number,
                ],
                'customer' => [
                    'name'  => $withdrawal->account_name,
                    'email' => $withdrawal->seller->email,
                ],
            ];
        } else {
            $destination = [
                'type'         => 'bank_account',
                'amount'       => (float) $withdrawal->amount,
                'currency'     => $withdrawal->currency,
                'narration'    => 'Withdrawal payout',
                'bank_account' => [
                    'bank'    => $withdrawal->bank_code,
                    'account' => $withdrawal->account_number,
                ],
                'customer' => [
                    'name'  => $withdrawal->account_name,
                    'email' => $withdrawal->seller->email,
                ],
            ];
        }

        try {
            $result = $korapay->disbursePayout($reference, $destination, [
                'withdrawal_id' => (string) $withdrawal->id,
            ]);

            $withdrawal->update([
                'status'            => 'approved',
                'processed_at'      => now(),
                'processed_by'      => auth('admin')->id(),
                'admin_note'        => $request->admin_note,
                'korapay_reference' => $result['reference'] ?? $reference,
                'korapay_status'    => $result['status'] ?? 'processing',
                'payout_fee'        => $result['fee'] ?? null,
            ]);

        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'verify payout before marking failed')) {
                // Server error — payout may still have gone through, do NOT refund yet
                $withdrawal->update([
                    'status'            => 'approved',
                    'processed_at'      => now(),
                    'processed_by'      => auth('admin')->id(),
                    'admin_note'        => trim($request->admin_note . ' [VERIFY PAYOUT — server error on disburse]'),
                    'korapay_reference' => $reference,
                    'korapay_status'    => 'unknown',
                ]);
            } else {
                // Clean API error (e.g. invalid bank code, insufficient balance) — safe to roll back
                $withdrawal->update(['status' => 'pending']);
                return back()->with('error', 'Payout failed: ' . $e->getMessage());
            }
        }

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $withdrawal->seller_id,
            'type'            => 'withdrawal_approved',
            'title'           => 'Withdrawal Approved',
            'body'            => "Your withdrawal of \${$withdrawal->amount} has been approved and is being processed.",
            'action_url'      => route('seller.withdrawals.index'),
        ]);

        return back()->with('success', "Withdrawal of \${$withdrawal->amount} approved and payout initiated.");
    }
    public function reject(Request $request, WithdrawalRequest $withdrawal)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);

        $request->validate(['reason' => ['required', 'string']]);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawals can be rejected.');
        }

        $withdrawal->update([
            'status'       => 'rejected',
            'processed_at' => now(),
            'processed_by' => auth('admin')->id(),
            'admin_note'   => $request->reason,
        ]);

        // Refund wallet
        $this->wallet->credit(
            $withdrawal->seller,
            $withdrawal->amount,
            'withdrawal_refund',
            "Withdrawal rejected — amount refunded. Reason: {$request->reason}"
        );

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $withdrawal->seller_id,
            'type'            => 'withdrawal_rejected',
            'title'           => 'Withdrawal Rejected',
            'body'            => "Your withdrawal of \${$withdrawal->amount} was rejected. Reason: {$request->reason}. Amount has been refunded to your wallet.",
            'action_url'      => route('seller.withdrawals.index'),
        ]);

        return back()->with('success', 'Withdrawal rejected and amount refunded to seller wallet.');
    }
}