@extends('layouts.buyer')
@section('title', 'My Bookings')
@section('page_title', 'My Delivery Bookings')
@section('breadcrumb')
    <li class="breadcrumb-item active">Bookings</li>
@endsection
@section('page_actions')
    <a href="{{ route('rider.booking') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> New Booking
    </a>
@endsection

@section('content')
@push('styles')
<style>
    .orderer-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .badge-pending      { background: #FEF9E7; color: #B7950B; }
    .badge-confirmed    { background: #EBF5FB; color: #1A5276; }
    .badge-picked_up    { background: #EAF4FB; color: #1F618D; }
    .badge-in_transit   { background: #E8F8F5; color: #1E8449; }
    .badge-delivered    { background: #D5F5E3; color: #1E8449; }
    .badge-cancelled    { background: #FADBD8; color: #C0392B; }
</style>
@endpush
<div class="card">
    <div class="card-body p-0">
        @if($bookings->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Booking #</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">From → To</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Carrier</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Fee</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Tracking #</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td class="fw-semibold text-primary"><a href="{{ route('buyer.bookings.track', $booking->id) }}">{{ $booking->booking_number }}</td></td>
                        <td class="fs-13">
                            {{ $booking->pickup_city }}, {{ $booking->pickup_country }}
                            <i class="feather-arrow-right mx-1" style="font-size:12px;"></i>
                            {{ $booking->delivery_city }}, {{ $booking->delivery_country }}
                        </td>
                        <td class="fs-13">{{ $booking->carrier ?? '—' }}</td>
                        <td class="fw-bold text-success">₦{{ number_format($booking->fee, 2) }}</td>
                        <td>
                            @if($booking->tracking_number)
                                <code class="fs-12">{{ $booking->tracking_number }}</code>
                            @else
                                <span class="text-muted fs-12">Pending</span>
                            @endif
                        </td>
                        <td>
                            @php
                                // Define status configurations
                                $statusConfig = [
                                    'pending' => [
                                        'icon' => 'feather-clock',
                                        'color' => '#ffc107',
                                        'text_color' => '#212529',
                                        'label' => 'Pending'
                                    ],
                                    'confirmed' => [
                                        'icon' => 'feather-check-circle',
                                        'color' => '#17a2b8',
                                        'text_color' => '#ffffff',
                                        'label' => 'Confirmed'
                                    ],
                                    'picked_up' => [
                                        'icon' => 'feather-package',
                                        'color' => '#6f42c1',
                                        'text_color' => '#ffffff',
                                        'label' => 'Picked Up'
                                    ],
                                    'in_transit' => [
                                        'icon' => 'feather-truck',
                                        'color' => '#007bff',
                                        'text_color' => '#ffffff',
                                        'label' => 'In Transit'
                                    ],
                                    'delivered' => [
                                        'icon' => 'feather-check-circle',
                                        'color' => '#28a745',
                                        'text_color' => '#ffffff',
                                        'label' => 'Delivered'
                                    ],
                                    'cancelled' => [
                                        'icon' => 'feather-x-circle',
                                        'color' => '#dc3545',
                                        'text_color' => '#ffffff',
                                        'label' => 'Cancelled'
                                    ],
                                    'failed' => [
                                        'icon' => 'feather-alert-circle',
                                        'color' => '#dc3545',
                                        'text_color' => '#ffffff',
                                        'label' => 'Failed'
                                    ],
                                    'processing' => [
                                        'icon' => 'feather-loader',
                                        'color' => '#007bff',
                                        'text_color' => '#ffffff',
                                        'label' => 'Processing'
                                    ],
                                    'completed' => [
                                        'icon' => 'feather-check-circle',
                                        'color' => '#28a745',
                                        'text_color' => '#ffffff',
                                        'label' => 'Completed'
                                    ],
                                    'refunded' => [
                                        'icon' => 'feather-rotate-ccw',
                                        'color' => '#6c757d',
                                        'text_color' => '#ffffff',
                                        'label' => 'Refunded'
                                    ],
                                    'disputed' => [
                                        'icon' => 'feather-alert-triangle',
                                        'color' => '#fd7e14',
                                        'text_color' => '#ffffff',
                                        'label' => 'Disputed'
                                    ]
                                ];
                                
                                // Get current status or default to 'unknown'
                                $status = $booking->status ?? 'unknown';
                                
                                // Get config for status or use fallback
                                $config = $statusConfig[$status] ?? [
                                    'icon' => 'feather-help-circle',
                                    'color' => '#6c757d',
                                    'text_color' => '#ffffff',
                                    'label' => ucfirst(str_replace('_', ' ', $status))
                                ];
                                
                                // Format label to be more readable
                                $label = $config['label'];
                            @endphp
                            
                            <span class="badge" style="
                                background-color: {{ $config['color'] }};
                                color: {{ $config['text_color'] }};
                                padding: 6px 12px;
                                border-radius: 6px;
                                font-size: 12px;
                                font-weight: 600;
                                display: inline-flex;
                                align-items: center;
                                gap: 6px;
                                white-space: nowrap;
                            ">
                                <i class="{{ $config['icon'] }}" style="font-size: 11px;"></i>
                                {{ $label }}
                            </span>
                        </td>
                        <td class="text-muted fs-12">{{ $booking->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('buyer.bookings.track', $booking->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="feather-map-pin me-1"></i> Track
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $bookings->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-truck mb-2 d-block" style="font-size:40px;"></i>
            <p class="mb-3">No bookings yet.</p>
            <a href="{{ route('rider.booking') }}" class="btn btn-primary">Book a Delivery</a>
        </div>
        @endif
    </div>
</div>
@endsection