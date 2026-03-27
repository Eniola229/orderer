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

        $wallet = \App\Models\Wallet::where('owner_type', 'App\Models\Seller')
                    ->where('owner_id', $seller->id)
                    ->first();

        return view('admin.sellers.show', compact(
            'seller', 'orderCount', 'totalEarnings', 'wallet'
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
            'status'          => 'suspended',
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
        $seller->update(['status' => 'active']);
        return back()->with('success', 'Seller account reinstated.');
    }
}