<?php
namespace App\Http\Controllers\Seller;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Brand;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    public function index()
    {
        $seller = auth('seller')->user();
        $wallet = $this->walletService->getOrCreate($seller);
        $hasBrand = Brand::where('seller_id', $seller->id)->exists();

        $stats = [
            'wallet_balance'    => $wallet->balance,
            'ads_balance'       => $wallet->ads_balance,
            'total_orders'   => OrderItem::without('orderable')->where('seller_id', $seller->id)->count(),
            'pending_orders' => OrderItem::without('orderable')->where('seller_id', $seller->id)->where('status', 'pending')->count(),
            'total_products'    => Product::where('seller_id', $seller->id)->count(),
            'approved_products' => Product::where('seller_id', $seller->id)->where('status', 'approved')->count(),
            'active_ads'        => \App\Models\Ad::where('seller_id', $seller->id)->where('status', 'active')->count(),
        ];

        $recentOrders = OrderItem::without('orderable')
            ->where('seller_id', $seller->id)
            ->with('order')
            ->latest()
            ->take(5)
            ->get();

        $recentProducts = Product::where('seller_id', $seller->id)
            ->with('images')
            ->latest()
            ->take(5)
            ->get();

        // ── Orders chart: last 12 months ─────────────────────────────
        $orderChart = OrderItem::without('orderable')
            ->where('seller_id', $seller->id)
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');
        // Build a full 12-month series (fill gaps with zero)
        $chartLabels  = [];
        $chartOrders  = [];
        $chartRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $chartLabels[]  = now()->subMonths($i)->format('M Y');
            $chartOrders[]  = $orderChart->has($key) ? (int) $orderChart[$key]->orders  : 0;
            $chartRevenue[] = $orderChart->has($key) ? (float) $orderChart[$key]->revenue : 0.0;
        }
        // ─────────────────────────────────────────────────────────────

        // ── Low stock products (stock <= 5, approved) ─────────────────
        $lowStockProducts = Product::where('seller_id', $seller->id)
            ->where('status', 'approved')
            ->where('stock', '<=', 5)
            ->with('images')
            ->orderBy('stock', 'asc')
            ->take(8)
            ->get();
        // ─────────────────────────────────────────────────────────────

        // ── Top selling products ──────────────────────────────────────
        $topProducts = Product::where('seller_id', $seller->id)
            ->where('status', 'approved')
            ->where('total_sold', '>', 0)
            ->with('images')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();
        // ─────────────────────────────────────────────────────────────

        return view('seller.dashboard.index', compact(
            'seller', 'stats', 'recentOrders', 'recentProducts',
            'wallet', 'hasBrand',
            'chartLabels', 'chartOrders', 'chartRevenue',
            'lowStockProducts', 'topProducts'
        ));
    }
}