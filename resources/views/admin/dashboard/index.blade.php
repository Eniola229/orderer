@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page_title', 'Admin Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Overview</li>
@endsection

@section('content')

{{-- Alert cards (pending items) --}}
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
                <i class="feather-dollar-sign" style="font-size:20px;color:#A93226;flex-shrink:0;"></i>
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
</div>
@endif

{{-- Main stat cards --}}
<div class="row">
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">30-Day Revenue</p>
                        <h2 class="fw-bold mb-0 text-success">₦{{ number_format($revenue, 2) }}</h2>
                        <small class="text-muted">Commission: ₦{{ number_format($commission, 2) }}</small>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#D5F5E3;color:#2ECC71;">
                        <i class="feather-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Orders</p>
                        <h2 class="fw-bold mb-0">{{ number_format($stats['total_orders']) }}</h2>
                        <small class="text-warning fw-semibold">{{ $stats['pending_orders'] }} pending</small>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#EBF5FB;color:#2980B9;">
                        <i class="feather-shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Sellers</p>
                        <h2 class="fw-bold mb-0">{{ number_format($stats['total_sellers']) }}</h2>
                        <small class="text-warning fw-semibold">{{ $stats['pending_sellers'] }} pending</small>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#FEF9E7;color:#F39C12;">
                        <i class="feather-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Buyers</p>
                        <h2 class="fw-bold mb-0">{{ number_format($stats['total_buyers']) }}</h2>
                        <small class="text-muted">Registered users</small>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#FADBD8;color:#E74C3C;">
                        <i class="feather-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Revenue chart --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Revenue — Last 14 Days</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">
                @if($admin->canModerateSellers())
                <a href="{{ route('admin.sellers.pending') }}" class="btn btn-outline-warning">
                    <i class="feather-users me-2"></i>
                    Review Sellers
                    @if($stats['pending_sellers'])
                    <span class="badge bg-warning text-dark ms-1">{{ $stats['pending_sellers'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.products.pending') }}" class="btn btn-outline-primary">
                    <i class="feather-package me-2"></i>
                    Review Products
                    @if($stats['pending_products'])
                    <span class="badge bg-primary ms-1">{{ $stats['pending_products'] }}</span>
                    @endif
                </a>
                @endif
                @if($admin->canManageFinance())
                <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline-success">
                    <i class="feather-dollar-sign me-2"></i>
                    Process Withdrawals
                    @if($stats['pending_withdrawals'])
                    <span class="badge bg-success ms-1">{{ $stats['pending_withdrawals'] }}</span>
                    @endif
                </a>
                @endif
                @if($admin->canHandleSupport())
                <a href="{{ route('admin.support.open') }}" class="btn btn-outline-danger">
                    <i class="feather-life-buoy me-2"></i>
                    Open Tickets
                    @if($stats['open_tickets'])
                    <span class="badge bg-danger ms-1">{{ $stats['open_tickets'] }}</span>
                    @endif
                </a>
                @endif
                @if($admin->canManageAds())
                <a href="{{ route('admin.ads.pending') }}" class="btn btn-outline-info">
                    <i class="feather-trending-up me-2"></i>
                    Approve Ads
                    @if($stats['pending_ads'])
                    <span class="badge bg-info ms-1">{{ $stats['pending_ads'] }}</span>
                    @endif
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Recent orders --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Recent Orders</h5>
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
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Payment</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="fw-semibold text-primary">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="fs-13">{{ $order->user->email ?? '—' }}</td>
                                <td class="fw-bold">₦{{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge orderer-badge badge-{{ $order->payment_status }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge orderer-badge badge-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @endforeach
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
    series: [{
        name: 'Revenue (₦)',
        data: chartData.map(d => d.value)
    }],
    chart: {
        type: 'area',
        height: 200,
        toolbar: { show: false },
        sparkline: { enabled: false }
    },
    colors: ['#2ECC71'],
    fill: {
        type: 'gradient',
        gradient: { opacityFrom: 0.4, opacityTo: 0.05 }
    },
    stroke: { curve: 'smooth', width: 2 },
    xaxis: {
        categories: chartData.map(d => d.label),
        labels: { style: { fontSize: '11px' } }
    },
    yaxis: {
        labels: {
            formatter: val => '₦' + val.toFixed(0)
        }
    },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f1f1f1' },
    tooltip: {
        y: { formatter: val => '₦' + val.toFixed(2) }
    }
};

const chart = new ApexCharts(document.getElementById('revenueChart'), options);
chart.render();
</script>
@endpush

@endsection