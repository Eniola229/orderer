<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Seller;
use App\Models\Order;
use App\Models\Product;
use App\Models\WithdrawalRequest;
use App\Models\SupportTicket;
use App\Models\Ad;
use App\Models\WalletTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();

        $stats = [
            'total_buyers'      => User::count(),
            'total_sellers'     => Seller::count(),
            'pending_sellers'   => Seller::where('is_approved', false)->count(),
            'total_orders'      => Order::count(),
            'pending_orders'    => Order::where('status', 'pending')->count(),
            'total_products'    => Product::count(),
            'pending_products'  => Product::where('status', 'pending')->count(),
            'pending_withdrawals'=> WithdrawalRequest::where('status', 'pending')->count(),
            'open_tickets'      => SupportTicket::where('status', 'open')->count(),
            'pending_ads'       => Ad::where('status', 'pending')->count(),
        ];

        // Revenue (last 30 days)
        $revenue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('total');

        $commission = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('commission_total');

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        // Recent sellers
        $recentSellers = Seller::latest()->take(5)->get();

        // Revenue chart data (last 14 days)
        $chartData = [];
        for ($i = 13; $i >= 0; $i--) {
            $date   = now()->subDays($i)->format('Y-m-d');
            $label  = now()->subDays($i)->format('M d');
            $dayRev = Order::where('payment_status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('total');
            $chartData[] = ['label' => $label, 'value' => (float) $dayRev];
        }

        return view('admin.dashboard.index', compact(
            'admin', 'stats', 'revenue', 'commission',
            'recentOrders', 'recentSellers', 'chartData'
        ));
    }
}