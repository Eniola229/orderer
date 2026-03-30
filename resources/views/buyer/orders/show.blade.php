@extends('layouts.buyer')
@section('title', 'Order #' . $order->order_number)
@section('page_title', 'Order #' . $order->order_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('buyer.orders') }}">Orders</a></li>
    <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
@endsection

@section('content')

@php
function orderStatusBadge(string $status): string {
    return match($status) {
        'pending'    => 'bg-warning text-dark',
        'confirmed'  => 'bg-info text-white',
        'processing' => 'bg-primary text-white',
        'shipped'    => 'bg-primary text-white',
        'delivered'  => 'bg-success text-white',
        'completed'  => 'bg-success text-white',
        'cancelled'  => 'bg-danger text-white',
        'disputed'   => 'bg-danger text-white',
        'paid'       => 'bg-success text-white',
        'failed'     => 'bg-danger text-white',
        'refunded'   => 'bg-secondary text-white',
        default      => 'bg-secondary text-white',
    };
}
@endphp

<div class="row">
    <div class="col-lg-8">

        {{-- Items --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Order Items</h5>
                <span class="badge {{ orderStatusBadge($order->status) }}">
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
                                        <span class="fw-semibold fs-13">{{ $item->item_name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted fs-13">{{ $item->seller->business_name ?? '—' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="fw-bold">${{ number_format($item->total_price, 2) }}</td>
                                <td>
                                    <span class="badge {{ orderStatusBadge($item->status) }}">
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

        {{-- Confirm delivery --}}
        @if($order->status === 'shipped')
        <div class="card mb-3">
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="feather-truck me-2"></i>
                    Your order is on the way! Confirm delivery once you receive it to release payment to the seller.
                </div>
                <form action="{{ route('buyer.orders.confirm', $order->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-success"
                            onclick="return confirm('Confirm you have received this order?')">
                        <i class="feather-check-circle me-2"></i> Confirm Delivery
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Status timeline --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Timeline</h5>
            </div>
            <div class="card-body">
                @foreach($order->statusLogs()->latest()->get() as $log)
                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                    <div class="avatar-text avatar-sm rounded bg-primary text-white flex-shrink-0">
                        <i class="feather-activity" style="font-size:12px;"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold fs-13">
                            {{ $log->from_status ? ucfirst($log->from_status) . ' → ' : '' }}
                            {{ ucfirst($log->to_status) }}
                        </p>
                        @if($log->note)
                        <p class="mb-0 text-muted fs-13">{{ $log->note }}</p>
                        @endif
                        <small class="text-muted">
                            by {{ ucfirst($log->changed_by_type) }} ·
                            {{ $log->created_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <div class="col-lg-4">

        {{-- Order summary --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-semibold">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="fw-semibold">${{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total</span>
                    <span class="fw-bold text-primary">${{ number_format($order->total, 2) }}</span>
                </div>
                <div class="mt-3">
                    <small class="text-muted d-block">Payment method</small>
                    <strong>{{ ucfirst($order->payment_method) }}</strong>
                </div>
                <div class="mt-2">
                    <small class="text-muted d-block">Payment status</small>
                    <span class="badge {{ orderStatusBadge($order->payment_status) }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->payment_reference)
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted d-block">Reference</small>
                    <code class="fs-12">{{ $order->payment_reference }}</code>
                </div>
                @endif
                <div class="mt-3">
                    <small class="text-muted d-block">Payment method</small>
                <strong>{{ ucfirst($order->payment_method) }}</strong>
            </div>
            </div>
        </div>

        {{-- Shipping / Courier info --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Shipping Info</h5>
            </div>
            <div class="card-body">

                {{-- Carrier --}}
                @if($order->shipping_carrier)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Carrier</small>
                    <div class="d-flex align-items-center gap-2">
                        <i class="feather-truck text-primary"></i>
                        <span class="fw-semibold">{{ $order->shipping_carrier }}</span>
                        @if($order->shipping_service_name)
                        <span class="text-muted fs-12">— {{ $order->shipping_service_name }}</span>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Shipbubble order ID --}}
                @if($order->shipbubble_shipment_id)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Shipment ID</small>
                    <code class="fs-12">{{ $order->shipbubble_shipment_id }}</code>
                </div>
                @endif

                {{-- Tracking number --}}
                @if($order->tracking_number)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Tracking Number</small>
                    <code class="fs-12">{{ $order->tracking_number }}</code>
                </div>
                @endif

                {{-- Estimated delivery --}}
                @if($order->estimated_delivery_date)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Estimated Delivery</small>
                    <span class="fw-semibold text-success">
                        <i class="feather-calendar me-1"></i>
                        {{ $order->estimated_delivery_date }}
                    </span>
                </div>
                @endif

                {{-- Track button --}}
                @if($order->tracking_url)
                <a href="{{ $order->tracking_url }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                    <i class="feather-map-pin me-2"></i> Track Shipment
                </a>
                @else
                <div class="text-muted fs-12 text-center py-2">
                    <i class="feather-clock me-1"></i>
                    Tracking details will appear here once your order is shipped.
                </div>
                @endif

            </div>
        </div>

        {{-- Delivery address --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Delivery Address</h5>
            </div>
            <div class="card-body">
                <p class="mb-1 fw-bold">{{ $order->shipping_name }}</p>
                <p class="mb-1 text-muted fs-13">{{ $order->shipping_phone }}</p>
                <p class="mb-1 text-muted fs-13">{{ $order->shipping_address }}</p>
                <p class="mb-1 text-muted fs-13">{{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
                <p class="mb-0 text-muted fs-13">{{ $order->shipping_country }}</p>
            </div>
        </div>

    </div>
</div>

@endsection