<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\SellerDocument;
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
            $query->where('status', 'suspended');
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('business_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('first_name', 'like', "%{$s}%")
            );
        }

        $sellers = $query->withCount(['products', 'orders' => fn($q) => $q->whereHas('items', fn($r) => $r->where('seller_id', \DB::raw('sellers.id')))])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.sellers.index', compact('sellers'));
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
        $seller->load(['documents', 'products', 'brand']);

        $orderCount   = \App\Models\OrderItem::where('seller_id', $seller->id)->count();
        $totalEarnings= \App\Models\OrderItem::where('seller_id', $seller->id)
                            ->where('status', 'completed')
                            ->sum('seller_earnings');

        $wallet = \App\Models\Wallet::where('walletable_type', 'App\Models\Seller')
                    ->where('walletable_id', $seller->id)
                    ->first();

        // Get wallet transactions
        $transactions = [];
        if ($wallet) {
            $transactions = \App\Models\WalletTransaction::where('wallet_id', $wallet->id)
                            ->latest()
                            ->limit(10)
                            ->get();
        }

        return view('admin.sellers.show', compact(
            'seller', 'orderCount', 'totalEarnings', 'wallet', 'transactions'
        ));
    }
    public function approve(Seller $seller)
    {
        if (!auth('admin')->user()->canModerateSellers()) {
            abort(403);
        }

        $seller->update([
            'is_approved' => true,
            'approved_by' => auth('admin')->id(),
            'approved_at' => now(),
        ]);

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

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $seller->update(['rejection_reason' => $request->reason]);

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $seller->id,
            'type'            => 'account_rejected',
            'title'           => 'Application Not Approved',
            'body'            => "Your seller application was not approved. Reason: {$request->reason}",
        ]);

        $this->brevo->sendSellerRejected($seller, $request->reason);

        return back()->with('success', 'Seller application rejected.');
    }

    public function suspend(Request $request, Seller $seller)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $request->validate(['reason' => ['required', 'string']]);

        $seller->update([
            'is_active'          => false,
            'rejection_reason'=> $request->reason,
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

    /**
     * Adjust seller wallet balance (credit or debit for main wallet or ads balance)
     */
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
            $wallet = $walletService->getOrCreate($seller);
            
            $amount = $request->amount;
            $reason = $request->reason;
            $walletType = $request->wallet_type;
            $actionType = $request->type;
            
            // Check for sufficient balance if it's a debit
            if ($actionType === 'debit') {
                if ($walletType === 'balance' && $wallet->balance < $amount) {
                    return redirect()->back()->with('error', 
                        "Insufficient main wallet balance. Current balance: $" . number_format($wallet->balance, 2)
                    );
                }
                if ($walletType === 'ads' && $wallet->ads_balance < $amount) {
                    return redirect()->back()->with('error', 
                        "Insufficient ads balance. Current balance: $" . number_format($wallet->ads_balance, 2)
                    );
                }
            }
            
            // Process the adjustment based on wallet type and action type
            if ($walletType === 'balance') {
                // Main wallet adjustment
                if ($actionType === 'credit') {
                    $transaction = $walletService->credit(
                        $seller,
                        $amount,
                        'credit',  // Using the enum value from schema
                        "System adjustment: {$reason}",
                        'seller',
                        $seller->id
                    );
                    
                    $message = "Successfully added $" . number_format($amount, 2) . " to seller's main wallet.";
                    
                } else { // debit
                    $transaction = $walletService->debit(
                        $seller,
                        $amount,
                        'debit',  // Using the enum value from schema
                        "System adjustment: {$reason}",
                        'seller',
                        $seller->id
                    );
                    
                    $message = "Successfully deducted $" . number_format($amount, 2) . " from seller's main wallet.";
                }
                
            } else { // Ads balance adjustment
                if ($actionType === 'credit') {
                    // Top up ads balance
                    $walletService->topupAdsBalance($seller, $amount);
                    
                    $message = "Successfully added $" . number_format($amount, 2) . " to seller's ads balance.";
                    
                } else { // debit
                    // Debit ads balance
                    $walletService->debitAdsBalance($seller, $amount, 'admin_adjustment_' . time());
                    
                    $message = "Successfully deducted $" . number_format($amount, 2) . " from seller's ads balance.";
                }
                
                $transaction = null;
            }
            
            // Create notification for seller
            Notification::create([
                'notifiable_type' => 'App\Models\Seller',
                'notifiable_id'   => $seller->id,
                'type'            => 'wallet_adjusted',
                'title'           => 'Wallet Balance Updated',
                'body'            => "System adjustment: {$reason} - Amount: $" . number_format($amount, 2),
                'action_url'      => route('seller.wallet.index'),
                'data'            => json_encode([
                    'wallet_type' => $walletType,
                    'amount'      => $amount,
                    'type'        => $actionType,
                    'reason'      => $reason,
                ]),
            ]);
            
            // Log admin action
            \Illuminate\Support\Facades\Log::info('Admin wallet adjustment', [
                'admin_id'    => auth('admin')->id(),
                'seller_id'   => $seller->id,
                'wallet_type' => $walletType,
                'type'        => $actionType,
                'amount'      => $amount,
                'reason'      => $reason,
            ]);
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('Wallet adjustment failed', [
                'seller_id' => $seller->id,
                'admin_id'  => auth('admin')->id(),
                'error'     => $e->getMessage(),
            ]);
            
            return redirect()->back()->with('error', 'Failed to adjust wallet: ' . $e->getMessage());
        }
    }
}