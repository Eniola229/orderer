@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page_title', 'Admin Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Overview</li>
@endsection

@section('content')

{{-- ── Pending alert strip ─────────────────────────────────────── --}}
@if($stats['pending_sellers'] || $stats['pending_products'] || $stats['pending_withdrawals'] || $stats['pending_ads'])
<div class="row mb-3">
    @if($stats['pending_sellers'])
    <div class="col-md-3 mb-2">
        <a href="{{ route('admin.sellers.pending') }}" class="text-decoration-none">
            <div class="alert mb-0 d-flex align-items-center gap-3"
                 style="background:#FEF9E7;border:1px solid #F9CA24;border-radius:10px;">
                <i class="feather-users" style="font-size:20px;color:#B7950B;flex-shrink:0;"></i>
                <div>
                    <strong style="color:#B7950B;">{{ $stats['pending_sellers'] }} Sellers</strong>
                    <p class="mb-0 text-muted" style="font-size:12px;">Awaiting approval</p>
                </div>
            </div>
        </a> 
    </div>
    @endif
    @if($stats['pending_products'])
    <div class="col-md-3 mb-2">
        <a href="{{ route('admin.products.pending') }}" class="text-decoration-none">
            <div class="alert mb-0 d-flex align-items-center gap-3"
                 style="background:#FEF9E7;border:1px solid #F9CA24;border-radius:10px;">
                <i class="feather-package" style="font-size:20px;color:#B7950B;flex-shrink:0;"></i>
                <div>
                    <strong style="color:#B7950B;">{{ $stats['pending_products'] }} Products</strong>
                    <p class="mb-0 text-muted" style="font-size:12px;">Awaiting review</p>
                </div>
            </div>
        </a>
    </div>
    @endif
    @if($stats['pending_withdrawals'])
    <div class="col-md-3 mb-2">
        <a href="{{ route('admin.withdrawals.index') }}" class="text-decoration-none">
            <div class="alert mb-0 d-flex align-items-center gap-3"
                 style="background:#FADBD8;border:1px solid #E74C3C;border-radius:10px;">
                <i class="feather-credit-card" style="font-size:20px;color:#A93226;flex-shrink:0;"></i>
                <div>
                    <strong style="color:#A93226;">{{ $stats['pending_withdrawals'] }} Withdrawals</strong>
                    <p class="mb-0 text-muted" style="font-size:12px;">Pending processing</p>
                </div>
            </div>
        </a>
    </div>
    @endif
    @if($stats['pending_ads'])
    <div class="col-md-3 mb-2">
        <a href="{{ route('admin.ads.pending') }}" class="text-decoration-none">
            <div class="alert mb-0 d-flex align-items-center gap-3"
                 style="background:#EBF5FB;border:1px solid #85C1E9;border-radius:10px;">
                <i class="feather-trending-up" style="font-size:20px;color:#1A5276;flex-shrink:0;"></i>
                <div>
                    <strong style="color:#1A5276;">{{ $stats['pending_ads'] }} Ads</strong>
                    <p class="mb-0 text-muted" style="font-size:12px;">Awaiting approval</p>
                </div>
            </div>
        </a>
    </div>
    @endif
    @if($stats['pending_flash_sales'])
    <div class="col-md-3 mb-2">
        <a href="{{ route('admin.flash-sales.index') }}" class="text-decoration-none">
            <div class="alert mb-0 d-flex align-items-center gap-3"
                 style="background:#F4ECF7;border:1px solid #8E44AD;border-radius:10px;">
                <i class="feather-zap" style="font-size:20px;color:#6C3483;flex-shrink:0;"></i>
                <div>
                    <strong style="color:#6C3483;">{{ $stats['pending_flash_sales'] }} Flash Sales</strong>
                    <p class="mb-0 text-muted" style="font-size:12px;">Awaiting admin approval</p>
                </div>
            </div>
        </a>
    </div>
    @endif
</div>
@endif

{{-- ── Filter bar ──────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.dashboard') }}" id="filterForm" class="d-flex align-items-center gap-3 flex-wrap">
            
            {{-- Date range pills --}}
            <span class="fw-semibold fs-13 text-muted me-1">Period:</span>
            <div class="btn-group">
                @foreach(['7'=>'7d','14'=>'14d','30'=>'30d','90'=>'90d','365'=>'1yr'] as $val => $label)
                <button type="submit"
                        name="range" value="{{ $val }}"
                        class="btn btn-sm {{ ($range ?? '30') == $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $label }}
                </button>
                @endforeach
                <button type="button"
                        class="btn btn-sm {{ ($range ?? '30') == 'custom' ? 'btn-primary' : 'btn-outline-secondary' }}"
                        onclick="toggleCustom()">
                    Custom
                </button>
            </div>

            @if(($selectedStatus ?? 'all') !== 'all' || ($range ?? '30') !== '30')
                <a href="{{ route('admin.dashboard') }}"
                   class="btn btn-sm btn-outline-secondary ms-1"
                   title="Reset all filters">
                    <i class="feather-x me-1"></i> Clear
                </a>
                @endif


            {{-- Custom date inputs --}}
            <div id="customRange"
                 class="{{ ($range ?? '30') == 'custom' ? 'd-flex' : 'd-none' }} gap-2 align-items-center">
                <input type="hidden" name="range" id="rangeHidden" value="custom">
                <input type="date" name="date_from"
                       class="form-control form-control-sm"
                       value="{{ request('date_from', $dateFrom->format('Y-m-d')) }}"
                       style="width:145px;">
                <span class="text-muted">–</span>
                <input type="date" name="date_to"
                       class="form-control form-control-sm"
                       value="{{ request('date_to', $dateTo->format('Y-m-d')) }}"
                       style="width:145px;">
                <button type="submit" class="btn btn-sm btn-primary">Apply</button>
            </div>

            {{-- Status filter --}}
            <span class="fw-semibold fs-13 text-muted ms-2 me-1">Status:</span>
            <div class="btn-group flex-wrap">
                @foreach($allStatuses as $s)
                @php
                    $sLabel = ucfirst($s);
                    $isActive = ($selectedStatus ?? 'all') === $s;
                    $btnClass = match($s) {
                        'all'       => $isActive ? 'btn-dark'    : 'btn-outline-secondary',
                        'pending'   => $isActive ? 'btn-warning'  : 'btn-outline-warning',
                        'confirmed' => $isActive ? 'btn-info'     : 'btn-outline-info',
                        'shipped'   => $isActive ? 'btn-primary'  : 'btn-outline-primary',
                        'delivered' => $isActive ? 'btn-success'  : 'btn-outline-success',
                        'completed' => $isActive ? 'btn-success'  : 'btn-outline-success',
                        'cancelled' => $isActive ? 'btn-danger'   : 'btn-outline-danger',
                        default     => $isActive ? 'btn-secondary': 'btn-outline-secondary',
                    };
                @endphp
                <button type="submit"
                        name="status" value="{{ $s }}"
                        class="btn btn-sm {{ $btnClass }}">
                    {{ $sLabel }}
                    @if($s === 'cancelled' && $stats['cancelled_orders'])
                    <span class="badge bg-danger ms-1" style="font-size:9px;">
                        {{ $stats['cancelled_orders'] }}
                    </span>
                    @endif
                </button>
                @endforeach
            </div>

            <span class="ms-auto text-muted fs-12">
                <strong>{{ $dateFrom->format('M d, Y') }}</strong>
                –
                <strong>{{ $dateTo->format('M d, Y') }}</strong>
                @if(($selectedStatus ?? 'all') !== 'all')
                · <span class="badge bg-secondary">{{ ucfirst($selectedStatus) }}</span>
                @endif
            </span>

        </form>
    </div>
</div>

{{-- ── Stat cards ──────────────────────────────────────────────── --}}
<div class="row">

    {{-- Revenue --}}
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Revenue</p>
                        <h2 class="fw-bold mb-0 text-success">
                            ₦{{ number_format($revenue, 2) }}
                        </h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#D5F5E3;color:#2ECC71;flex-shrink:0;">
                        <i class="feather-trending-up"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($revenueChange !== null)
                    <span class="badge {{ $revenueChange >= 0 ? 'bg-success' : 'bg-danger' }}">
                        <i class="feather-{{ $revenueChange >= 0 ? 'arrow-up' : 'arrow-down' }}"
                           style="font-size:10px;"></i>
                        {{ abs($revenueChange) }}%
                    </span>
                    @endif
                    <small class="text-muted">
                        Prev: ₦{{ number_format($prevRevenue, 2) }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Commission --}}
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Commission</p>
                        <h2 class="fw-bold mb-0 text-primary">
                            ₦{{ number_format($commission, 2) }}
                        </h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#EBF5FB;color:#2980B9;flex-shrink:0;">
                        <i class="feather-percent"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($commissionChange !== null)
                    <span class="badge {{ $commissionChange >= 0 ? 'bg-success' : 'bg-danger' }}">
                        <i class="feather-{{ $commissionChange >= 0 ? 'arrow-up' : 'arrow-down' }}"
                           style="font-size:10px;"></i>
                        {{ abs($commissionChange) }}%
                    </span>
                    @endif
                    <small class="text-muted">
                        Prev: ₦{{ number_format($prevCommission, 2) }}
                    </small>
                </div>
                @if($revenue > 0)
                <small class="text-muted d-block mt-1">
                    {{ round(($commission / $revenue) * 100, 1) }}% of revenue
                </small>
                @endif
            </div>
        </div>
    </div>

    {{-- Orders --}}
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Orders</p>
                        <h2 class="fw-bold mb-0">{{ number_format($stats['total_orders']) }}</h2>
                        <small class="text-warning fw-semibold d-block">
                            {{ $stats['pending_orders'] }} pending
                        </small>
                        <small class="text-danger fw-semibold">
                            {{ $stats['cancelled_orders'] }} cancelled
                        </small>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#FEF9E7;color:#F39C12;flex-shrink:0;">
                        <i class="feather-shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sellers / Buyers --}}
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Sellers / Buyers</p>
                        <h2 class="fw-bold mb-0">
                            {{ number_format($stats['total_sellers']) }}
                            <span class="text-muted fs-18 fw-normal">/</span>
                            {{ number_format($stats['total_buyers']) }}
                        </h2>
                        <small class="text-warning fw-semibold">
                            {{ $stats['pending_sellers'] }} sellers pending
                        </small>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#FADBD8;color:#E74C3C;flex-shrink:0;">
                        <i class="feather-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">

    {{-- Chart --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    Revenue &amp; Commission
                    <small class="text-muted fw-normal fs-12">
                        — {{ $dateFrom->format('M d') }} to {{ $dateTo->format('M d, Y') }}
                        @if(($selectedStatus ?? 'all') !== 'all')
                        · {{ ucfirst($selectedStatus) }} only
                        @endif
                    </small>
                </h5>
                <div class="d-flex gap-3">
                    <span style="font-size:12px;">
                        <span style="display:inline-block;width:10px;height:10px;background:#2ECC71;border-radius:50%;margin-right:4px;"></span>
                        Revenue
                    </span>
                    <span style="font-size:12px;">
                        <span style="display:inline-block;width:10px;height:10px;background:#2980B9;border-radius:50%;margin-right:4px;"></span>
                        Commission
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div id="revenueChart"></div>
            </div>
        </div>
    </div>

    {{-- Summary + Quick Actions --}}
    <div class="col-lg-4">

        {{-- Period summary mini card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Period Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Gross Revenue</span>
                    <strong>₦{{ number_format($revenue, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Commission</span>
                    <strong class="text-primary">₦{{ number_format($commission, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Net to Sellers</span>
                    <strong class="text-success">₦{{ number_format(max(0, $revenue - $commission), 2) }}</strong>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted fs-13">vs Previous Period</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted fs-12">Revenue</span>
                    <span class="{{ ($revenueChange ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fw-semibold fs-12">
                        {{ $revenueChange !== null ? (($revenueChange >= 0 ? '+' : '') . $revenueChange . '%') : 'N/A' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted fs-12">Commission</span>
                    <span class="{{ ($commissionChange ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fw-semibold fs-12">
                        {{ $commissionChange !== null ? (($commissionChange >= 0 ? '+' : '') . $commissionChange . '%') : 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">
                @if($admin->canModerateSellers())
                <a href="{{ route('admin.sellers.pending') }}" class="btn btn-outline-warning btn-sm">
                    <i class="feather-users me-2"></i> Review Sellers
                    @if($stats['pending_sellers'])
                    <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_sellers'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.products.pending') }}" class="btn btn-outline-primary btn-sm">
                    <i class="feather-package me-2"></i> Review Products
                    @if($stats['pending_products'])
                    <span class="badge bg-primary ms-1">{{ $stats['pending_products'] }}</span>
                    @endif
                </a>
                @endif
                @if($admin->canManageFinance())
                <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline-success btn-sm">
                    <i class="feather-credit-card me-2"></i> Process Withdrawals
                    @if($stats['pending_withdrawals'])
                    <span class="badge bg-success ms-1">{{ $stats['pending_withdrawals'] }}</span>
                    @endif
                </a>
                @endif
                @if($admin->canHandleSupport())
                <a href="{{ route('admin.support.open') }}" class="btn btn-outline-danger btn-sm">
                    <i class="feather-life-buoy me-2"></i> Open Tickets
                    @if($stats['open_tickets'])
                    <span class="badge bg-danger ms-1">{{ $stats['open_tickets'] }}</span>
                    @endif
                </a>
                @endif
                @if($admin->canManageAds())
                <a href="{{ route('admin.ads.pending') }}" class="btn btn-outline-info btn-sm">
                    <i class="feather-trending-up me-2"></i> Approve Ads
                    @if($stats['pending_ads'])
                    <span class="badge bg-info ms-1">{{ $stats['pending_ads'] }}</span>
                    @endif
                </a>
                @endif
                @if($admin->canManageAds())
                <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-outline-success btn-sm">
                    <i class="feather-zap me-2"></i> Review Flash Sales
                    @if($stats['pending_flash_sales'])
                    <span class="badge bg-info ms-1 text-muted">{{ $stats['pending_flash_sales'] }}</span>
                    @endif
                </a>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ── Recent Orders table ─────────────────────────────────────── --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    Recent Orders
                    @if(($selectedStatus ?? 'all') !== 'all')
                    <span class="badge bg-secondary ms-2 fw-normal fs-12">
                        {{ ucfirst($selectedStatus) }}
                    </span>
                    @endif
                </h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Order #</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Buyer</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Total</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Commission</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Payment</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="fw-semibold text-primary">
                                        #{{ $order->order_number ?? $order->id }}
                                    </a>
                                </td>
                                <td class="fs-13">{{ $order->user->name ?? $order->user->email ?? '—' }}</td>
                                <td class="fw-bold">₦{{ number_format($order->total, 2) }}</td>
                                <td>
                                    @if($order->status === 'cancelled')
                                    <span class="text-muted fs-12">—</span>
                                    @else
                                    <span class="text-primary fw-semibold">
                                        ₦{{ number_format($order->item_commission ?? 0, 2) }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $payBadge = match($order->payment_status) {
                                            'paid'     => 'bg-success',
                                            'pending'  => 'bg-warning text-dark',
                                            'failed'   => 'bg-danger',
                                            'refunded' => 'bg-secondary',
                                            default    => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $payBadge }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>                                </td>
                                </td>
                                <td>
                                    @php
                                        $statusBadge = match($order->status) {
                                            'pending'   => 'bg-warning text-dark',
                                            'confirmed' => 'bg-info',
                                            'shipped'   => 'bg-primary',
                                            'delivered' => 'bg-success',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            'disputed'  => 'bg-danger',
                                            default     => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusBadge }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No orders found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('dashboard/assets/vendors/js/apexcharts.min.js') }}"></script>
<script>
const chartData = @json($chartData);

const options = {
    series: [
        {
            name: 'Revenue (₦)',
            type: 'area',
            data: chartData.map(d => d.revenue)
        },
        {
            name: 'Commission (₦)',
            type: 'line',
            data: chartData.map(d => d.commission)
        }
    ],
    chart: {
        height: 270,
        toolbar: { show: false },
        zoom:    { enabled: false },
        fontFamily: 'inherit'
    },
    colors: ['#2ECC71', '#2980B9'],
    fill: {
        type: ['gradient', 'solid'],
        gradient: {
            shade: 'light',
            type: 'vertical',
            opacityFrom: 0.35,
            opacityTo: 0.02,
        }
    },
    stroke: {
        curve: 'smooth',
        width: [2, 2],
        dashArray: [0, 5]
    },
    markers: {
        size: [0, 3],
        colors: ['#2ECC71', '#2980B9'],
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: { size: 5 }
    },
    xaxis: {
        categories: chartData.map(d => d.label),
        labels: {
            style: { fontSize: '11px', colors: '#999' },
            rotate: chartData.length > 20 ? -45 : 0,
            rotateAlways: false
        },
        axisBorder: { show: false },
        axisTicks:  { show: false }
    },
    yaxis: {
        labels: {
            formatter: val => '₦' + (val >= 1000
                ? (val / 1000).toFixed(1) + 'k'
                : val.toFixed(0)),
            style: { fontSize: '11px', colors: '#999' }
        }
    },
    dataLabels: { enabled: false },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4,
        yaxis: { lines: { show: true } },
        xaxis: { lines: { show: false } }
    },
    tooltip: {
        shared: true,
        intersect: false,
        y: {
            formatter: val =>
                '₦' + parseFloat(val).toLocaleString('en-NG', { minimumFractionDigits: 2 })
        }
    },
    legend: { show: false }
};

const chart = new ApexCharts(document.getElementById('revenueChart'), options);
chart.render();

// Custom date range toggle
function toggleCustom() {
    const el = document.getElementById('customRange');
    el.classList.toggle('d-none');
    el.classList.toggle('d-flex');
    if (!el.classList.contains('d-none')) {
        document.getElementById('rangeHidden').value = 'custom';
    }
}
</script>
@endpush

@endsection