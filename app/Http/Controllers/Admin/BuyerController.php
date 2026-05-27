<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class BuyerController extends Controller
{

    public function index(Request $request)
    {
        // ── Active-period (default 12 months, user-adjustable) ────────────────
        $activeMonths = (int) $request->input('active_months', 12);
        $activeMonths = in_array($activeMonths, [1, 3, 6, 12, 24]) ? $activeMonths : 12;
        $activeSince  = now()->subMonths($activeMonths);

        $activePeriodLabel = match ($activeMonths) {
            1  => '1 month',
            3  => '3 months',
            6  => '6 months',
            12 => '1 year',
            24 => '2 years',
            default => "{$activeMonths} months",
        };

        // ── Paginated list ────────────────────────────────────────────────────
        $query = \App\Models\User::withCount('orders');

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'suspended') {
            $query->where('is_active', false);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name',  'like', "%{$s}%")
                  ->orWhere('email',      'like', "%{$s}%")
            );
        }

        $buyers = $query->latest()->paginate(50)->withQueryString();

        // ── Stats ─────────────────────────────────────────────────────────────

        // Buyer IDs who placed at least one order within the active window
        $activeBuyerIds = \App\Models\Order::where('created_at', '>=', $activeSince)
                            ->distinct('user_id')
                            ->pluck('user_id');

        $stats = [
            'total'               => \App\Models\User::count(),

            'active_buyers'       => $activeBuyerIds->count(),
            'active_period_label' => $activePeriodLabel,

            // Registered, not suspended, but no order in the chosen window
            'inactive_buyers'     => \App\Models\User::where('is_active', true)
                                        ->whereNotIn('id', $activeBuyerIds)
                                        ->count(),

            // Never placed any order ever
            'never_ordered'       => \App\Models\User::doesntHave('orders')->count(),

            // Has placed at least one order ever
            'have_ordered'        => \App\Models\User::has('orders')->count(),

            // Total order count across all users
            'total_orders'        => \App\Models\Order::count(),

            'suspended'           => \App\Models\User::where('is_active', false)->count(),

            // Email not yet verified
            'unverified_email'    => \App\Models\User::whereNull('email_verified_at')->count(),

            // Sum of all user wallet balances (polymorphic wallets table)
            'total_wallet'        => \App\Models\Wallet::where('walletable_type', 'App\Models\User')
                                        ->sum('balance'),
        ];

        return view('admin.buyers.index', compact('buyers', 'stats', 'activeMonths'));
    }
    public function show(User $user)
    {
        if (!auth('admin')->user()->canModerateBuyer()) abort(403);
        $user->load('orders');
        $wallet = \App\Models\Wallet::where('walletable_type', 'App\Models\User')
                    ->where('walletable_id', $user->id)->first();
        return view('admin.buyers.show', compact('user', 'wallet'));
    }

    public function suspend(User $user)
    {
        if (!auth('admin')->user()->canModerateBuyer()) abort(403);
        $user->update(['is_active' => false]);
        return back()->with('success', 'Buyer account suspended.');
    }

    public function unsuspend(User $user)
    {
        if (!auth('admin')->user()->canModerateBuyer()) abort(403);
        $user->update(['is_active' => true]);
        return back()->with('success', 'Buyer account reinstated.');
    }
    
    public function adjustWallet(Request $request, User $user) 
    {
        if (!auth('admin')->user()->canManageFinance()) abort(403);
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type'   => 'required|in:credit,debit',
            'reason' => 'required|string'
        ]);
        
        try {
            $walletService = app(\App\Services\WalletService::class);
            $wallet = $walletService->getOrCreate($user);
            
            // Check sufficient balance for debit
            if ($request->type === 'debit' && $wallet->balance < $request->amount) {
                return back()->with('error', 
                    "Insufficient balance. Current balance: ₦" . number_format($wallet->balance, 2)
                );
            }
            
            if ($request->type === 'credit') {
                $walletService->credit(
                    $user, 
                    $request->amount, 
                    'credit',  // Using enum value from schema
                    "System adjustment: {$request->reason}",
                    'user',
                    $user->id
                );
            } else {
                $walletService->debit(
                    $user, 
                    $request->amount, 
                    'debit',  // Using enum value from schema
                    "System adjustment: {$request->reason}",
                    'user',
                    $user->id
                );
            }
            
            // Create notification for user
            Notification::create([
                'notifiable_type' => 'App\Models\User',
                'notifiable_id'   => $user->id,
                'type'            => 'wallet_adjusted',
                'title'           => 'Wallet Balance Updated',
                'body'            => "System adjustment: {$request->reason} - Amount: ₦" . number_format($request->amount, 2),
                'action_url'      => route('buyer.wallet'),
            ]);
            
            return back()->with('success', 'Wallet adjusted successfully.');
            
        } catch (\Exception $e) {
            \Log::error('User wallet adjustment failed', [
                'user_id'   => $user->id,
                'admin_id'  => auth('admin')->id(),
                'error'     => $e->getMessage(),
            ]);
            
            return back()->with('error', 'Failed to adjust wallet: ' . $e->getMessage());
        }
    }
}