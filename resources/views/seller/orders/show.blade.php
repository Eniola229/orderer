@extends('layouts.seller')
@section('title', 'Order #' . $order->order_number)
@section('page_title', 'Order #' . $order->order_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
@endsection

@section('content')

@php
function sellerOrderStatusBadge(string $status): string {
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

        {{-- My items in this order --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Order Items (Your Listings)</h5>
                <span class="badge {{ sellerOrderStatusBadge($order->status) }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Item</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Qty</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Unit Price</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Commission</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Your Earnings</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myItems as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($item->item_image)
                                        <img src="{{ $item->item_image }}"
                                             style="width:44px;height:44px;object-fit:cover;border-radius:8px;" alt="">
                                        @endif
                                        <span class="fw-semibold fs-13">{{ $item->item_name }}</span>
                                    </div>
                                </td>
                                <td class="fw-semibold">{{ $item->quantity }}</td>
                                <td>₦{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-danger">
                                    -₦{{ number_format($item->commission_amount, 2) }}
                                    <small class="text-muted d-block">({{ $item->commission_rate }}%)</small>
                                </td>
                                <td class="fw-bold text-success">₦{{ number_format($item->seller_earnings, 2) }}</td>
                                <td>
                                    <span class="badge {{ sellerOrderStatusBadge($item->status) }}">
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

        {{-- Update status --}}
        @if(!in_array($order->status, ['completed', 'cancelled']))
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Update Order Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('seller.orders.status', $order->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-bold">New Status</label>
                            <select name="status" class="form-select" id="statusSelect" required>
                                <option value="">Select status</option>
                                @if($order->status === 'pending')
                                    <option value="confirmed">Confirmed — Accept &amp; prepare</option>
                                    <option value="cancelled">Cancelled — Reject order</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-bold">Note to Buyer (optional)</label>
                            <input type="text" name="note" class="form-control"
                                   placeholder="e.g. Your order is packed and ready for pickup...">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="feather-refresh-cw me-2"></i> Update Status
                    </button>
                </form>
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
                @empty
                <p class="text-muted fs-13">No status history yet.</p>
                @endforelse
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
                    <span class="fw-semibold">₦{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping fee</span>
                    <span class="fw-semibold">₦{{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Order Total</span>
                    <span class="fw-bold text-primary">₦{{ number_format($order->total, 2) }}</span>
                </div>

                {{-- Seller earnings breakdown --}}
                @php
                    $myCommissionTotal = $myItems->sum('commission_amount');
                    $myEarningsTotal   = $myItems->sum('seller_earnings');
                @endphp
                <div class="p-3 rounded mb-3" style="background:#f0faf5;border:1px solid #d4edda;">
                    <p class="fw-bold mb-2 text-success" style="font-size:13px;">Your Earnings Breakdown</p>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted fs-13">Your Items Total</span>
                        <span class="fw-semibold fs-13">₦{{ number_format($myItems->sum('total_price'), 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted fs-13">Platform Commission</span>
                        <span class="fw-semibold fs-13 text-danger">-₦{{ number_format($myCommissionTotal, 2) }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-13">Your Net Earnings</span>
                        <span class="fw-bold text-success">₦{{ number_format($myEarningsTotal, 2) }}</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Payment method</span>
                    <span class="fw-semibold fs-13">{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted fs-13">Payment status</span>
                    <span class="badge {{ sellerOrderStatusBadge($order->payment_status) }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->payment_reference)
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted d-block">Reference</small>
                    <code class="fs-11">{{ $order->payment_reference }}</code>
                </div>
                @endif
            </div>
        </div>

        {{-- Shipping info --}}
        {{-- Seller only sees their own items' shipment --}}
        @php
            $myShipmentId = $myItems->first()?->shipbubble_shipment_id;
            $myFirstItem  = $myItems->first();
        @endphp

        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title mb-0">Your Shipment Info</h5></div>
            <div class="card-body">

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

                @if($myFirstItem?->shipbubble_shipment_id)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Shipment ID</small>
                    <code class="fs-12">{{ $myFirstItem->shipbubble_shipment_id }}</code>
                </div>
                @endif

                @if($myFirstItem?->tracking_number)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Tracking Number</small>
                    <code class="fs-12 text-primary">{{ $myFirstItem->tracking_number }}</code>
                </div>
                @endif

                @if($myFirstItem?->estimated_delivery_date)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Estimated Delivery</small>
                    <span class="fw-semibold text-success">
                        <i class="feather-calendar me-1"></i>
                        {{ $myFirstItem->estimated_delivery_date }}
                    </span>
                </div>
                @endif

                @if($myFirstItem?->tracking_url)
                <a href="{{ $myFirstItem->tracking_url }}" target="_blank"
                   class="btn btn-outline-primary btn-sm w-100">
                    <i class="feather-map-pin me-2"></i> Track Shipment
                </a>
                @else
                <div class="text-muted fs-12 text-center py-2">
                    <i class="feather-clock me-1"></i>
                    Tracking details will appear once the shipment is booked.
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

@push('scripts')
<script>
document.getElementById('statusSelect')?.addEventListener('change', function () {
    const shippingFields = document.getElementById('shippingFields');
    if (shippingFields) {
        shippingFields.style.display = this.value === 'shipped' ? 'block' : 'none';
    }
});
</script>
@endpush

@endsection