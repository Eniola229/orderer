@extends('layouts.seller')
@section('title', 'Order #' . $order->order_number)
@section('page_title', 'Order #' . $order->order_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-8">

        {{-- My items in this order --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Order Items (Your Listings)</h5>
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
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-danger">
                                    -${{ number_format($item->commission_amount, 2) }}
                                    <small class="text-muted d-block">({{ $item->commission_rate }}%)</small>
                                </td>
                                <td class="fw-bold text-success">${{ number_format($item->seller_earnings, 2) }}</td>
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
                                @if(in_array($order->status, ['pending','confirmed']))
                                    <option value="shipped">Shipped — Mark as dispatched</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-bold">Note to Buyer (optional)</label>
                            <input type="text" name="note" class="form-control"
                                   placeholder="e.g. Your order is packed and ready for pickup...">
                        </div>
                    </div>

                    {{-- Shipping details — shown when "Shipped" selected --}}
                    <div id="shippingFields" style="display:none;">
                        <div class="alert alert-info mb-3">
                            <i class="feather-info me-2"></i>
                            Enter tracking details, or use the <strong>Shipbubble Rate Finder</strong> below to book shipment automatically.
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Carrier</label>
                                <input type="text" name="shipping_carrier" class="form-control"
                                       placeholder="e.g. DHL, GIG">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Service Name</label>
                                <input type="text" name="shipping_service_name" class="form-control"
                                       placeholder="e.g. DHL Express">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-control"
                                       placeholder="e.g. 1Z9999999...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tracking URL</label>
                                <input type="url" name="tracking_url" class="form-control"
                                       placeholder="https://track.dhl.com/...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Estimated Delivery Date</label>
                                <input type="text" name="estimated_delivery_date" class="form-control"
                                       placeholder="e.g. Jan 15 – Jan 18, 2026">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="feather-refresh-cw me-2"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        {{-- Shipbubble rate finder --}}
        @if(in_array($order->status, ['pending','confirmed']))
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="feather-truck me-2 text-primary"></i>
                    Book Shipment via Shipbubble
                </h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="fetchSellerRates()">
                    <i class="feather-zap me-1"></i> Get Rates
                </button>
            </div>
            <div class="card-body">
                <p class="text-muted fs-13 mb-3">
                    Automatically fetch shipping rates and book a shipment for this order.
                    The tracking number will be applied automatically.
                </p>

                <div id="sellerRatesLoading" style="display:none;text-align:center;padding:20px;">
                    <div class="spinner-border text-success" role="status"></div>
                    <p style="margin-top:8px;color:#888;font-size:13px;">Fetching rates...</p>
                </div>

                <div id="sellerRatesList"></div>

                <form id="bookShipmentForm"
                      action="{{ route('seller.orders.ship', $order->id) }}"
                      method="POST"
                      style="display:none;">
                    @csrf
                    <input type="hidden" name="service_code" id="shipServiceCode">
                    <input type="hidden" name="carrier"      id="shipCarrier">
                    <input type="hidden" name="service_name" id="shipServiceName">
                    <input type="hidden" name="rate_data"    id="shipRateData">
                    <button type="submit" class="btn btn-success mt-3">
                        <i class="feather-check me-2"></i> Confirm &amp; Book Shipment
                    </button>
                </form>
            </div>
        </div>
        @endif
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
                    <span class="fw-semibold">${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping fee</span>
                    <span class="fw-semibold">${{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Total</span>
                    <span class="fw-bold text-primary">${{ number_format($order->total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Payment method</span>
                    <span class="fw-semibold fs-13">{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted fs-13">Payment status</span>
                    <span class="badge orderer-badge badge-{{ $order->payment_status }}">
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

        {{-- Shipping details --}}
        <div class="card mb-3">
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

        {{-- Shipment info if available --}}
        @if($order->tracking_number)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Shipment Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted d-block">Carrier</small>
                    <strong>{{ $order->shipping_carrier }} — {{ $order->shipping_service_name }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Tracking Number</small>
                    <code class="text-primary">{{ $order->tracking_number }}</code>
                </div>
                @if($order->tracking_url)
                <a href="{{ $order->tracking_url }}" target="_blank"
                   class="btn btn-sm btn-outline-primary w-100 mt-2">
                    <i class="feather-external-link me-1"></i> Track Shipment
                </a>
                @endif
                @if($order->estimated_delivery_date)
                <div class="mt-2">
                    <small class="text-muted d-block">Est. Delivery</small>
                    <strong>{{ $order->estimated_delivery_date }}</strong>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
// Show/hide shipping fields
document.getElementById('statusSelect')?.addEventListener('change', function() {
    document.getElementById('shippingFields').style.display =
        this.value === 'shipped' ? 'block' : 'none';
});

// Fetch Shipbubble rates for seller
function fetchSellerRates() {
    document.getElementById('sellerRatesLoading').style.display = 'block';
    document.getElementById('sellerRatesList').innerHTML = '';
    document.getElementById('bookShipmentForm').style.display = 'none';

    fetch('{{ route("seller.orders.rates", $order->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('sellerRatesLoading').style.display = 'none';

        if (!data.success || !data.rates || !data.rates.length) {
            document.getElementById('sellerRatesList').innerHTML =
                '<div class="alert alert-warning">No rates available. Enter tracking details manually above.</div>';
            return;
        }

        let html = '';
        data.rates.forEach(function(rate, idx) {
            const courier     = rate.courier?.name || 'Courier';
            const service     = rate.service?.name || 'Standard';
            const price       = parseFloat(rate.total || 0).toFixed(2);
            const eta         = rate.delivery_eta || '';
            const serviceCode = rate.service_code || '';

            html += `
            <label class="d-block border rounded p-3 mb-2"
                   style="cursor:pointer;transition:border-color .2s;"
                   for="srate_${idx}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <input type="radio" name="_srate" id="srate_${idx}"
                               value="${serviceCode}"
                               data-carrier="${courier}"
                               data-service="${service}"
                               data-ratedata='${JSON.stringify(rate)}'
                               onchange="selectSellerRate(this)"
                               class="form-check-input mt-0"
                               ${idx === 0 ? 'checked' : ''}>
                        <div>
                            <p class="mb-0 fw-bold fs-13">${courier}</p>
                            <p class="mb-0 text-muted" style="font-size:12px;">${service}</p>
                            ${eta ? `<small class="text-muted"><i class="feather-clock me-1"></i>${eta}</small>` : ''}
                        </div>
                    </div>
                    <span class="fw-bold text-success">$${price}</span>
                </div>
            </label>`;
        });

        document.getElementById('sellerRatesList').innerHTML = html;
        document.getElementById('bookShipmentForm').style.display = 'block';

        const first = document.querySelector('[name="_srate"]');
        if (first) selectSellerRate(first);
    })
    .catch(() => {
        document.getElementById('sellerRatesLoading').style.display = 'none';
        document.getElementById('sellerRatesList').innerHTML =
            '<div class="alert alert-danger">Failed to fetch rates. Try again or enter tracking manually.</div>';
    });
}

function selectSellerRate(radio) {
    document.getElementById('shipServiceCode').value = radio.value;
    document.getElementById('shipCarrier').value     = radio.dataset.carrier;
    document.getElementById('shipServiceName').value = radio.dataset.service;
    document.getElementById('shipRateData').value    = radio.dataset.ratedata;

    document.querySelectorAll('label[for^="srate_"]').forEach(l => {
        l.style.borderColor = '#dee2e6';
        l.style.background  = '#fff';
    });
    radio.closest('label').style.borderColor = '#2ECC71';
    radio.closest('label').style.background  = '#f0faf5';
}
</script>
@endpush

@endsection