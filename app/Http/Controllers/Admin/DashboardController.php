<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Seller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\WithdrawalRequest;
use App\Models\SupportTicket;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /** Statuses that represent real money (non-cancelled) */
    private array $revenueStatuses = ['pending', 'confirmed', 'shipped', 'delivered', 'completed'];

    /** All statuses available in the status filter dropdown */
    private array $allStatuses = [
        'all', 'pending', 'confirmed', 'shipped',
        'delivered', 'completed', 'cancelled',
    ];

    public function index(Request $request)
    {
        $admin = auth('admin')->user();

        // ── 1. Date range ───────────────────────────────────────────
        $range = $request->input('range', '30');

        if ($range === 'custom'
            && $request->filled('date_from')
            && $request->filled('date_to'))
        {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo   = Carbon::parse($request->date_to)->endOfDay();
        } else {
            $days     = in_array($range, ['7', '14', '30', '90', '365'])
                            ? (int) $range
                            : 30;
            $dateFrom = now()->subDays($days)->startOfDay();
            $dateTo   = now()->endOfDay(); 
        }

        $periodDays = max(1, (int) $dateFrom->diffInDays($dateTo));
        $prevFrom   = $dateFrom->copy()->subDays($periodDays)->startOfDay();
        $prevTo     = $dateFrom->copy()->subSecond();

        // ── 2. Status filter ────────────────────────────────────────
        $selectedStatus = $request->input('status', 'all');

        // Resolve which statuses to query for revenue/commission
        if ($selectedStatus === 'all') {
            $queryStatuses = $this->revenueStatuses;
            $includePaid   = true;
        } elseif ($selectedStatus === 'cancelled') {
            $queryStatuses = ['cancelled'];
            $includePaid   = false;
        } else {
            $queryStatuses = [$selectedStatus];
            $includePaid   = true;
        }

        // ── 3. Platform-wide live stats (always unfiltered) ─────────
        $stats = [
            'total_buyers'        => User::count(),
            'total_sellers'       => Seller::count(),
            'pending_sellers'     => Seller::where('is_approved', false)->count(),
            'total_orders'        => Order::whereIn('status', $this->revenueStatuses)->count(),
            'pending_orders'      => Order::where('status', 'pending')->count(),
            'cancelled_orders'    => Order::where('status', 'cancelled')->count(),
            'total_products'      => Product::count(),
            'pending_products'    => Product::where('status', 'pending')->count(),
            'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
            'open_tickets'        => SupportTicket::where('status', 'open')->count(),
            'pending_ads'         => Ad::where('status', 'pending')->count(),
            'pending_flash_sales' => \App\Models\FlashSale::whereNull('created_by')->count(),
            'pending_houses'      => \App\Models\HouseListing::where('status', 'pending')->count(),
            'pending_services'    => \App\Models\ServiceListing::where('status', 'pending')->count(),
        ];

        // ── 4. Revenue helper (from orders table) ────────────────────
        $getRevenue = function (Carbon $from, Carbon $to) use ($queryStatuses, $includePaid): float {
            $q = Order::whereIn('status', $queryStatuses)
                    ->whereBetween('created_at', [$from, $to]);
            if ($includePaid) {
                $q->where('payment_status', 'paid');
            }
            return (float) $q->sum('total');
        };

        // ── 5. Commission helper (from order_items table) ────────────
        $getCommission = function (Carbon $from, Carbon $to) use ($queryStatuses): float {
            if ($queryStatuses === ['cancelled']) {
                return 0.0;
            }

            return (float) OrderItem::whereHas('order', fn($q) =>
                    $q->where('payment_status', 'paid')
                      ->whereIn('status', $queryStatuses)
                      ->whereBetween('created_at', [$from, $to])
                )
                ->sum('commission_amount');
        };

        // ── 6. Current & previous period totals ──────────────────────
        $revenue    = $getRevenue($dateFrom, $dateTo);
        $commission = $getCommission($dateFrom, $dateTo);

        $prevRevenue    = $getRevenue($prevFrom, $prevTo);
        $prevCommission = $getCommission($prevFrom, $prevTo);

        $revenueChange = $prevRevenue > 0
            ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : null;

        $commissionChange = $prevCommission > 0
            ? round((($commission - $prevCommission) / $prevCommission) * 100, 1)
            : null;

        // ── 7. Chart data ─────────────────────────────────────────────
        $chartData = [];

        $buildDayBucket = function (string $dayStr, string $label) use ($queryStatuses, $includePaid): array {
            $revQ = Order::whereIn('status', $queryStatuses)->whereDate('created_at', $dayStr);
            if ($includePaid) $revQ->where('payment_status', 'paid');
            $dayRev = (float) $revQ->sum('total');

            $dayComm = $queryStatuses === ['cancelled'] ? 0.0 :
                (float) OrderItem::whereHas('order', fn($q) =>
                    $q->where('payment_status', 'paid')
                      ->whereIn('status', $queryStatuses)
                      ->whereDate('created_at', $dayStr)
                )->sum('commission_amount');

            return ['label' => $label, 'revenue' => $dayRev, 'commission' => $dayComm];
        };

        $buildWeekBucket = function (Carbon $wStart, Carbon $wEnd, string $label) use ($queryStatuses, $includePaid): array {
            $revQ = Order::whereIn('status', $queryStatuses)->whereBetween('created_at', [$wStart, $wEnd]);
            if ($includePaid) $revQ->where('payment_status', 'paid');
            $weekRev = (float) $revQ->sum('total');

            $weekComm = $queryStatuses === ['cancelled'] ? 0.0 :
                (float) OrderItem::whereHas('order', fn($q) =>
                    $q->where('payment_status', 'paid')
                      ->whereIn('status', $queryStatuses)
                      ->whereBetween('created_at', [$wStart, $wEnd])
                )->sum('commission_amount');

            return ['label' => $label, 'revenue' => $weekRev, 'commission' => $weekComm];
        };

        if ($periodDays <= 31) {
            for ($i = $periodDays - 1; $i >= 0; $i--) {
                $day    = $dateTo->copy()->subDays($i);
                $chartData[] = $buildDayBucket($day->format('Y-m-d'), $day->format('M d'));
            }
        } else {
            $cursor = $dateFrom->copy()->startOfWeek();
            while ($cursor <= $dateTo) {
                $wEnd  = $cursor->copy()->endOfWeek();
                $label = $cursor->format('M d') . '–' . $wEnd->format('M d');
                $chartData[] = $buildWeekBucket($cursor->copy(), $wEnd, $label);
                $cursor->addWeek();
            }
        }

        // ── 8. Recent orders (from orders table, commission from order_items) ──
        // Get orders - no payment_status filter, just show orders
        $recentQuery = Order::with('user')->latest();

        // Only filter by status if NOT 'all'
        if ($selectedStatus !== 'all') {
            $recentQuery->where('status', $selectedStatus);
        }

        $recentOrders = $recentQuery->take(8)->get();

        // Get commission for each order from order_items table
        if ($recentOrders->isNotEmpty()) {
            $orderIds = $recentOrders->pluck('id');
            $commissionByOrder = OrderItem::whereIn('order_id', $orderIds)
                ->select('order_id', DB::raw('SUM(commission_amount) as total_commission'))
                ->groupBy('order_id')
                ->pluck('total_commission', 'order_id');

            $recentOrders->each(fn($order) =>
                $order->item_commission = (float) ($commissionByOrder[$order->id] ?? 0)
            );
        }

        // ── 9. Return view with all data ─────────────────────────────
        return view('admin.dashboard.index', compact(
            'admin', 'stats',
            'revenue', 'commission',
            'prevRevenue', 'prevCommission',
            'revenueChange', 'commissionChange',
            'recentOrders', 'chartData',
            'range', 'dateFrom', 'dateTo',
            'selectedStatus'
        ) + ['allStatuses' => $this->allStatuses]);
    }
}