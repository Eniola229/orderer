<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Wallet;
use App\Services\WalletService;

class DashboardController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    public function index()
    {
        $seller = auth('seller')->user();
        $wallet = $this->walletService->getOrCreate($seller);

        $stats = [
            'wallet_balance'   => $wallet->balance,
            'ads_balance'      => $wallet->ads_balance,
            'total_orders'     => OrderItem::where('seller_id', $seller->id)->count(),
            'pending_orders'   => OrderItem::where('seller_id', $seller->id)->where('status', 'pending')->count(),
            'total_products'   => Product::where('seller_id', $seller->id)->count(),
            'approved_products'=> Product::where('seller_id', $seller->id)->where('status', 'approved')->count(),
            'active_ads'       => \App\Models\Ad::where('seller_id', $seller->id)->where('status', 'active')->count(),
        ];

        $recentOrders = OrderItem::where('seller_id', $seller->id)
            ->with('order.user')
            ->latest()
            ->take(5)
            ->get();

        $recentProducts = Product::where('seller_id', $seller->id)
            ->with('images')
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard.index', compact(
            'seller', 'stats', 'recentOrders', 'recentProducts', 'wallet'
        ));
    }
}