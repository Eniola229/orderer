@extends('layouts.admin')
@section('title', 'Booking ' . $deliveryBooking->booking_number)
@section('page_title', 'Delivery Booking Detail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.delivery-bookings.index') }}">Delivery Bookings</a></li>
    <li class="breadcrumb-item active">{{ $deliveryBooking->booking_number }}</li>
@endsection

@section('content')

@php
    $booking     = $deliveryBooking;
    $isCancelled = $booking->status === 'cancelled';
    $isDelivered = $booking->status === 'delivered';
    $stages      = ['pending','confirmed','picked_up','in_transit','delivered'];
    $currentIdx  = array_search($booking->status, $stages);
    $currentIdx  = $currentIdx !== false ? $currentIdx : 0;
@endphp

<div class="row">
    {{-- Left column --}}
    <div class="col-lg-8">

        {{-- Booking summary --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">{{ $booking->booking_number }}</h5>
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
                    <span class="badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:6px 14px;border-radius:8px;font-size:13px;font-weight:700;">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">From</small>
                        <strong>{{ $booking->pickup_address }}</strong>
                        <small class="text-muted d-block">{{ $booking->pickup_city }}, {{ $booking->pickup_country }}</small>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">To</small>
                        <strong>{{ $booking->delivery_address }}</strong>
                        <small class="text-muted d-block">{{ $booking->delivery_city }}, {{ $booking->delivery_country }}</small>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Carrier</small>
                        <strong>{{ $booking->carrier ?? '—' }}</strong>
                        @if($booking->service_name)
                            <small class="text-muted d-block">{{ $booking->service_name }}</small>
                        @endif
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Tracking #</small>
                        @if($booking->tracking_number)
                            <strong class="text-success">{{ $booking->tracking_number }}</strong>
                        @else
                            <span class="text-muted">Pending assignment</span>
                        @endif
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Item Description</small>
                        <strong>{{ $booking->item_description ?? '—' }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Weight</small>
                        <strong>{{ $booking->weight_kg }} kg</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Delivery Type</small>
                        <span class="badge bg-secondary">{{ ucfirst($booking->delivery_type) }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Declared Value</small>
                        <strong>₦{{ number_format($booking->declared_value, 2) }}</strong>
                    </div>
                    @if($booking->estimated_delivery_date)
                    <div class="col-6">
                        <small class="text-muted d-block mb-1">Est. Delivery</small>
                        <strong>{{ $booking->estimated_delivery_date }}</strong>
                    </div>
                    @endif
                    @if($booking->tracking_url && !$isCancelled)
                    <div class="col-12">
                        <a href="{{ $booking->tracking_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="feather-external-link me-1"></i> Track on {{ $booking->carrier }} Website
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-4">
                    Delivery Progress
                    @if($isCancelled)
                        <span class="text-danger ms-2" style="font-size:13px;font-weight:600;">— Cancelled</span>
                    @endif
                </h6>

                @if($isCancelled)
                    <div class="text-center py-3" style="color:#C0392B;">
                        <i class="feather-x-circle" style="font-size:48px;display:block;margin-bottom:12px;"></i>
                        <p class="mb-0 fw-semibold">This shipment has been cancelled.</p>
                    </div>
                @else
                    <div style="display:flex;align-items:center;justify-content:space-between;position:relative;">
                        <div style="position:absolute;top:14px;left:0;right:0;height:3px;background:#e9ecef;z-index:0;"></div>
                        <div style="position:absolute;top:14px;left:0;height:3px;background:#28a745;z-index:1;
                                    width:{{ ($currentIdx / (count($stages) - 1)) * 100 }}%;transition:width .5s;"></div>
                        @foreach($stages as $i => $stage)
                        <div style="text-align:center;position:relative;z-index:2;">
                            <div style="width:30px;height:30px;border-radius:50%;
                                        background:{{ $i <= $currentIdx ? '#28a745' : '#e9ecef' }};
                                        color:{{ $i <= $currentIdx ? '#fff' : '#aaa' }};
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:12px;font-weight:700;margin:0 auto 6px;">
                                {{ $i < $currentIdx ? '✓' : ($i + 1) }}
                            </div>
                            <small style="font-size:10px;color:{{ $i <= $currentIdx ? '#28a745' : '#aaa' }};font-weight:600;text-transform:uppercase;">
                                {{ ucfirst(str_replace('_', ' ', $stage)) }}
                            </small>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Tracking Events --}}
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-4">Tracking Events</h6>
                @if(count($trackingEvents))
                    @foreach($trackingEvents as $event)
                    <div style="display:flex;gap:14px;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #f5f5f5;">
                        <div style="width:10px;height:10px;border-radius:50%;background:#28a745;margin-top:5px;flex-shrink:0;"></div>
                        <div>
                            <p class="mb-0 fw-semibold" style="font-size:14px;">
                                {{ $event['status'] ?? $event['description'] ?? 'Update' }}
                            </p>
                            @if(!empty($event['description']) && isset($event['status']))
                                <p class="mb-0 text-muted" style="font-size:13px;">{{ $event['description'] }}</p>
                            @endif
                            @if(!empty($event['location']))
                                <small class="text-muted">
                                    <i class="feather-map-pin me-1"></i>{{ $event['location'] }}
                                </small>
                            @endif
                            @if(!empty($event['event_at']))
                                <small class="text-muted d-block">
                                    {{ \Carbon\Carbon::parse($event['event_at'])->format('M d, Y H:i') }}
                                </small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="feather-map" style="font-size:32px;display:block;margin-bottom:10px;color:#ddd;"></i>
                        <p>No tracking events recorded yet.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Right column --}}
    <div class="col-lg-4">

        {{-- Customer --}}
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Customer</h6>
                @if($booking->user)
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:44px;height:44px;border-radius:50%;background:#e9ecef;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:#495057;">
                        {{ strtoupper(substr($booking->user->full_name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold">{{ $booking->user->full_name ?? '—' }}</p>
                        <small class="text-muted">{{ $booking->user->email ?? '—' }}</small>
                    </div>
                </div>
                <a href="{{ route('admin.buyers.show', $booking->user_id) }}" class="btn btn-sm btn-outline-primary w-100">
                    View Customer Profile
                </a>
                @else
                    <p class="text-muted mb-0">Customer not found.</p>
                @endif
            </div>
        </div>

        {{-- Pricing Breakdown --}}
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Pricing Breakdown</h6>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Shipping Fee</span>
                    <strong>₦{{ number_format($booking->fee, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom align-items-center">
                    <span class="text-muted d-flex align-items-center gap-1">
                        Service Fee
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:50%;background:#6c757d;color:#fff;font-size:9px;font-weight:700;cursor:default;"
                              title="₦200 platform processing fee charged per booking">?</span>
                    </span>
                    <strong class="text-info">₦{{ number_format($booking->service_fee, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="fw-bold">Total Charged</span>
                    <strong class="text-success fs-16">₦{{ number_format($booking->fee + $booking->service_fee, 2) }}</strong>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Payment Status</span>
                    @php
                        $pc = match($booking->payment_status) {
                            'paid'     => 'success',
                            'pending'  => 'warning',
                            'failed'   => 'danger',
                            default    => 'secondary',
                        };
                    @endphp
                    <span class="badge bg-{{ $pc }}">{{ ucfirst($booking->payment_status) }}</span>
                </div>
                @if($booking->payment_reference)
                <div class="mt-2">
                    <small class="text-muted d-block">Payment Reference</small>
                    <code style="font-size:12px;">{{ $booking->payment_reference }}</code>
                </div>
                @endif
            </div>
        </div>

        {{-- Update Status --}}
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Update Status</h6>
                <form action="{{ route('admin.delivery-bookings.update-status', $booking->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <select name="status" class="form-select form-select-sm">
                            @foreach(['pending','confirmed','picked_up','in_transit','delivered','cancelled'] as $s)
                                <option value="{{ $s }}" {{ $booking->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success w-100">
                        <i class="feather-check me-1"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        {{-- Meta --}}
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Booking Meta</h6>
                <small class="text-muted d-block mb-1">Booking Number</small>
                <code class="d-block mb-3">{{ $booking->booking_number }}</code>

                @if($booking->shipbubble_shipment_id)
                <small class="text-muted d-block mb-1">Shipbubble Shipment ID</small>
                <code class="d-block mb-3">{{ $booking->shipbubble_shipment_id }}</code>
                @endif

                @if($booking->courier_id)
                <small class="text-muted d-block mb-1">Courier ID</small>
                <code class="d-block mb-3">{{ $booking->courier_id }}</code>
                @endif

                <small class="text-muted d-block mb-1">Created</small>
                <p class="mb-0 fs-13">{{ $booking->created_at->format('M d, Y \a\t H:i') }}</p>
            </div>
        </div>

    </div>
</div>

@endsection