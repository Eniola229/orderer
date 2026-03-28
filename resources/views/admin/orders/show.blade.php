@extends('layouts.admin')
@section('title', 'Order #' . $order->order_number)
@section('page_title', 'Order #' . $order->order_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-8">

        {{-- Items --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Order Items</h5>
                <span class="badge orderer-badge badge-{{ $order->status }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Item</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Qty</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Commission</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Earnings</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($item->item_image)
                                        <img src="{{ $item->item_image }}"
                                             style="width:40px;height:40px;object-fit:cover;border-radius:6px;" alt="">
                                        @endif
                                        <span class="fw-semibold fs-13">{{ Str::limit($item->item_name,30) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.sellers.show', $item->seller_id) }}"
                                       class="fs-13 text-primary">
                                        {{ $item->seller->business_name ?? '—' }}
                                    </a>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td class="fw-bold">${{ number_format($item->total_price, 2) }}</td>
                                <td class="text-danger">
                                    ${{ number_format($item->commission_amount, 2) }}
                                    <small class="text-muted">({{ $item->commission_rate }}%)</small>
                                </td>
                                <td class="text-success fw-bold">
                                    ${{ number_format($item->seller_earnings, 2) }}
                                </td>
                                <td>
                                    <span class="badge orderer-badge badge-{{ $item->status }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Admin actions — finance role required --}}
        @if(auth('admin')->user()->canEditOrders() && !in_array($order->status, ['completed','cancelled']))
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Admin Actions</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-3">
                    <i class="feather-alert-triangle me-2"></i>
                    These actions are irreversible and will be logged.
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <form action="{{ route('admin.orders.complete', $order->id) }}"
                          method="POST"
                          onsubmit="return confirm('Force-complete this order and release escrow to sellers?')">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-success">
                            <i class="feather-check-circle me-2"></i> Force Complete &amp; Release Escrow
                        </button>
                    </form>
                    <form action="{{ route('admin.orders.refund', $order->id) }}"
                          method="POST"
                          onsubmit="return confirm('Refund this order back to the buyer\'s wallet?')">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-danger">
                            <i class="feather-rotate-ccw me-2"></i> Cancel &amp; Refund Buyer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Timeline --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Timeline</h5>
            </div>
            <div class="card-body">
                @forelse($order->statusLogs()->latest()->get() as $log)
                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                    <div class="avatar-text avatar-sm rounded bg-primary text-white flex-shrink-0"
                         style="width:32px;height:32px;">
                        <i class="feather-activity" style="font-size:12px;"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold fs-13">
                            @if($log->from_status)
                                {{ ucfirst($log->from_status) }} →
                            @endif
                            {{ ucfirst($log->to_status) }}
                        </p>
                        @if($log->note)
                        <p class="mb-0 text-muted fs-13">{{ $log->note }}</p>
                        @endif
                        <small class="text-muted">
                            by {{ ucfirst($log->changed_by_type) }}
                            · {{ $log->created_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
                @empty
                <p class="text-muted fs-13">No history yet.</p>
                @endforelse
            </div>
        </div>

    </div>

    <div class="col-lg-4">

        {{-- Summary --}}
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title mb-0">Order Summary</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-semibold">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="fw-semibold">${{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Commission</span>
                    <span class="fw-semibold text-primary">${{ number_format($order->commission_total ?? 0, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Total</span>
                    <span class="fw-bold text-primary">${{ number_format($order->total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Payment</span>
                    <span class="fw-semibold fs-13">{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Payment Status</span>
                    <span class="badge orderer-badge badge-{{ $order->payment_status }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->payment_reference)
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted d-block">Reference</small>
                    <code class="fs-12">{{ $order->payment_reference }}</code>
                </div>
                @endif
                @if($order->escrow)
                <div class="mt-2 p-2 rounded" style="background:#FEF9E7;">
                    <small class="text-muted d-block">Escrow</small>
                    <span class="badge orderer-badge badge-{{ $order->escrow->status }}">
                        {{ ucfirst($order->escrow->status) }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Buyer --}}
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title mb-0">Buyer</h5></div>
            <div class="card-body">
                @if($order->user)
                <p class="mb-1 fw-bold">{{ $order->user->first_name }} {{ $order->user->last_name }}</p>
                <p class="mb-1 text-muted fs-13">{{ $order->user->email }}</p>
                <p class="mb-2 text-muted fs-13">{{ $order->user->phone ?? '—' }}</p>
                <a href="{{ route('admin.buyers.show', $order->user_id) }}"
                   class="btn btn-sm btn-outline-primary w-100">View Buyer Profile</a>
                @else
                <p class="text-muted">User not found.</p>
                @endif
            </div>
        </div>

        {{-- Delivery --}}
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title mb-0">Delivery Address</h5></div>
            <div class="card-body">
                <p class="mb-1 fw-bold">{{ $order->shipping_name }}</p>
                <p class="mb-1 text-muted fs-13">{{ $order->shipping_phone }}</p>
                <p class="mb-1 text-muted fs-13">{{ $order->shipping_address }}</p>
                <p class="mb-1 text-muted fs-13">{{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
                <p class="mb-0 text-muted fs-13">{{ $order->shipping_country }}</p>
            </div>
        </div>

        {{-- Shipment --}}
        @if($order->tracking_number)
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Shipment</h5></div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted d-block">Carrier</small>
                    <strong>{{ $order->shipping_carrier }} — {{ $order->shipping_service_name }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Tracking #</small>
                    <code class="text-primary">{{ $order->tracking_number }}</code>
                </div>
                @if($order->estimated_delivery_date)
                <div>
                    <small class="text-muted d-block">Est. Delivery</small>
                    <strong>{{ $order->estimated_delivery_date }}</strong>
                </div>
                @endif
                @if($order->tracking_url)
                <a href="{{ $order->tracking_url }}" target="_blank"
                   class="btn btn-sm btn-outline-primary w-100 mt-2">
                    Track Shipment
                </a>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>

@endsection