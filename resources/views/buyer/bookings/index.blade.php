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
                        <td class="fw-bold text-success">${{ number_format($booking->fee, 2) }}</td>
                        <td>
                            @if($booking->tracking_number)
                                <code class="fs-12">{{ $booking->tracking_number }}</code>
                            @else
                                <span class="text-muted fs-12">Pending</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $icons = [
                                    'pending'    => 'feather-clock',
                                    'confirmed'  => 'feather-check-circle',
                                    'picked_up'  => 'feather-package',
                                    'in_transit' => 'feather-truck',
                                    'delivered'  => 'feather-check-circle',
                                    'cancelled'  => 'feather-x-circle',
                                ];
                                $icon = $icons[$booking->status] ?? 'feather-help-circle';
                            @endphp
                            <span class="badge orderer-badge badge-{{ $booking->status }}">
                                <i class="{{ $icon }} me-1" style="font-size:11px;"></i>
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
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