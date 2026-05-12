<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreeShippingRule;
use App\Models\User;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendFreeShippingAnnouncementJob;

class FreeShippingController extends Controller
{
    private function authorise(): void
    {
        abort_unless(
            Auth::guard('admin')->user()->canManageFinance(),
            403,
            'You do not have permission to manage free shipping rules.'
        );
    }

    public function index(Request $request)
    {
        $this->authorise();

        // ── Date filter ───────────────────────────────────────────
        $dateFrom = $request->filled('date_from')
            ? \Carbon\Carbon::parse($request->date_from)->startOfDay()
            : \Carbon\Carbon::now()->subDays(30)->startOfDay();

        $dateTo = $request->filled('date_to')
            ? \Carbon\Carbon::parse($request->date_to)->endOfDay()
            : \Carbon\Carbon::now()->endOfDay();

        // ── Rules list (paginated, date-filtered by created_at) ───
        $rules = FreeShippingRule::with('creator')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // ── Stats (always scoped to date range) ───────────────────
        $allRules = FreeShippingRule::whereBetween('created_at', [$dateFrom, $dateTo])->get();

        $totalRules    = $allRules->count();
        $activeRules   = $allRules->filter(fn($r) => $r->isCurrentlyActive())->count();
        $scheduledRules = $allRules->filter(fn($r) =>
            $r->is_active && $r->starts_at && $r->starts_at->isFuture()
        )->count();
        $expiredRules  = $allRules->filter(fn($r) =>
            $r->ends_at && $r->ends_at->isPast()
        )->count();
        $disabledRules = $allRules->filter(fn($r) => !$r->is_active)->count();

        // Rules IDs in range (for pivot counts)
        $ruleIds = $allRules->pluck('id');

        // Specific buyers enrolled across all rules in range
        $totalSpecificBuyers = \DB::table('free_shipping_rule_buyers')
            ->whereIn('rule_id', $ruleIds)
            ->distinct('user_id')
            ->count('user_id');

        // Specific products enrolled
        $totalSpecificProducts = \DB::table('free_shipping_rule_products')
            ->whereIn('rule_id', $ruleIds)
            ->distinct('product_id')
            ->count('product_id');

        // Specific sellers enrolled
        $totalSpecificSellers = \DB::table('free_shipping_rule_sellers')
            ->whereIn('rule_id', $ruleIds)
            ->distinct('seller_id')
            ->count('seller_id');

        // Orders that used a free shipping rule (within date range by order created_at)
        $ordersWithFreeShipping = \App\Models\Order::whereIn('free_shipping_rule_id', $ruleIds)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('payment_status', 'paid');

        $totalOrdersDiscounted  = (clone $ordersWithFreeShipping)->count();
        $totalDiscountGiven     = (clone $ordersWithFreeShipping)->sum('free_shipping_discount');
        $totalShippingRevLost   = $totalDiscountGiven; // alias for clarity in view
        $avgDiscountPerOrder    = $totalOrdersDiscounted > 0
            ? round($totalDiscountGiven / $totalOrdersDiscounted, 2)
            : 0;

        // Breakdown by audience type
        $audienceBreakdown = $allRules->groupBy('applies_to')->map->count();

        // Breakdown by product scope
        $scopeBreakdown = $allRules->groupBy('product_scope')->map->count();

        return view('admin.free-shipping.index', compact(
            'rules',
            'dateFrom', 'dateTo',
            'totalRules', 'activeRules', 'scheduledRules', 'expiredRules', 'disabledRules',
            'totalSpecificBuyers', 'totalSpecificProducts', 'totalSpecificSellers',
            'totalOrdersDiscounted', 'totalDiscountGiven', 'avgDiscountPerOrder',
            'audienceBreakdown', 'scopeBreakdown',
        ));
    }

    public function create()
    {
        $this->authorise();
        $buyers   = User::where('is_active', true)->select('id', 'first_name', 'last_name', 'email')->get();
        $products = Product::where('status', 'approved')->select('id', 'name')->get();
        $sellers  = Seller::where('is_active', true)->where('is_approved', true)->select('id', 'business_name')->get();
        return view('admin.free-shipping.create', compact('buyers', 'products', 'sellers'));
    }

    public function store(Request $request)
    {
        $this->authorise();

        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'description'           => 'nullable|string',
            'applies_to'            => 'required|in:all_buyers,new_buyers,buyers_no_orders,specific_buyers',
            'new_buyer_days'        => 'nullable|integer|min:1|required_if:applies_to,new_buyers',
            'product_scope'         => 'required|in:all,specific_products,specific_sellers',
            'minimum_order_amount'  => 'nullable|numeric|min:0',
            'max_discount_amount'   => 'nullable|numeric|min:0',
            'starts_at'             => 'nullable|date',
            'ends_at'               => 'nullable|date|after_or_equal:starts_at',
            'is_active'             => 'boolean',
            'buyer_ids'             => 'nullable|array',
            'buyer_ids.*'           => 'uuid|exists:users,id',
            'product_ids'           => 'nullable|array',
            'product_ids.*'         => 'uuid|exists:products,id',
            'seller_ids'            => 'nullable|array',
            'seller_ids.*'          => 'uuid|exists:sellers,id',
        ]);

        $rule = FreeShippingRule::create([
            ...$data,
            'is_active'  => $request->boolean('is_active', true),
            'created_by' => Auth::guard('admin')->id(),
        ]);

        if ($data['applies_to'] === 'specific_buyers' && !empty($data['buyer_ids'])) {
            $rule->buyers()->sync($data['buyer_ids']);
        }
        if ($data['product_scope'] === 'specific_products' && !empty($data['product_ids'])) {
            $rule->products()->sync($data['product_ids']);
        }
        if ($data['product_scope'] === 'specific_sellers' && !empty($data['seller_ids'])) {
            $rule->sellers()->sync($data['seller_ids']);
        }

        // Queue announcement emails to all eligible buyers
        SendFreeShippingAnnouncementJob::dispatch($rule->id);

        return redirect()->route('admin.free-shipping.index')
            ->with('success', 'Free shipping rule created. Announcement emails are being sent.');
    }

    public function edit(FreeShippingRule $freeShipping)
    {
        $this->authorise();
        $buyers   = User::where('is_active', true)->select('id', 'first_name', 'last_name', 'email')->get();
        $products = Product::where('status', 'approved')->select('id', 'name')->get();
        $sellers  = Seller::where('is_active', true)->where('is_approved', true)->select('id', 'business_name')->get();
        return view('admin.free-shipping.edit', compact('freeShipping', 'buyers', 'products', 'sellers'));
    }

    public function update(Request $request, FreeShippingRule $freeShipping)
    {
        $this->authorise();

        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'description'           => 'nullable|string',
            'applies_to'            => 'required|in:all_buyers,new_buyers,buyers_no_orders,specific_buyers',
            'new_buyer_days'        => 'nullable|integer|min:1|required_if:applies_to,new_buyers',
            'product_scope'         => 'required|in:all,specific_products,specific_sellers',
            'minimum_order_amount'  => 'nullable|numeric|min:0',
            'max_discount_amount'   => 'nullable|numeric|min:0',
            'starts_at'             => 'nullable|date',
            'ends_at'               => 'nullable|date|after_or_equal:starts_at',
            'is_active'             => 'boolean',
            'buyer_ids'             => 'nullable|array',
            'buyer_ids.*'           => 'uuid|exists:users,id',
            'product_ids'           => 'nullable|array',
            'product_ids.*'         => 'uuid|exists:products,id',
            'seller_ids'            => 'nullable|array',
            'seller_ids.*'          => 'uuid|exists:sellers,id',
        ]);

        $freeShipping->update([
            ...$data,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $freeShipping->buyers()->sync(
            $data['applies_to'] === 'specific_buyers' ? ($data['buyer_ids'] ?? []) : []
        );
        $freeShipping->products()->sync(
            $data['product_scope'] === 'specific_products' ? ($data['product_ids'] ?? []) : []
        );
        $freeShipping->sellers()->sync(
            $data['product_scope'] === 'specific_sellers' ? ($data['seller_ids'] ?? []) : []
        );

        return redirect()->route('admin.free-shipping.index')
            ->with('success', 'Rule updated.');
    }

    public function toggle(FreeShippingRule $freeShipping)
    {
        $this->authorise();
        $freeShipping->update(['is_active' => !$freeShipping->is_active]);
        return back()->with('success', 'Rule ' . ($freeShipping->is_active ? 'activated' : 'disabled') . '.');
    }

    public function destroy(FreeShippingRule $freeShipping)
    {
        $this->authorise();
        $freeShipping->buyers()->detach();
        $freeShipping->products()->detach();
        $freeShipping->sellers()->detach();
        $freeShipping->delete();
        return redirect()->route('admin.free-shipping.index')->with('success', 'Rule deleted.');
    }
}