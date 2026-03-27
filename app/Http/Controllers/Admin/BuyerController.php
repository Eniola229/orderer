<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
            );
        }

        $buyers = $query->withCount('orders')->latest()->paginate(20)->withQueryString();

        return view('admin.buyers.index', compact('buyers'));
    }

    public function show(User $user)
    {
        $user->load('orders');
        $wallet = \App\Models\Wallet::where('owner_type', 'App\Models\User')
                    ->where('owner_id', $user->id)->first();
        return view('admin.buyers.show', compact('user', 'wallet'));
    }

    public function suspend(User $user)
    {
        $user->update(['status' => 'suspended']);
        return back()->with('success', 'Buyer account suspended.');
    }

    public function unsuspend(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', 'Buyer account reinstated.');
    }
}