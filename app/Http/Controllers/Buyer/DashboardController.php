<?php
// =====================================================
// app/Http/Controllers/Buyer/DashboardController.php
// =====================================================
namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use App\Models\Wishlist;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth('web')->user();

        $stats = [
            'total_orders'     => Order::where('user_id', $user->id)->count(),
            'pending_orders'   => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed_orders' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
            'wallet_balance'   => $user->wallet_balance,
            'wishlist_count'   => Wishlist::where('user_id', $user->id)->count(),
        ];

        $recentOrders = Order::where('user_id', $user->id)
            ->with('items')
            ->latest()
            ->take(5)
            ->get();

        $notifications = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->take(5)
            ->get();

        return view('buyer.dashboard.index', compact(
            'user', 'stats', 'recentOrders', 'notifications'
        ));
    }
}
