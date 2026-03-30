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
    public function __construct(protected WalletService $wallet, protected BrevoMailService $brevo) {

    }


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

        $statusColors = [
                            'pending'   => 'warning',
                            'approved'  => 'success',
                            'rejected'  => 'danger',
                            'completed' => 'info',
                            'failed'    => 'dark',
                            'processing'=> 'primary',
                            'paid'      => 'success',
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
        
        // Get exchange rate if currency is not NGN
        $exchangeRate = null;
        $convertedAmount = (float) $withdrawal->amount;
        $conversionNarration = "";
        
        if ($withdrawal->currency !== 'NGN') {
            try {
                $exchangeRate = $this->getExchangeRate('NGN', $withdrawal->currency);
                
                if (!$exchangeRate) {
                    // Roll back the lock
                    $withdrawal->update(['status' => 'pending']);
                    return back()->with('error', "Cannot process payout: Failed to get exchange rate for NGN to {$withdrawal->currency}. Please try again later.");
                }
                
                $convertedAmount = round((float) $withdrawal->amount * $exchangeRate, 2);
                $conversionNarration = " (NGN {$withdrawal->amount} @ {$exchangeRate} {$withdrawal->currency}/NGN)";
                
            } catch (\Exception $e) {
                $withdrawal->update(['status' => 'pending']);
                return back()->with('error', "Cannot process payout: Exchange rate API error - " . $e->getMessage());
            }
        }

        if ($withdrawal->payout_type === 'mobile_money') {
            if (empty($withdrawal->mobile_money_operator) || empty($withdrawal->mobile_number)) {
                $withdrawal->update(['status' => 'pending']);
                return back()->with('error', 'Cannot process payout: missing mobile money details.');
            }

            $destination = [
                'type'          => 'mobile_money',
                'amount'        => $convertedAmount,
                'currency'      => $withdrawal->currency,
                'narration'     => 'Withdrawal payout' . $conversionNarration,
                'mobile_money'  => [
                    'operator'      => $withdrawal->mobile_money_operator,
                    'mobile_number' => $withdrawal->mobile_number,
                ],
                'customer'      => [
                    'name'  => $withdrawal->account_name,
                    'email' => $withdrawal->seller->email,
                ],
            ];
        } else {
            $destination = [
                'type'          => 'bank_account',
                'amount'        => $convertedAmount,
                'currency'      => $withdrawal->currency,
                'narration'     => 'Withdrawal payout' . $conversionNarration,
                'bank_account'  => [
                    'bank'     => $withdrawal->bank_code,
                    'account'  => $withdrawal->account_number,
                ],
                'customer'      => [
                    'name'  => $withdrawal->account_name,
                    'email' => $withdrawal->seller->email,
                ],
            ];
        }

        // Build metadata conditionally - only include exchange_rate if it exists
        $metadata = [
            'withdrawal_id' => (string) $withdrawal->id,
            'usd_amount' => (float) $withdrawal->amount,
            'local_amount' => $convertedAmount,
        ];
        
        // Only add exchange_rate to metadata if it exists (not null)
        if ($exchangeRate !== null) {
            $metadata['exchange_rate'] = $exchangeRate;
        }

        try {
            $result = $korapay->disbursePayout($reference, $destination, $metadata);

            $withdrawal->update([
                'status'            => 'approved',
                'processed_at'      => now(),
                'processed_by'      => auth('admin')->id(),
                'korapay_reference' => $result['reference'] ?? $reference,
                'korapay_status'    => $result['status'] ?? 'processing',
                'payout_fee'        => $result['fee'] ?? null,
                'exchange_rate'     => $exchangeRate,
                'converted_amount'  => $convertedAmount,
            ]);

            $this->brevo->sendWithdrawalSuccess($withdrawal);

        } catch (\Exception $e) {
            // Log the full error for debugging
            \Log::error('Korapay payout exception', [
                'withdrawal_id' => $withdrawal->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = $e->getMessage();
            
            if (str_contains($errorMessage, 'verify payout before marking failed')) {
                $withdrawal->update([
                    'status'            => 'approved',
                    'processed_at'      => now(),
                    'processed_by'      => auth('admin')->id(),
                    'korapay_reference' => $reference,
                    'korapay_status'    => 'unknown',
                    'exchange_rate'     => $exchangeRate,
                    'converted_amount'  => $convertedAmount,
                ]);
                
                return back()->with('warning', 'Payout initiated but needs verification: ' . $errorMessage);
            } else {
                // Roll back the lock
                $withdrawal->update(['status' => 'pending']);
                
                // Return the actual error message to the user
                return back()->with('error', 'Payout failed: ' . $errorMessage);
            }
        }

        // Build notification with exchange rate info
        $notificationBody = "Your withdrawal of ₦{$withdrawal->amount} has been approved";
        if ($exchangeRate && $withdrawal->currency !== 'NGN') {
            $notificationBody .= " and converted to {$withdrawal->currency} {$convertedAmount} at rate {$exchangeRate} {$withdrawal->currency}/NGN";
        }
        $notificationBody .= " and is being processed.";

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $withdrawal->seller_id,
            'type'            => 'withdrawal_approved',
            'title'           => 'Withdrawal Approved',
            'body'            => $notificationBody,
            'action_url'      => route('seller.withdrawals.index'),
        ]);

        return back()->with('success', "Withdrawal of ₦{$withdrawal->amount} approved and payout initiated.");
    }
    /**
     * Get exchange rate from NGN to target currency
     */
    private function getExchangeRate(string $from, string $to): ?float
    {
        try {
            // Using free API (exchangerate-api.com)
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get("https://api.exchangerate-api.com/v4/latest/{$from}");
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['rates'][$to] ?? null;
            }
            
            // Fallback to another free API
            $response2 = \Illuminate\Support\Facades\Http::timeout(5)->get("https://api.frankfurter.app/latest", [
                'from' => $from,
                'to' => $to,
            ]);
            
            if ($response2->successful()) {
                $data = $response2->json();
                return $data['rates'][$to] ?? null;
            }
            
            return null;
            
        } catch (\Exception $e) {
            throw new \Exception("Exchange rate API error: " . $e->getMessage());
        }
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
            'status'       => 'rejected',
            'processed_at' => now(),
            'processed_by' => auth('admin')->id(),
            'rejection_reason'   => $request->reason,
        ]);

        // Refund wallet
        $this->wallet->credit(
            $withdrawal->seller,
            $withdrawal->amount,
            'refund',
            "Withdrawal rejected — amount refunded. Reason: {$request->reason}"
        );

        $this->brevo->sendWithdrawalRejected($withdrawal, $request->reason);

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $withdrawal->seller_id,
            'type'            => 'withdrawal_rejected',
            'title'           => 'Withdrawal Rejected',
            'body'            => "Your withdrawal of ₦{$withdrawal->amount} was rejected. Reason: {$request->reason}. Amount has been refunded to your wallet.",
            'action_url'      => route('seller.withdrawals.index'),
        ]);

        return back()->with('success', 'Withdrawal rejected and amount refunded to seller wallet.');
    }

    /**
     * Change withdrawal status (especially for processing to pending/approved)
     */
    public function changeStatus(Request $request, WithdrawalRequest $wd)
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);
        
        $request->validate([
            'status' => ['required', 'in:pending,approved']
        ]);
        
        $withdrawal = $wd;
        $oldStatus = $withdrawal->status;
        
        // Only allow changing from processing to pending or approved
        if ($oldStatus !== 'processing') {
            return back()->with('error', 'Only withdrawals in processing status can be manually changed.');
        }
        
        $newStatus = $request->status;
        
        // Update the withdrawal
        $withdrawal->update([
            'status' => $newStatus,
            'processed_at' => $newStatus === 'approved' ? now() : ($newStatus === 'pending' ? null : $withdrawal->processed_at),
            'processed_by' => auth('admin')->id(),
            'korapay_status' => $newStatus === 'pending' ? null : $withdrawal->korapay_status,
        ]);
        
        // If changing to pending, we need to reverse any wallet actions if necessary
        if ($newStatus === 'pending') {
  
            // Send notification to seller
            Notification::create([
                'notifiable_type' => 'App\Models\Seller',
                'notifiable_id' => $withdrawal->seller_id,
                'type' => 'withdrawal_status_changed',
                'title' => 'Withdrawal Status Updated',
                'body' => "Your withdrawal of ₦{$withdrawal->amount} has been changed from {$oldStatus} back to pending for review.",
                'action_url' => route('seller.withdrawals.index'),
            ]);
            
            return back()->with('success', "Withdrawal status changed from {$oldStatus} to pending.");
        }
        
        // If changing to approved from processing
        if ($newStatus === 'approved') {
            // Send notification
            Notification::create([
                'notifiable_type' => 'App\Models\Seller',
                'notifiable_id' => $withdrawal->seller_id,
                'type' => 'withdrawal_approved',
                'title' => 'Withdrawal Approved',
                'body' => "Your withdrawal of ₦{$withdrawal->amount} has been approved and will be processed.",
                'action_url' => route('seller.withdrawals.index'),
            ]);
            
            // Send email notification
            if (method_exists($this, 'sendWithdrawalSuccess')) {
                $this->sendWithdrawalSuccess($withdrawal);
            } elseif (isset($this->brevo)) {
                $this->brevo->sendWithdrawalSuccess($withdrawal);
            }
            
            return back()->with('success', "Withdrawal status changed from {$oldStatus} to approved.");
        }
        
        return back()->with('error', 'Invalid status change request.');
    }
}