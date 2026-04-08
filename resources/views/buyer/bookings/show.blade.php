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
                <span class="badge" style="
                    @php
                        $status = $booking->status ?? 'unknown';
                        
                        $statusStyles = [
                            'pending' => [
                                'background' => '#ffc107',
                                'color' => '#212529'
                            ],
                            'confirmed' => [
                                'background' => '#17a2b8',
                                'color' => '#ffffff'
                            ],
                            'picked_up' => [
                                'background' => '#6f42c1',
                                'color' => '#ffffff'
                            ],
                            'in_transit' => [
                                'background' => '#007bff',
                                'color' => '#ffffff'
                            ],
                            'delivered' => [
                                'background' => '#28a745',
                                'color' => '#ffffff'
                            ],
                            'cancelled' => [
                                'background' => '#dc3545',
                                'color' => '#ffffff'
                            ],
                            'failed' => [
                                'background' => '#dc3545',
                                'color' => '#ffffff'
                            ],
                            'processing' => [
                                'background' => '#007bff',
                                'color' => '#ffffff'
                            ],
                            'completed' => [
                                'background' => '#28a745',
                                'color' => '#ffffff'
                            ],
                            'refunded' => [
                                'background' => '#6c757d',
                                'color' => '#ffffff'
                            ],
                            'disputed' => [
                                'background' => '#fd7e14',
                                'color' => '#ffffff'
                            ],
                            'approved' => [
                                'background' => '#28a745',
                                'color' => '#ffffff'
                            ],
                            'rejected' => [
                                'background' => '#dc3545',
                                'color' => '#ffffff'
                            ]
                        ];
                        
                        $style = $statusStyles[$status] ?? [
                            'background' => '#6c757d',
                            'color' => '#ffffff'
                        ];
                    @endphp
                    background-color: {{ $style['background'] }};
                    color: {{ $style['color'] }};
                    padding: 5px 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: 600;
                    display: inline-block;
                ">
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
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
                        <small class="text-muted d-block">Shipping Fee</small>
                        <strong class="text-success">₦{{ number_format($booking->fee, 2) }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block" style="display:flex;align-items:center;gap:4px;">
                            Service Fee
                            <span title="A one-time platform fee that covers order processing, support, and secure payment handling."
                                  style="display:inline-flex;align-items:center;justify-content:center;
                                         width:14px;height:14px;border-radius:50%;background:#6c757d;
                                         color:#fff;font-size:9px;font-weight:700;cursor:default;">?</span>
                        </small>
                        <strong class="text-muted">₦{{ number_format($booking->service_fee, 2) }}</strong>
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
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping Fee</span>
                    <span>₦{{ number_format($booking->fee, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 align-items-center">
                    <span class="text-muted d-flex align-items-center gap-1">
                        Service Fee
                        <span title="A one-time platform fee that covers order processing, support, and secure payment handling."
                              style="display:inline-flex;align-items:center;justify-content:center;
                                     width:14px;height:14px;border-radius:50%;background:#6c757d;
                                     color:#fff;font-size:9px;font-weight:700;cursor:default;">?</span>
                    </span>
                    <span>₦{{ number_format($booking->service_fee, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total Paid</span>
                    <span class="fw-bold text-success" style="font-size:18px;">
                        ₦{{ number_format($booking->fee + $booking->service_fee, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection