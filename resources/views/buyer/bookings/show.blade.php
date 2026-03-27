@extends('layouts.buyer')
@section('title', 'Booking ' . $booking->booking_number)
@section('page_title', 'Booking ' . $booking->booking_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('buyer.bookings') }}">Bookings</a></li>
    <li class="breadcrumb-item active">{{ $booking->booking_number }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Booking Details</h5>
                <span class="badge orderer-badge badge-{{ $booking->status }}">
                    {{ ucfirst(str_replace('_',' ',$booking->status)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <small class="text-muted d-block">From</small>
                        <strong>{{ $booking->pickup_city }}, {{ $booking->pickup_country }}</strong>
                        <p class="text-muted fs-13 mb-0">{{ $booking->pickup_address }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">To</small>
                        <strong>{{ $booking->delivery_city }}, {{ $booking->delivery_country }}</strong>
                        <p class="text-muted fs-13 mb-0">{{ $booking->delivery_address }}</p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Carrier</small>
                        <strong>{{ $booking->carrier ?? 'TBD' }}</strong>
                        @if($booking->service_name)
                        <small class="text-muted d-block">{{ $booking->service_name }}</small>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Weight</small>
                        <strong>{{ $booking->weight_kg }} kg</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Fee Paid</small>
                        <strong class="text-success">${{ number_format($booking->fee, 2) }}</strong>
                    </div>
                    @if($booking->tracking_number)
                    <div class="col-12">
                        <small class="text-muted d-block">Tracking Number</small>
                        <strong class="text-primary" style="font-size:18px;">
                            {{ $booking->tracking_number }}
                        </strong>
                        @if($booking->tracking_url)
                        <a href="{{ $booking->tracking_url }}" target="_blank"
                           class="btn btn-sm btn-outline-primary ms-2">
                            <i class="feather-external-link me-1"></i> Track on carrier site
                        </a>
                        @endif
                    </div>
                    @endif
                    @if($booking->estimated_delivery_date)
                    <div class="col-12">
                        <small class="text-muted d-block">Estimated Delivery</small>
                        <strong>{{ $booking->estimated_delivery_date }}</strong>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('buyer.bookings.track', $booking->id) }}"
                   class="btn btn-primary">
                    <i class="feather-map-pin me-2"></i> Live Tracking
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Summary</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Booking #</span>
                    <strong>{{ $booking->booking_number }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Type</span>
                    <strong>{{ ucfirst($booking->delivery_type) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Item</span>
                    <strong class="text-end" style="max-width:60%;">
                        {{ Str::limit($booking->item_description, 30) }}
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment</span>
                    <span class="badge orderer-badge badge-{{ $booking->payment_status }}">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Date</span>
                    <strong>{{ $booking->created_at->format('M d, Y') }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total Paid</span>
                    <span class="fw-bold text-success" style="font-size:18px;">
                        ${{ number_format($booking->fee, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection