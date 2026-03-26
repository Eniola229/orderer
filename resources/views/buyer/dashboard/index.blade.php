@extends('layouts.buyer')
@section('title', 'My Dashboard')
@section('page_title', 'My Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Overview</li>
@endsection
@section('page_actions')
    <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm">
        <i class="feather-shopping-bag me-1"></i> Continue Shopping
    </a>
@endsection

@section('content')

<div class="row">
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Wallet Balance</p>
                        <h3 class="fw-bold mb-0">${{ number_format($stats['wallet_balance'], 2) }}</h3>
                        <p class="text-muted fs-12 mt-1 mb-0">Available to spend</p>
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
                        <h3 class="fw-bold mb-0">{{ $stats['total_orders'] }}</h3>
                        <p class="text-muted fs-12 mt-1 mb-0">
                            <span class="text-warning fw-semibold">{{ $stats['pending_orders'] }} pending</span>
                        </p>
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
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Completed Orders</p>
                        <h3 class="fw-bold mb-0">{{ $stats['completed_orders'] }}</h3>
                        <p class="text-muted fs-12 mt-1 mb-0 text-success fw-semibold">All delivered</p>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#D5F5E3;color:#2ECC71;">
                        <i class="feather-check-circle"></i>
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
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Wishlist</p>
                        <h3 class="fw-bold mb-0">{{ $stats['wishlist_count'] }}</h3>
                        <p class="text-muted fs-12 mt-1 mb-0">Saved items</p>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#FADBD8;color:#E74C3C;">
                        <i class="feather-heart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Recent Orders</h5>
                <a href="{{ route('buyer.orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentOrders->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Order #</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Items</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Total</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('buyer.orders.show', $order->id) }}"
                                       class="fw-semibold text-primary">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="text-muted fs-13">{{ $order->items->count() }} item(s)</td>
                                <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge orderer-badge badge-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="feather-shopping-bag mb-2 d-block" style="font-size:36px;"></i>
                    <p class="mb-3">No orders yet.</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm">
                        Start Shopping
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">

        {{-- Quick actions --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('shop.index') }}" class="btn btn-primary">
                        <i class="feather-shopping-bag me-2"></i> Browse Shop
                    </a>
                    <a href="{{ route('buyer.wallet') }}" class="btn btn-outline-primary">
                        <i class="feather-dollar-sign me-2"></i> Top Up Wallet
                    </a>
                    <a href="{{ route('rider.booking') }}" class="btn btn-outline-primary">
                        <i class="feather-truck me-2"></i> Book a Rider
                    </a>
                    <a href="{{ route('buyer.referral') }}" class="btn btn-outline-primary">
                        <i class="feather-gift me-2"></i> Refer &amp; Earn
                    </a>
                </div>
            </div>
        </div>

        {{-- Notifications --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Notifications</h5>
            </div>
            <div class="card-body p-0">
                @forelse($notifications as $notif)
                <div class="d-flex gap-3 p-3 border-bottom">
                    <div class="avatar-text avatar-sm rounded bg-primary text-white flex-shrink-0">
                        <i class="feather-bell" style="font-size:12px;"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold fs-13">{{ $notif->title }}</p>
                        <p class="mb-0 text-muted fs-12">{{ Str::limit($notif->body, 60) }}</p>
                        <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="feather-bell mb-2 d-block" style="font-size:24px;"></i>
                    No new notifications
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection
