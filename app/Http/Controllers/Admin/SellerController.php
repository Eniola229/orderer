<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\SellerDocument;
use App\Models\WithdrawalRequest;
use App\Models\Ad;
use App\Services\BrevoMailService;
use App\Models\Notification;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function __construct(protected BrevoMailService $brevo) {}

    public function index(Request $request)
    {
        $query = Seller::query();

        if ($request->status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($request->status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($request->status === 'suspended') {
            $query->where('is_active', false);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('business_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('first_name', 'like', "%{$s}%")
            );
        }

        $sellers = $query
            ->withCount(['products', 'orders' => fn($q) => $q->whereHas('items', fn($r) => $r->where('seller_id', \DB::raw('sellers.id')))])
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $stats = [
            'total'           => Seller::count(),
            'pending'         => Seller::where('is_approved', false)->count(),
            'approved'        => Seller::where('is_approved', true)->count(),
            'verified'        => Seller::where('is_verified_business', true)->count(),
            'individual'      => Seller::where('is_verified_business', false)->count(),
            'active'          => Seller::where('is_active', true)->where('is_approved', true)->count(),
            'suspended'       => Seller::where('is_active', false)->count(),
            'total_wallet'    => \App\Models\Wallet::where('walletable_type', 'App\Models\Seller')->sum('balance'),
            'total_ads_bal'   => \App\Models\Wallet::where('walletable_type', 'App\Models\Seller')->sum('ads_balance'),
            'total_orders'    => \App\Models\OrderItem::distinct('order_id')->count('order_id'),
            'running_ads'     => \App\Models\Ad::where('status', 'active')
                                    ->where('start_date', '<=', now())
                                    ->where('end_date', '>=', now())
                                    ->distinct('seller_id')->count('seller_id'),
            'with_products'   => \App\Models\Product::where('status', 'approved')
                                    ->distinct('seller_id')->count('seller_id'),
            'with_services'   => \App\Models\ServiceListing::where('status', 'approved')
                                    ->distinct('seller_id')->count('seller_id'),
            'with_properties' => \App\Models\HouseListing::where('status', 'approved')
                                    ->distinct('seller_id')->count('seller_id'),
        ];

        return view('admin.sellers.index', compact('sellers', 'stats'));
    }

    public function pending()
    {
        $sellers = Seller::where('is_approved', false)
            ->with('documents')
            ->latest()
            ->paginate(20);

        return view('admin.sellers.pending', compact('sellers'));
    }

    public function show(Seller $seller)
    {
        $seller->load(['documents', 'products', 'brand', 'services', 'properties']);

        $orderCount    = \App\Models\OrderItem::where('seller_id', $seller->id)->count();
        $totalEarnings = \App\Models\OrderItem::where('seller_id', $seller->id)
                            ->where('status', 'completed')
                            ->sum('seller_earnings');

        $wallet = \App\Models\Wallet::where('walletable_type', 'App\Models\Seller')
                    ->where('walletable_id', $seller->id)
                    ->first();

        $transactions = [];
        if ($wallet) {
            $transactions = \App\Models\WalletTransaction::where('wallet_id', $wallet->id)
                            ->latest()
                            ->limit(10)
                            ->get();
        }

        // ── Withdrawal history (latest 8, all statuses) ───────────────────
        $withdrawals = WithdrawalRequest::where('seller_id', $seller->id)
                        ->latest()
                        ->limit(8)
                        ->get();

        $withdrawalStats = [
            'total_requested' => WithdrawalRequest::where('seller_id', $seller->id)->sum('amount'),
            'total_paid'      => WithdrawalRequest::where('seller_id', $seller->id)
                                    ->where('status', 'approved')->sum('amount'),
            'pending_count'   => WithdrawalRequest::where('seller_id', $seller->id)
                                    ->whereIn('status', ['pending', 'processing'])->count(),
        ];

        // ── Ads history (latest 8) ────────────────────────────────────────
        $ads = Ad::where('seller_id', $seller->id)
                ->latest()
                ->limit(8)
                ->get();

        $adStats = [
            'total'       => Ad::where('seller_id', $seller->id)->count(),
            'active'      => Ad::where('seller_id', $seller->id)->where('status', 'active')
                                ->where('start_date', '<=', now())->where('end_date', '>=', now())->count(),
            'total_spent' => Ad::where('seller_id', $seller->id)->sum('amount_spent'),
            'impressions' => Ad::where('seller_id', $seller->id)->sum('total_impressions'),
            'clicks'      => Ad::where('seller_id', $seller->id)->sum('total_clicks'),
        ];

        $sellerReferrals = \App\Models\Referral::where('referrer_type', 'App\Models\Seller')
            ->where('referrer_id', $seller->id)
            ->with(['referred', 'earnings'])
            ->latest()
            ->get();

        $referralStats = [
            'total'   => $sellerReferrals->count(),
            'earned'  => \App\Models\ReferralEarning::whereHas('referral', function ($q) use ($seller) {
                            $q->where('referrer_type', 'App\Models\Seller')
                              ->where('referrer_id', $seller->id);
                         })->where('status', 'credited')->sum('amount'),
            'pending' => \App\Models\ReferralEarning::whereHas('referral', function ($q) use ($seller) {
                            $q->where('referrer_type', 'App\Models\Seller')
                              ->where('referrer_id', $seller->id);
                         })->where('status', 'pending')->sum('amount'),
        ];

        return view('admin.sellers.show', compact(
            'seller', 'orderCount', 'totalEarnings', 'wallet', 'transactions',
            'withdrawals', 'withdrawalStats',
            'ads', 'adStats',
            'sellerReferrals', 'referralStats'
        ));
    }

    public function approve(Seller $seller)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $seller->update([
            'is_approved'         => true,
            'verification_status' => 'approved',
            'approved_by'         => auth('admin')->id(),
            'approved_at'         => now(),
        ]);

        if ($seller->document) {
            $seller->document->update(['status' => 'approved']);
        }

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $seller->id,
            'type'            => 'account_approved',
            'title'           => 'Account Approved',
            'body'            => 'Congratulations! Your seller account has been approved. You can now list products.',
            'action_url'      => route('seller.dashboard'),
        ]);

        $this->brevo->sendSellerApproved($seller);

        return back()->with('success', "Seller {$seller->business_name} approved.");
    }

    public function reject(Request $request, Seller $seller)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        $seller->update([
            'verification_status' => 'rejected',
            'is_approved'         => false,
            'rejection_reason'    => $request->reason,
            'rejected_at'         => now(),
            'rejected_by'         => auth('admin')->id(),
        ]);

        if ($seller->document) {
            $seller->document->update([
                'status'           => 'rejected',
                'rejection_reason' => $request->reason,
            ]);
        }

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $seller->id,
            'type'            => 'account_rejected',
            'title'           => 'Application Not Approved',
            'body'            => "Your seller application was not approved. Reason: {$request->reason}" .
                                 ($request->required_fields ? " Please update the following: " . implode(', ', $request->required_fields) : ""),
            'action_url'      => route('seller.pending'),
        ]);

        $this->brevo->sendSellerRejected($seller, $request->reason);

        return back()->with('success', 'Seller application rejected. The seller can now update their information.');
    }

    public function suspend(Request $request, Seller $seller)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $request->validate(['reason' => ['required', 'string']]);

        $seller->update([
            'is_active'        => false,
            'rejection_reason' => $request->reason,
        ]);

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $seller->id,
            'type'            => 'account_suspended',
            'title'           => 'Account Suspended',
            'body'            => "Your seller account has been suspended. Reason: {$request->reason}",
        ]);

        return back()->with('success', 'Seller account suspended.');
    }

    public function unsuspend(Seller $seller)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);
        $seller->update(['is_active' => true]);
        return back()->with('success', 'Seller account reinstated.');
    }

    public function adjustWallet(Request $request, Seller $seller)
    {
        if (!auth('admin')->user()->canManageFinance()) {
            abort(403, 'You do not have permission to manage finances.');
        }

        $request->validate([
            'wallet_type' => ['required', 'string', 'in:balance,ads'],
            'type'        => ['required', 'string', 'in:credit,debit'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'reason'      => ['required', 'string', 'max:500'],
        ]);

        try {
            $walletService = app(\App\Services\WalletService::class);
            $wallet        = $walletService->getOrCreate($seller);

            $amount     = $request->amount;
            $reason     = $request->reason;
            $walletType = $request->wallet_type;
            $actionType = $request->type;

            if ($actionType === 'debit') {
                if ($walletType === 'balance' && $wallet->balance < $amount) {
                    return back()->with('error', "Insufficient main wallet balance. Current balance: ₦" . number_format($wallet->balance, 2));
                }
                if ($walletType === 'ads' && $wallet->ads_balance < $amount) {
                    return back()->with('error', "Insufficient ads balance. Current balance: ₦" . number_format($wallet->ads_balance, 2));
                }
            }

            if ($walletType === 'balance') {
                if ($actionType === 'credit') {
                    $walletService->credit($seller, $amount, 'credit', "System adjustment: {$reason}", 'seller', $seller->id);
                    $message = "Successfully added ₦" . number_format($amount, 2) . " to seller's main wallet.";
                } else {
                    $walletService->debit($seller, $amount, 'debit', "System adjustment: {$reason}", 'seller', $seller->id);
                    $message = "Successfully deducted ₦" . number_format($amount, 2) . " from seller's main wallet.";
                }
            } else {
                if ($actionType === 'credit') {
                    $walletService->topupAdsBalance($seller, $amount);
                    $message = "Successfully added ₦" . number_format($amount, 2) . " to seller's ads balance.";
                } else {
                    $walletService->debitAdsBalance($seller, $amount, 'admin_adjustment_' . time());
                    $message = "Successfully deducted ₦" . number_format($amount, 2) . " from seller's ads balance.";
                }
            }

            Notification::create([
                'notifiable_type' => 'App\Models\Seller',
                'notifiable_id'   => $seller->id,
                'type'            => 'wallet_adjusted',
                'title'           => 'Wallet Balance Updated',
                'body'            => "System adjustment: {$reason} - Amount: ₦" . number_format($amount, 2),
                'action_url'      => route('seller.wallet.index'),
                'data'            => json_encode([
                    'wallet_type' => $walletType,
                    'amount'      => $amount,
                    'type'        => $actionType,
                    'reason'      => $reason,
                ]),
            ]);

            \Illuminate\Support\Facades\Log::info('Admin wallet adjustment', [
                'admin_id'    => auth('admin')->id(),
                'seller_id'   => $seller->id,
                'wallet_type' => $walletType,
                'type'        => $actionType,
                'amount'      => $amount,
                'reason'      => $reason,
            ]);

            return back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Wallet adjustment failed', [
                'seller_id' => $seller->id,
                'admin_id'  => auth('admin')->id(),
                'error'     => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to adjust wallet: ' . $e->getMessage());
        }
    }
}