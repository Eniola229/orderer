@extends('layouts.seller')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Overview</li>
@endsection

@section('page_actions')
    <a href="{{ route('seller.products.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> Add Listing
    </a>
@endsection

@section('content')

{{-- Pending approval banner --}}
@if(!auth('seller')->user()->is_approved)
<div class="alert alert-warning d-flex align-items-center mb-3">
    <i class="feather-clock me-3" style="font-size:20px;"></i>
    <div>
        <strong>Account under review.</strong>
        Our team is reviewing your application. You'll receive an email once approved.
        You can explore the dashboard but cannot publish listings yet.
    </div>
</div>
@endif

{{-- Brand Creation Alert --}}
@if(!$hasBrand)
<div class="alert alert-info border-start border-4 border-primary mb-3" role="alert">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <i class="feather-award" style="font-size: 22px;"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-0">Create Your Brand to Grow Your Business! 🚀</h6>
                <p class="mb-0 text-muted fs-13">Build trust, increase visibility, and establish a professional identity.</p>
            </div>
        </div>
        <div class="d-flex gap-2 ms-3 flex-shrink-0">
            <a href="{{ route('seller.brand.index') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus-circle me-1"></i> Create Brand
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="this.closest('.alert').remove()">
                <i class="feather-x"></i>
            </button>
        </div>
    </div>
</div>
@endif

{{-- Stat cards --}}
<div class="row g-3 mb-3">
    <div class="col-xxl-3 col-md-6">
        <div class="card mb-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Wallet Balance</p>
                        <h4 class="fw-bold mb-0">₦{{ number_format($stats['wallet_balance'], 2) }}</h4>
                        <p class="text-muted fs-12 mt-1 mb-0"><i class="feather-arrow-up text-success me-1"></i>Available to withdraw</p>
                    </div>
                    <div class="avatar-text avatar-md rounded" style="background:#D5F5E3;color:#2ECC71;">
                        <i class="feather-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-md-6">
        <div class="card mb-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Orders</p>
                        <h4 class="fw-bold mb-0">{{ $stats['total_orders'] }}</h4>
                        <p class="text-muted fs-12 mt-1 mb-0"><span class="text-warning fw-semibold">{{ $stats['pending_orders'] }} pending</span></p>
                    </div>
                    <div class="avatar-text avatar-md rounded" style="background:#EBF5FB;color:#2980B9;">
                        <i class="feather-shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-md-6">
        <div class="card mb-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Listings</p>
                        <h4 class="fw-bold mb-0">{{ $stats['total_products'] }}</h4>
                        <p class="text-muted fs-12 mt-1 mb-0"><span class="text-success fw-semibold">{{ $stats['approved_products'] }} approved</span></p>
                    </div>
                    <div class="avatar-text avatar-md rounded" style="background:#FADBD8;color:#E74C3C;">
                        <i class="feather-package"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-md-6">
        <div class="card mb-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Ads Balance</p>
                        <h4 class="fw-bold mb-0">₦{{ number_format($stats['ads_balance'], 2) }}</h4>
                        <p class="text-muted fs-12 mt-1 mb-0">{{ $stats['active_ads'] }} active ad(s)</p>
                    </div>
                    <div class="avatar-text avatar-md rounded" style="background:#FEF9E7;color:#F39C12;">
                        <i class="feather-trending-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main content row --}}
<div class="row g-3">

    {{-- LEFT: Recent Orders (wider) --}}
    <div class="col-lg-8">

        {{-- Recent Orders --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Recent Orders</h5>
                <a href="{{ route('seller.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentOrders->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Order #</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Buyer</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('seller.orders.show', $item->order->id) }}" class="fw-semibold text-primary">
                                        #{{ $item->order->order_number }}
                                    </a>
                                </td>
                                <td class="fs-13">{{ $item->order->shipping_name }}</td>
                                <td class="fw-bold">₦{{ number_format($item->total_price, 2) }}</td>
                                <td>
                                    <span class="badge orderer-badge badge-{{ $item->status }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">{{ $item->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="feather-shopping-bag mb-2 d-block" style="font-size:32px;"></i>
                    No orders yet.
                </div>
                @endif
            </div>
        </div>

        {{-- Low stock & Top products --}}
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100 mb-0">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0"><i class="feather-alert-triangle text-warning me-2"></i>Low Stock</h5>
                        <a href="{{ route('seller.products.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
                    </div>
                    <div class="card-body p-0">
                        @if($lowStockProducts->count())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fs-11 text-uppercase text-muted fw-semibold">Product</th>
                                        <th class="fs-11 text-uppercase text-muted fw-semibold text-center">Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $lsp)
                                    @php $lspImg = $lsp->images->where('is_primary',true)->first() ?? $lsp->images->first(); @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($lspImg)
                                                    <img src="{{ $lspImg->image_url }}" style="width:30px;height:30px;object-fit:cover;border-radius:6px;" alt="">
                                                @else
                                                    <div style="width:30px;height:30px;background:#f5f5f5;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                                        <i class="feather-image text-muted" style="font-size:12px;"></i>
                                                    </div>
                                                @endif
                                                <a href="{{ route('seller.products.edit', $lsp->id) }}" class="fs-12 fw-semibold text-dark text-decoration-none">
                                                    {{ Str::limit($lsp->name, 22) }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($lsp->stock === 0)
                                                <span class="badge bg-danger">Out</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ $lsp->stock }} left</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="feather-check-circle mb-2 d-block text-success" style="font-size:26px;"></i>
                            All products well-stocked.
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 mb-0">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0"><i class="feather-trending-up text-success me-2"></i>Top Selling</h5>
                        <a href="{{ route('seller.products.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        @if($topProducts->count())
                        @php $maxSold = $topProducts->first()->total_sold; @endphp
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fs-11 text-uppercase text-muted fw-semibold">#</th>
                                        <th class="fs-11 text-uppercase text-muted fw-semibold">Product</th>
                                        <th class="fs-11 text-uppercase text-muted fw-semibold text-end">Sold</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProducts as $idx => $tp)
                                    @php $tpImg = $tp->images->where('is_primary',true)->first() ?? $tp->images->first(); @endphp
                                    <tr>
                                        <td>
                                            <span style="width:20px;height:20px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;background:{{ $idx===0 ? '#FFD700' : ($idx===1 ? '#C0C0C0' : ($idx===2 ? '#CD7F32' : '#f5f5f5')) }};color:{{ $idx<=2 ? '#fff' : '#888' }};">
                                                {{ $idx + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($tpImg)
                                                    <img src="{{ $tpImg->image_url }}" style="width:30px;height:30px;object-fit:cover;border-radius:6px;" alt="">
                                                @else
                                                    <div style="width:30px;height:30px;background:#f5f5f5;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                                        <i class="feather-image text-muted" style="font-size:12px;"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="fs-12 fw-semibold mb-0">{{ Str::limit($tp->name, 18) }}</p>
                                                    <div style="height:3px;border-radius:2px;background:#eee;width:60px;margin-top:3px;">
                                                        <div style="height:3px;border-radius:2px;background:#2ECC71;width:{{ $maxSold > 0 ? round(($tp->total_sold / $maxSold) * 100) : 0 }}%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-bold fs-12 text-end">{{ number_format($tp->total_sold) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="feather-bar-chart-2 mb-2 d-block" style="font-size:26px;"></i>
                            No sales recorded yet.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT: Chart + Listings + Quick Actions --}}
    <div class="col-lg-4">

        {{-- Chart (compact) --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Orders & Revenue</h5>
                <div class="d-flex gap-2" style="font-size:11px;">
                    <span class="d-flex align-items-center gap-1">
                        <span style="width:8px;height:8px;border-radius:2px;background:#2980B9;display:inline-block;"></span> Orders
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <span style="width:8px;height:8px;border-radius:2px;background:#2ECC71;display:inline-block;"></span> Revenue
                    </span>
                </div>
            </div>
            <div class="card-body p-2">
                <div style="position:relative;height:180px;">
                    <canvas id="ordersChart" role="img" aria-label="Orders and revenue over last 12 months"></canvas>
                </div>
            </div>
        </div>

        {{-- Recent Listings --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Recent Listings</h5>
                <a href="{{ route('seller.products.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentProducts->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Product</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold text-end">Price</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentProducts as $product)
                            @php $img = $product->images->where('is_primary', true)->first() ?? $product->images->first(); @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($img)
                                            <img src="{{ $img->image_url }}" style="width:30px;height:30px;object-fit:cover;border-radius:6px;" alt="">
                                        @else
                                            <div style="width:30px;height:30px;background:#f5f5f5;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                                <i class="feather-image text-muted" style="font-size:12px;"></i>
                                            </div>
                                        @endif
                                        <span class="fs-12 fw-semibold">{{ Str::limit($product->name, 18) }}</span>
                                    </div>
                                </td>
                                <td class="fw-bold fs-12 text-end">₦{{ number_format($product->price, 2) }}</td>
                                <td>
                                    <span class="badge orderer-badge badge-{{ $product->status }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="feather-package mb-2 d-block" style="font-size:26px;"></i>
                    No listings yet.
                    <a href="{{ route('seller.products.create') }}" class="text-primary d-block mt-1 fs-13">Add your first product</a>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card mb-0">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('seller.products.create') }}" class="btn btn-primary btn-sm">
                        <i class="feather-plus me-2"></i> Add Product
                    </a>
                    <a href="{{ route('seller.services.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="feather-tool me-2"></i> Add Service
                    </a>
                    <a href="{{ route('seller.houses.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="feather-home me-2"></i> Add Property
                    </a>
                    <a href="{{ route('seller.ads.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="feather-trending-up me-2"></i> Create Ad
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.card { margin-bottom: 0; }
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
(function() {
    const labels  = @json($chartLabels);
    const orders  = @json($chartOrders);
    const revenue = @json($chartRevenue);

    new Chart(document.getElementById('ordersChart'), {
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Orders',
                    data: orders,
                    backgroundColor: 'rgba(41, 128, 185, 0.18)',
                    borderColor: '#2980B9',
                    borderWidth: 1.5,
                    borderRadius: 4,
                    yAxisID: 'yOrders',
                },
                {
                    type: 'line',
                    label: 'Revenue (₦)',
                    data: revenue,
                    borderColor: '#2ECC71',
                    backgroundColor: 'rgba(46, 204, 113, 0.08)',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointBackgroundColor: '#2ECC71',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'yRevenue',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            if (ctx.dataset.label === 'Revenue (₦)') {
                                return ' Revenue: ₦' + ctx.parsed.y.toLocaleString('en-NG', { minimumFractionDigits: 0 });
                            }
                            return ' Orders: ' + ctx.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, maxRotation: 45, autoSkip: true, maxTicksLimit: 6 }
                },
                yOrders: {
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        font: { size: 10 },
                        stepSize: 1,
                        callback: v => Number.isInteger(v) ? v : ''
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                yRevenue: {
                    position: 'right',
                    beginAtZero: true,
                    grid: { display: false },
                    ticks: {
                        font: { size: 10 },
                        callback: v => '₦' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                    }
                }
            }
        }
    });
})();
</script>
@endpush
@endsection