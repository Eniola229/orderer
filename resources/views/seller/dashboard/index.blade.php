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
<div class="alert alert-warning d-flex align-items-center mb-4">
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
<div class="alert alert-info border-start border-4 border-primary mb-4" role="alert">
    <div class="d-flex align-items-start">
        <i class="feather-award me-3 mt-1" style="font-size: 24px;"></i>
        <div class="flex-grow-1">
            <h6 class="alert-heading fw-bold mb-2">Create Your Brand to Grow Your Business! 🚀</h6>
            <p class="mb-2">Having a brand on our platform helps you:</p>
            <ul class="mb-3 ps-3">
                <li>Build trust and credibility with customers</li>
                <li>Increase product visibility and sales</li>
                <li>Get featured in brand-specific promotions</li>
                <li>Establish a professional business identity</li>
            </ul>
            <a href="{{ route('seller.brand.index') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus-circle me-1"></i> Create Your Brand Now
            </a>
            <button type="button" class="btn btn-link btn-sm text-muted ms-2" onclick="this.closest('.alert').remove()">
                <i class="feather-x"></i> Dismiss
            </button>
        </div>
    </div>
</div>
@endif

{{-- Stat cards --}}
<div class="row">
    <div class="col-xxl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Wallet Balance</p>
                        <h3 class="fw-bold mb-0">₦{{ number_format($stats['wallet_balance'], 2) }}</h3>
                        <p class="text-muted fs-12 mt-1 mb-0">
                            <i class="feather-arrow-up text-success me-1"></i>
                            Available to withdraw
                        </p>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#D5F5E3;color:#2ECC71;">
                        <i class="feather-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Orders</p>
                        <h3 class="fw-bold mb-0">{{ $stats['total_orders'] }}</h3>
                        <p class="text-muted fs-12 mt-1 mb-0">
                            <span class="text-warning fw-semibold">{{ $stats['pending_orders'] }} pending</span>
                        </p>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#EBF5FB;color:#2980B9;">
                        <i class="feather-shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Listings</p>
                        <h3 class="fw-bold mb-0">{{ $stats['total_products'] }}</h3>
                        <p class="text-muted fs-12 mt-1 mb-0">
                            <span class="text-success fw-semibold">{{ $stats['approved_products'] }} approved</span>
                        </p>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#FADBD8;color:#E74C3C;">
                        <i class="feather-package"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Ads Balance</p>
                        <h3 class="fw-bold mb-0">₦{{ number_format($stats['ads_balance'], 2) }}</h3>
                        <p class="text-muted fs-12 mt-1 mb-0">
                            {{ $stats['active_ads'] }} active ad(s)
                        </p>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#FEF9E7;color:#F39C12;">
                        <i class="feather-trending-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">

    {{-- Recent Orders --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Recent Orders</h5>
                <a href="{{ route('seller.orders.index') }}"
                   class="btn btn-sm btn-outline-primary">View All</a>
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
                                    <a href="{{ route('seller.orders.show', $item->order->id) }}"
                                       class="fw-semibold text-primary">
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
                                <td class="text-muted fs-12">
                                    {{ $item->created_at->format('M d, Y') }}
                                </td>
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
    </div>

    {{-- Right column --}}
    <div class="col-lg-5">

        {{-- Recent listings --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Recent Listings</h5>
                <a href="{{ route('seller.products.index') }}"
                   class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentProducts->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Product</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
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
                                            <img src="{{ $img->image_url }}"
                                                 style="width:36px;height:36px;object-fit:cover;border-radius:6px;"
                                                 alt="">
                                        @else
                                            <div style="width:36px;height:36px;background:#f5f5f5;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                                <i class="feather-image text-muted" style="font-size:14px;"></i>
                                            </div>
                                        @endif
                                        <span class="fs-13 fw-semibold">
                                            {{ Str::limit($product->name, 25) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="fw-bold fs-13">
                                    ₦{{ number_format($product->price, 2) }}
                                </td>
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
                    <i class="feather-package mb-2 d-block" style="font-size:28px;"></i>
                    No listings yet.
                    <a href="{{ route('seller.products.create') }}" class="text-primary d-block mt-1">
                        Add your first product
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('seller.products.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i> Add Product
                    </a>
                    <a href="{{ route('seller.services.create') }}" class="btn btn-outline-primary">
                        <i class="feather-tool me-2"></i> Add Service
                    </a>
                    <a href="{{ route('seller.houses.create') }}" class="btn btn-outline-primary">
                        <i class="feather-home me-2"></i> Add Property
                    </a>
                    <a href="{{ route('seller.ads.create') }}" class="btn btn-outline-primary">
                        <i class="feather-trending-up me-2"></i> Create Ad
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection