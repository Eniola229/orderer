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
        
        // Get exchange rate if currency is not USD
        $exchangeRate = null;
        $convertedAmount = (float) $withdrawal->amount;
        $conversionNarration = "";
        
        if ($withdrawal->currency !== 'USD') {
            try {
                $exchangeRate = $this->getExchangeRate('USD', $withdrawal->currency);
                
                if (!$exchangeRate) {
                    // Roll back the lock
                    $withdrawal->update(['status' => 'pending']);
                    return back()->with('error', "Cannot process payout: Failed to get exchange rate for USD to {$withdrawal->currency}. Please try again later.");
                }
                
                $convertedAmount = round((float) $withdrawal->amount * $exchangeRate, 2);
                $conversionNarration = " (USD {$withdrawal->amount} @ {$exchangeRate} {$withdrawal->currency}/USD)";
                
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

        try {
            $result = $korapay->disbursePayout($reference, $destination, [
                'withdrawal_id' => (string) $withdrawal->id,
                'exchange_rate' => $exchangeRate,
                'usd_amount' => (float) $withdrawal->amount,
                'local_amount' => $convertedAmount,
            ]);

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
            if (str_contains($e->getMessage(), 'verify payout before marking failed')) {
                $withdrawal->update([
                    'status'            => 'approved',
                    'processed_at'      => now(),
                    'processed_by'      => auth('admin')->id(),
                    'korapay_reference' => $reference,
                    'korapay_status'    => 'unknown',
                    'exchange_rate'     => $exchangeRate,
                    'converted_amount'  => $convertedAmount,
                ]);
            } else {
                $withdrawal->update(['status' => 'pending']);
                return back()->with('error', 'Payout failed: ' . $e->getMessage());
            }
        }

        // Build notification with exchange rate info
        $notificationBody = "Your withdrawal of \${$withdrawal->amount} has been approved";
        if ($exchangeRate && $withdrawal->currency !== 'USD') {
            $notificationBody .= " and converted to {$withdrawal->currency} {$convertedAmount} at rate {$exchangeRate} {$withdrawal->currency}/USD";
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

        return back()->with('success', "Withdrawal of \${$withdrawal->amount} approved and payout initiated.");
    }

    /**
     * Get exchange rate from USD to target currency
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
            'body'            => "Your withdrawal of \${$withdrawal->amount} was rejected. Reason: {$request->reason}. Amount has been refunded to your wallet.",
            'action_url'      => route('seller.withdrawals.index'),
        ]);

        return back()->with('success', 'Withdrawal rejected and amount refunded to seller wallet.');
    }
}