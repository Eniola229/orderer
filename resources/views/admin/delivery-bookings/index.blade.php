@extends('layouts.admin')
@section('title', 'Delivery Bookings')
@section('page_title', 'Delivery Bookings')
@section('breadcrumb')
    <li class="breadcrumb-item active">Delivery Bookings</li>
@endsection

@section('content')

<style>
    .filter-card { background: #f8f9fa; border-radius: 8px; margin-bottom: 20px; }
    .tooltip-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px; height: 16px;
        border-radius: 50%;
        background: #6c757d;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
        margin-left: 4px;
        vertical-align: middle;
        position: relative;
    }
    .tooltip-icon:hover::after {
        content: attr(data-tip);
        position: absolute;
        bottom: 120%;
        left: 50%;
        transform: translateX(-50%);
        background: #212529;
        color: #fff;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 400;
        white-space: nowrap;
        z-index: 999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        pointer-events: none;
    }
</style>

{{-- Stats Cards --}}
<div class="row mb-4">
    <div class="col-md-2 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Bookings</p>
                <h2 class="fw-bold mb-0 text-primary">{{ number_format($stats['total']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Pending</p>
                <h2 class="fw-bold mb-0 text-warning">{{ number_format($stats['pending']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Delivered</p>
                <h2 class="fw-bold mb-0 text-success">{{ number_format($stats['delivered']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Shipping Revenue</p>
                <h2 class="fw-bold mb-0 text-success">₦{{ number_format($stats['revenue'], 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">
                    Service Fees Earned
                    <span class="tooltip-icon"
                          data-tip="₦200 platform service fee collected per booking">?</span>
                </p>
                <h2 class="fw-bold mb-0 text-info">₦{{ number_format($stats['service_fees'], 2) }}</h2>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card filter-card">
    <div class="card-body">
        <form action="{{ route('admin.delivery-bookings.index') }}" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    @foreach(['pending','confirmed','picked_up','in_transit','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_',' ',$s)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Payment</label>
                <select name="payment_status" class="form-select form-select-sm">
                    <option value="">All Payment</option>
                    @foreach(['pending','paid','failed','refunded'] as $p)
                        <option value="{{ $p }}" {{ request('payment_status') == $p ? 'selected' : '' }}>
                            {{ ucfirst($p) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Type</label>
                <select name="delivery_type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="local"         {{ request('delivery_type') == 'local'         ? 'selected' : '' }}>Local</option>
                    <option value="international" {{ request('delivery_type') == 'international' ? 'selected' : '' }}>International</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label fw-semibold fs-12">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Booking # or email" value="{{ request('search') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <a href="{{ route('admin.delivery-bookings.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="feather-x"></i>
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="feather-filter"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($bookings->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Booking #</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Customer</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Route</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Carrier</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Shipping Fee</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">
                            Service Fee
                            <span class="tooltip-icon"
                                  data-tip="₦200 platform fee per booking">?</span>
                        </th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Total</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Payment</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td><code class="fw-bold">{{ $booking->booking_number }}</code></td>
                        <td>
                            <p class="mb-0 fw-semibold fs-13">{{ $booking->user->full_name ?? '—' }}</p>
                            <small class="text-muted">{{ $booking->user->email ?? '—' }}</small>
                        </td>
                        <td>
                            <small class="text-muted d-block">
                                📦 {{ $booking->pickup_city }}, {{ $booking->pickup_country }}
                            </small>
                            <small class="text-muted d-block">
                                📍 {{ $booking->delivery_city }}, {{ $booking->delivery_country }}
                            </small>
                        </td>
                        <td>
                            <span class="fw-semibold fs-13">{{ $booking->carrier ?? '—' }}</span>
                            @if($booking->service_name)
                                <small class="text-muted d-block">{{ $booking->service_name }}</small>
                            @endif
                        </td>
                        <td class="fw-semibold">₦{{ number_format($booking->fee, 2) }}</td>
                        <td>
                            <span class="text-info fw-semibold">₦{{ number_format($booking->service_fee, 2) }}</span>
                        </td>
                        <td class="fw-bold text-success">
                            ₦{{ number_format($booking->fee + $booking->service_fee, 2) }}
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending'    => ['bg' => '#ffc107', 'color' => '#212529'],
                                    'confirmed'  => ['bg' => '#17a2b8', 'color' => '#fff'],
                                    'picked_up'  => ['bg' => '#6610f2', 'color' => '#fff'],
                                    'in_transit' => ['bg' => '#fd7e14', 'color' => '#fff'],
                                    'delivered'  => ['bg' => '#28a745', 'color' => '#fff'],
                                    'cancelled'  => ['bg' => '#dc3545', 'color' => '#fff'],
                                ];
                                $sc = $statusColors[$booking->status] ?? ['bg' => '#6c757d', 'color' => '#fff'];
                            @endphp
                            <span class="badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:5px 10px;border-radius:4px;font-size:11px;font-weight:600;">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $pc = match($booking->payment_status) {
                                    'paid'     => ['bg' => '#28a745', 'color' => '#fff'],
                                    'pending'  => ['bg' => '#ffc107', 'color' => '#212529'],
                                    'failed'   => ['bg' => '#dc3545', 'color' => '#fff'],
                                    'refunded' => ['bg' => '#6c757d', 'color' => '#fff'],
                                    default    => ['bg' => '#6c757d', 'color' => '#fff'],
                                };
                            @endphp
                            <span class="badge" style="background:{{ $pc['bg'] }};color:{{ $pc['color'] }};padding:5px 10px;border-radius:4px;font-size:11px;font-weight:600;">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>
                        <td class="text-muted fs-12">{{ $booking->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.delivery-bookings.show', $booking->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="feather-eye"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-success"
                                        onclick="openStatusModal('{{ $booking->id }}', '{{ $booking->booking_number }}', '{{ $booking->status }}')">
                                    <i class="feather-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $bookings->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-package mb-2 d-block" style="font-size:40px;"></i>
            <p>No delivery bookings found.</p>
        </div>
        @endif
    </div>
</div>

{{-- Status Update Modal --}}
<div id="statusModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;max-width:480px;width:90%;margin:auto;box-shadow:0 10px 40px rgba(0,0,0,0.2);">
        <div style="padding:20px;border-bottom:1px solid #e5e7eb;">
            <h5 style="margin:0;font-size:18px;font-weight:700;">Update Booking Status</h5>
        </div>
        <form id="statusForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding:20px;">
                <p id="modalBookingInfo" style="color:#6b7280;font-size:14px;margin-bottom:20px;"></p>
                <div style="margin-bottom:16px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;font-size:14px;">New Status</label>
                    <select name="status" id="modalStatus" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="picked_up">Picked Up</option>
                        <option value="in_transit">In Transit</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div style="padding:20px;border-top:1px solid #e5e7eb;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeStatusModal()" style="padding:8px 20px;background:#f3f4f6;border:none;border-radius:6px;cursor:pointer;font-size:14px;">Cancel</button>
                <button type="submit" style="padding:8px 20px;background:#28a745;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px;">Update Status</button>
            </div>
        </form>
    </div>
</div>

<script>
function openStatusModal(id, number, currentStatus) {
    const modal   = document.getElementById('statusModal');
    const form    = document.getElementById('statusForm');
    const info    = document.getElementById('modalBookingInfo');
    const select  = document.getElementById('modalStatus');

    form.action  = `/admin/delivery-bookings/${id}/status`;
    info.innerHTML = `<strong>Booking #${number}</strong> — update shipment status below.`;
    select.value = currentStatus;

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeStatusModal();
});
</script>

@endsection