<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = auth('seller')->user();

        $stats = [
            'wallet_balance'   => $seller->wallet_balance,
            'ads_balance'      => $seller->ads_balance,
            'total_orders'     => OrderItem::where('seller_id', $seller->id)->count(),
            'pending_orders'   => OrderItem::where('seller_id', $seller->id)
                                           ->where('status', 'pending')->count(),
            'total_products'   => Product::where('seller_id', $seller->id)->count(),
            'approved_products'=> Product::where('seller_id', $seller->id)
                                         ->where('status', 'approved')->count(),
            'active_ads'       => \App\Models\Ad::where('seller_id', $seller->id)
                                                 ->where('status', 'active')->count(),
        ];

        $recentOrders = OrderItem::with('order')
            ->where('seller_id', $seller->id)
            ->latest()
            ->take(8)
            ->get();

        $recentProducts = Product::with('images')
            ->where('seller_id', $seller->id)
            ->latest()
            ->take(6)
            ->get();

        return view('seller.dashboard.index', compact(
            'seller', 'stats', 'recentOrders', 'recentProducts'
        ));
    }
}