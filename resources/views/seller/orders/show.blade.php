@extends('layouts.seller')
@section('title', 'Order Details')
@section('page_title', 'Order #{{ $order->order_number }}')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
@endsection

@section('content')

<div class="row">

    {{-- Left: order items + actions --}}
    <div class="col-12 col-lg-8">

        {{-- Order items --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Order #{{ $order->order_number }}</h5>
                <span class="badge orderer-badge badge-{{ $order->status }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Item</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Qty</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Unit Price</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Total</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Your Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myItems as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->item_image)
                                            <img src="{{ $item->item_image }}"
                                                 style="width:46px;height:46px;object-fit:cover;border-radius:8px;border:1px solid #eee;"
                                                 alt="">
                                        @else
                                            <div style="width:46px;height:46px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                                <i class="feather-image text-muted"></i>
                                            </div>
                                        @endif
                                        <p class="mb-0 fw-semibold fs-13">{{ $item->item_name }}</p>
                                    </div>
                                </td>
                                <td class="fw-semibold">{{ $item->quantity }}</td>
                                <td class="fs-13">${{ number_format($item->unit_price, 2) }}</td>
                                <td><span class="fw-bold">${{ number_format($item->total_price, 2) }}</span></td>
                                <td>
                                    <span class="fw-bold text-success">${{ number_format($item->seller_earnings, 2) }}</span>
                                    <small class="text-muted d-block">after {{ $item->commission_rate }}% commission</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Escrow status --}}
        @if($escrow)
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Escrow Status</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <p class="text-muted fs-12 mb-1">Total Held</p>
                        <p class="fw-bold fs-15 mb-0">${{ number_format($escrow->amount, 2) }}</p>
                    </div>
                    <div class="col-6 col-md-3">
                        <p class="text-muted fs-12 mb-1">Your Share</p>
                        <p class="fw-bold fs-15 text-success mb-0">${{ number_format($escrow->seller_amount, 2) }}</p>
                    </div>
                    <div class="col-6 col-md-3">
                        <p class="text-muted fs-12 mb-1">Platform Commission</p>
                        <p class="fw-bold fs-15 text-danger mb-0">${{ number_format($escrow->commission_amount, 2) }}</p>
                    </div>
                    <div class="col-6 col-md-3">
                        <p class="text-muted fs-12 mb-1">Escrow Status</p>
                        <span class="badge orderer-badge badge-{{ $escrow->status }}">
                            {{ ucfirst($escrow->status) }}
                        </span>
                    </div>
                </div>
                @if($escrow->status === 'held')
                <div class="alert alert-warning mb-0">
                    <i class="feather-lock me-2"></i>
                    Funds will be released to your wallet when the buyer confirms delivery.
                    Auto-release on {{ $escrow->release_at?->format('M d, Y') }}.
                </div>
                @elseif($escrow->status === 'released')
                <div class="alert alert-success mb-0">
                    <i class="feather-check-circle me-2"></i>
                    Funds released to your wallet on {{ $escrow->released_at?->format('M d, Y') }}.
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Update status --}}
        @if(in_array($order->status, ['pending','confirmed']))
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Update Order Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('seller.orders.status', $order->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">New Status</label>
                        <select name="status" class="form-select">
                            @if($order->status === 'pending')
                            <option value="confirmed">Confirmed — I have the item ready</option>
                            @endif
                            @if(in_array($order->status, ['pending','confirmed']))
                            <option value="shipped">Shipped — Item is on the way</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Note <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="note"
                                  class="form-control"
                                  rows="2"
                                  placeholder="e.g. tracking number, courier name..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-check me-2"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Status history --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Status History</h5>
            </div>
            <div class="card-body">
                @foreach($order->statusLogs()->latest()->get() as $log)
                <div class="d-flex gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                    <div style="width:8px;height:8px;border-radius:50%;background:#2ECC71;margin-top:5px;flex-shrink:0;"></div>
                    <div>
                        <p class="mb-0 fs-13 fw-semibold">
                            {{ $log->from_status ? ucfirst($log->from_status).' → ' : '' }}{{ ucfirst($log->to_status) }}
                        </p>
                        @if($log->note)
                            <p class="mb-0 fs-13 text-muted mt-1">{{ $log->note }}</p>
                        @endif
                        <p class="mb-0 fs-11 text-muted mt-1">
                            by {{ ucfirst($log->changed_by_type) }} · {{ $log->created_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Right: delivery + payment --}}
    <div class="col-12 col-lg-4">

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Delivery Address</h5>
            </div>
            <div class="card-body">
                <p class="fw-bold mb-1">{{ $order->shipping_name }}</p>
                <p class="fs-13 text-muted mb-1">{{ $order->shipping_phone }}</p>
                <p class="fs-13 text-muted mb-1">{{ $order->shipping_address }}</p>
                <p class="fs-13 text-muted mb-1">{{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
                <p class="fs-13 text-muted mb-0">{{ $order->shipping_country }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Payment</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fs-13 text-muted">Method</span>
                    <span class="fs-13 fw-semibold">{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fs-13 text-muted">Payment status</span>
                    <span class="badge orderer-badge badge-{{ $order->payment_status }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fs-13 text-muted">Order total</span>
                    <span class="fw-bold">${{ number_format($order->total, 2) }}</span>
                </div>
                @if($order->payment_reference)
                <div class="mt-3 p-2 rounded" style="background:#f8f9fa;">
                    <p class="fs-11 text-muted mb-1">Payment reference</p>
                    <p class="fs-12 text-muted mb-0" style="word-break:break-all;">
                        {{ $order->payment_reference }}
                    </p>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>

@endsection