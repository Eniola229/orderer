@extends('layouts.admin')
@section('title', 'Order #' . $order->order_number)
@section('page_title', 'Order #' . $order->order_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active">#{{ $order->order_number }}</li>
@endsection

@section('content')

@php
function adminOrderStatusBadge(string $status): string {
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
        'held'       => 'bg-warning text-dark',
        'released'   => 'bg-success text-white',
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
                <span class="badge {{ adminOrderStatusBadge($order->status) }}">
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
                                        <span class="fw-semibold fs-13">{{ Str::limit($item->item_name, 30) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.sellers.show', $item->seller_id) }}"
                                       class="fs-13 text-primary">
                                        {{ $item->seller->business_name ?? '—' }}
                                    </a>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td class="fw-bold">₦{{ number_format($item->total_price, 2) }}</td>
                                <td class="text-danger">
                                    ₦{{ number_format($item->commission_amount, 2) }}
                                    <small class="text-muted">({{ $item->commission_rate }}%)</small>
                                </td>
                                <td class="text-success fw-bold">
                                    ₦{{ number_format($item->seller_earnings, 2) }}
                                </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ adminOrderStatusBadge($item->status) }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                            @if(auth('admin')->user()->canEditOrders() && !in_array($item->status, ['cancelled','delivered','completed']))
                                            <form action="{{ route('admin.orders.items.cancel', [$order->id, $item->id]) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Cancel \'{{ addslashes($item->item_name) }}\' and refund ₦{{ number_format($item->total_price, 2) }} to buyer?')">
                                                @csrf @method('PUT')
                                                <button type="submit"
                                                        class="btn btn-outline-danger btn-sm"
                                                        title="Cancel this item and refund buyer">
                                                    <i class="feather-x" style="font-size:11px;"></i> Cancel Item
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                             </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Escrow Breakdown --}}
        @php
            $escrows = $order->escrowHolds->sortBy('created_at');
        @endphp
        @if($escrows->isNotEmpty())
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="feather-shield me-2"></i> Escrow Breakdown
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border">
                        {{ $escrows->count() }} row(s)
                    </span>
                    <span class="badge bg-warning text-dark">
                        Held: ₦{{ number_format($escrows->where('status','held')->sum('amount'), 2) }}
                    </span>
                    <span class="badge bg-success text-white">
                        Released: ₦{{ number_format($escrows->where('status','released')->sum('amount'), 2) }}
                    </span>
                    <span class="badge bg-secondary text-white">
                        Refunded: ₦{{ number_format($escrows->where('status','refunded')->sum('amount'), 2) }}
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Order Item</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Item Amount</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Commission</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Seller Gets</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Escrow Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Released At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($escrows as $escrow)
                            <tr>
                                <td>
                                    @if($escrow->orderItem)
                                    <div class="d-flex align-items-center gap-2">
                                        @if($escrow->orderItem->item_image)
                                        <img src="{{ $escrow->orderItem->item_image }}"
                                             style="width:34px;height:34px;object-fit:cover;border-radius:6px;" alt="">
                                        @endif
                                        <div>
                                            <p class="mb-0 fw-semibold fs-13">
                                                {{ Str::limit($escrow->orderItem->item_name, 35) }}
                                            </p>
                                            <small class="text-muted">
                                                qty {{ $escrow->orderItem->quantity }}
                                                · status:
                                                <span class="badge {{ adminOrderStatusBadge($escrow->orderItem->status) }}" style="font-size:10px;">
                                                    {{ ucfirst($escrow->orderItem->status) }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-muted fs-13">Item not found</span>
                                    @endif
                                </td>
                                <td>
                                    @if($escrow->seller)
                                    <a href="{{ route('admin.sellers.show', $escrow->seller_id) }}"
                                       class="fs-13 text-primary">
                                        {{ $escrow->seller->business_name ?? '—' }}
                                    </a>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-bold fs-13">
                                    ₦{{ number_format($escrow->amount, 2) }}
                                </td>
                                <td class="text-danger fs-13">
                                    ₦{{ number_format($escrow->commission_amount, 2) }}
                                </td>
                                <td class="text-success fw-bold fs-13">
                                    ₦{{ number_format($escrow->seller_amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge {{ match($escrow->status) {
                                        'held'     => 'bg-warning text-dark',
                                        'released' => 'bg-success text-white',
                                        'refunded' => 'bg-secondary text-white',
                                        default    => 'bg-light text-dark',
                                    } }}">
                                        {{ ucfirst($escrow->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">
                                    {{ $escrow->released_at?->format('M d, Y H:i') ?? '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="fw-bold fs-13 text-end">Totals</td>
                                <td class="fw-bold fs-13">₦{{ number_format($escrows->sum('amount'), 2) }}</td>
                                <td class="fw-bold text-danger fs-13">₦{{ number_format($escrows->sum('commission_amount'), 2) }}</td>
                                <td class="fw-bold text-success fs-13">₦{{ number_format($escrows->sum('seller_amount'), 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif


        {{-- Admin actions --}}
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
                    <span class="fw-semibold">₦{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="fw-semibold">₦{{ number_format($order->shipping_fee, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Commission</span>
                    <span class="fw-semibold text-primary">₦{{ number_format($order->commission_total ?? 0, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Total</span>
                    <span class="fw-bold text-primary">₦{{ number_format($order->total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Payment</span>
                    <span class="fw-semibold fs-13">{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Payment Status</span>
                    <span class="badge {{ adminOrderStatusBadge($order->payment_status) }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->payment_reference)
                <div class="mt-2 p-2 bg-light rounded">
                    <small class="text-muted d-block">Reference</small>
                    <code class="fs-12">{{ $order->payment_reference }}</code>
                </div>
                @endif
                @php
                    $heldTotal     = $escrows->where('status', 'held')->sum('amount');
                    $releasedTotal = $escrows->where('status', 'released')->sum('amount');
                    $refundedTotal = $escrows->where('status', 'refunded')->sum('amount');
                @endphp
                @if($escrows->isNotEmpty())
                <div class="mt-2 p-2 rounded" style="background:#FEF9E7;">
                    <small class="text-muted d-block mb-1">Escrow</small>
                    @if($heldTotal > 0)
                    <div class="d-flex justify-content-between fs-12">
                        <span class="text-muted">Held</span>
                        <span class="badge bg-warning text-dark">₦{{ number_format($heldTotal, 2) }}</span>
                    </div>
                    @endif
                    @if($releasedTotal > 0)
                    <div class="d-flex justify-content-between fs-12 mt-1">
                        <span class="text-muted">Released</span>
                        <span class="badge bg-success text-white">₦{{ number_format($releasedTotal, 2) }}</span>
                    </div>
                    @endif
                    @if($refundedTotal > 0)
                    <div class="d-flex justify-content-between fs-12 mt-1">
                        <span class="text-muted">Refunded</span>
                        <span class="badge bg-secondary text-white">₦{{ number_format($refundedTotal, 2) }}</span>
                    </div>
                    @endif
                </div>
                @endif
                @if($order->is_multi_seller)
                <div class="mt-2 p-2 rounded" style="background:#E8F4FD;">
                    <small class="text-muted d-block">Order Type</small>
                    <span class="badge bg-info text-white">
                        <i class="feather-layers me-1"></i> Multi-Seller Order
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

        
        {{-- Shipping info — shows multiple or single --}}
        {{-- Admin sees all shipments grouped --}}
        @php
            $itemsByShipment = $order->items->groupBy('shipbubble_shipment_id');
        @endphp

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="feather-truck me-2"></i>
                    Shipment Details
                    @if($order->is_multi_seller)
                    <span class="badge bg-info text-white ms-2">{{ $itemsByShipment->count() }} Shipments</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @forelse($itemsByShipment as $shipmentId => $shipmentItems)
                @php $fi = $shipmentItems->first(); @endphp
                <div class="mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">
                            <i class="feather-box me-1"></i>
                            Shipment #{{ $loop->iteration }}
                            @if($order->is_multi_seller)
                            <small class="text-muted">({{ $fi->seller->business_name ?? 'Seller' }})</small>
                            @endif
                        </h6>
                        <span class="badge {{ match($fi->shipping_status ?? 'pending') {
                            'delivered' => 'bg-success',
                            'shipped'   => 'bg-primary',
                            'confirmed' => 'bg-info',
                            'cancelled' => 'bg-danger',
                            default     => 'bg-warning text-dark'
                        } }}">{{ ucfirst($fi->shipping_status ?? 'pending') }}</span>
                    </div>

                    <div class="ms-3">
                        {{-- Items --}}
                        <div class="mb-2">
                            <small class="text-muted d-block">Items</small>
                            @foreach($shipmentItems as $si)
                            <div class="fs-13">
                                {{ $si->item_name }} ×{{ $si->quantity }}
                                <span class="badge ms-1 {{ match($si->status) {
                                    'delivered' => 'bg-success',
                                    'shipped'   => 'bg-primary',
                                    default     => 'bg-warning text-dark'
                                } }}">{{ ucfirst($si->status) }}</span>
                            </div>
                            @endforeach
                        </div>

                        @if($fi->shipbubble_shipment_id)
                        <div class="mb-2">
                            <small class="text-muted d-block">Shipment ID</small>
                            <code class="fs-12">{{ $fi->shipbubble_shipment_id }}</code>
                        </div>
                        @endif

                        @if($fi->courier_id)
                        <div class="mb-2">
                            <small class="text-muted d-block">Courier</small>
                            <span class="fw-semibold">{{ $fi->courier_id }}</span>
                        </div>
                        @endif

                        @if($fi->tracking_number)
                        <div class="mb-2">
                            <small class="text-muted d-block">Tracking Number</small>
                            <code class="fs-12 text-primary">{{ $fi->tracking_number }}</code>
                        </div>
                        @endif

                        @if($fi->estimated_delivery_date)
                        <div class="mb-2">
                            <small class="text-muted d-block">Estimated Delivery</small>
                            <span class="fw-semibold text-success">
                                <i class="feather-calendar me-1"></i>
                                {{ $fi->estimated_delivery_date }}
                            </span>
                        </div>
                        @endif

                        @if($fi->delivered_at)
                        <div class="mb-2">
                            <small class="text-muted d-block">Delivered At</small>
                            <span class="text-success">
                                <i class="feather-check-circle me-1"></i>
                                {{ $fi->delivered_at->format('M d, Y H:i') }}
                            </span>
                        </div>
                        @endif

                        @if($fi->tracking_url)
                        <a href="{{ $fi->tracking_url }}" target="_blank"
                           class="btn btn-outline-primary btn-sm w-100 mt-1">
                            <i class="feather-map-pin me-2"></i> Track Shipment #{{ $loop->iteration }}
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-muted fs-13 text-center py-3">
                    No shipments booked yet.
                </div>
                @endforelse
                    </div>
                </div>

        {{-- Delivery address --}}
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
    </div>
</div>

@endsection