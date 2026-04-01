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
        $query = User::query();

        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $buyers = $query->withCount('orders')
                        ->latest()
                        ->paginate(20)
                        ->withQueryString();

        return view('admin.buyers.index', compact('buyers'));
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