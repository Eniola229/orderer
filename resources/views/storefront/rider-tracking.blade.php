@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Track Shipment</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                @php
                    $isCancelled = $booking->status === 'cancelled';
                    $isDelivered = $booking->status === 'delivered';
                    $stages      = ['pending','confirmed','picked_up','in_transit','delivered'];
                    $currentIdx  = array_search($booking->status, $stages);
                    $currentIdx  = $currentIdx !== false ? $currentIdx : 0;

                    $badgeBg    = $isDelivered ? '#D5F5E3' : ($isCancelled ? '#FADBD8' : '#FEF9E7');
                    $badgeColor = $isDelivered ? '#1E8449' : ($isCancelled ? '#C0392B' : '#B7950B');
                @endphp
 
                {{-- Booking summary card --}}
                <div style="border:1px solid {{ $isCancelled ? '#FADBD8' : '#eee' }};border-radius:12px;padding:24px;margin-bottom:24px;
                            {{ $isCancelled ? 'background:#FFF9F9;' : '' }}">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                        <h5 style="font-weight:800;margin:0;">{{ $booking->booking_number }}</h5>
                        <span style="background:{{ $badgeBg }};color:{{ $badgeColor }};
                                     padding:4px 14px;border-radius:12px;font-size:12px;font-weight:700;">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small style="color:#888;display:block;">From</small>
                            <strong>{{ $booking->pickup_address }}, {{ $booking->pickup_country }}</strong>
                        </div>
                        <div class="col-6">
                            <small style="color:#888;display:block;">To</small>
                            <strong>{{ $booking->delivery_address }}, {{ $booking->delivery_country }}</strong>
                        </div>
                        <div class="col-6 mt-3">
                            <small style="color:#888;display:block;">Carrier</small>
                            <strong>{{ $booking->carrier ?? 'Assigned' }}</strong>
                            @if($booking->service_name)
                                <small style="color:#888;display:block;">{{ $booking->service_name }}</small>
                            @endif
                        </div>
                        <div class="col-6 mt-3">
                            <small style="color:#888;display:block;">Tracking #</small>
                            @if($booking->tracking_number)
                                <strong style="color:#2ECC71;">{{ $booking->tracking_number }}</strong>
                            @else
                                <span style="color:#aaa;">Pending assignment</span>
                            @endif
                        </div>
                        @if($booking->estimated_delivery_date)
                        <div class="col-12 mt-3">
                            <small style="color:#888;display:block;">Estimated Delivery</small>
                            <strong>{{ $booking->estimated_delivery_date }}</strong>
                        </div>
                        @endif
                        @if($booking->tracking_url && !$isCancelled)
                        <div class="col-12 mt-3">
                            <a href="{{ $booking->tracking_url }}" target="_blank" class="btn essence-btn btn-sm">
                                <i class="fa fa-external-link mr-2"></i>
                                Track on {{ $booking->carrier }} Website
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Progress bar --}}
                <div style="border:1px solid {{ $isCancelled ? '#FADBD8' : '#eee' }};border-radius:12px;padding:24px;margin-bottom:24px;
                            {{ $isCancelled ? 'background:#FFF9F9;' : '' }}">
                    <h6 style="font-weight:700;margin-bottom:20px;">
                        Delivery Progress
                        @if($isCancelled)
                            <span style="color:#C0392B;font-size:13px;font-weight:600;margin-left:8px;">— Cancelled</span>
                        @endif
                    </h6>

                    @if($isCancelled)
                        <div style="text-align:center;padding:20px 0;color:#C0392B;">
                            <i class="fa fa-times-circle" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                            <p style="margin:0;font-weight:600;font-size:15px;">This shipment has been cancelled.</p>
                        </div>
                    @else
                        <div style="display:flex;align-items:center;justify-content:space-between;position:relative;">
                            <div style="position:absolute;top:14px;left:0;right:0;height:3px;background:#eee;z-index:0;"></div>
                            <div style="position:absolute;top:14px;left:0;height:3px;background:#2ECC71;z-index:1;
                                        width:{{ ($currentIdx / (count($stages)-1)) * 100 }}%;transition:width .5s;"></div>
                            @foreach($stages as $i => $stage)
                            <div style="text-align:center;position:relative;z-index:2;">
                                <div style="width:30px;height:30px;border-radius:50%;
                                            background:{{ $i <= $currentIdx ? '#2ECC71' : '#eee' }};
                                            color:{{ $i <= $currentIdx ? '#fff' : '#aaa' }};
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:12px;font-weight:700;margin:0 auto 6px;">
                                    {{ $i < $currentIdx ? '✓' : ($i + 1) }}
                                </div>
                                <small style="font-size:10px;color:{{ $i <= $currentIdx ? '#2ECC71' : '#aaa' }};
                                             font-weight:600;text-transform:uppercase;">
                                    {{ ucfirst(str_replace('_', ' ', $stage)) }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Tracking events --}}
                @if(count($trackingEvents))
                    <div style="border:1px solid #eee;border-radius:12px;padding:24px;">
                        <h6 style="font-weight:700;margin-bottom:20px;">Tracking Events</h6>
                        @foreach($trackingEvents as $event)
                        <div style="display:flex;gap:14px;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #f5f5f5;">
                            <div style="width:10px;height:10px;border-radius:50%;background:#2ECC71;margin-top:5px;flex-shrink:0;"></div>
                            <div>
                                <p style="margin:0;font-weight:600;font-size:14px;">
                                    {{ $event['status'] ?? $event['description'] ?? $event['message'] ?? 'Update' }}
                                </p>
                                @if(isset($event['description']) && isset($event['status']))
                                    <p style="margin:2px 0 0;font-size:13px;color:#666;">{{ $event['description'] }}</p>
                                @endif
                                @if(!empty($event['location']))
                                    <small style="color:#aaa;">
                                        <i class="fa fa-map-marker mr-1"></i>{{ $event['location'] }}
                                    </small>
                                @endif
                                @if(isset($event['captured']) || isset($event['datetime']) || isset($event['event_at']))
                                    <small style="color:#aaa;display:block;">
                                        {{ \Carbon\Carbon::parse($event['captured'] ?? $event['datetime'] ?? $event['event_at'])->format('M d, Y H:i') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                @elseif($isCancelled)
                    <div style="border:1px solid #FADBD8;border-radius:12px;padding:40px;text-align:center;background:#FFF9F9;">
                        <i class="fa fa-times-circle" style="font-size:48px;margin-bottom:12px;display:block;color:#C0392B;"></i>
                        <p style="color:#C0392B;font-weight:600;margin:0;font-size:15px;">This shipment was cancelled.</p>
                        @if(!empty($packageStatus))
                            <div style="margin-top:16px;">
                                @foreach($packageStatus as $ps)
                                    <small style="color:#aaa;display:block;margin-top:4px;">
                                        {{ $ps['status'] }}
                                        @if(isset($ps['datetime']))
                                            — {{ \Carbon\Carbon::parse($ps['datetime'])->format('M d, Y H:i') }}
                                        @endif
                                    </small>
                                @endforeach
                            </div>
                        @endif
                    </div>

                @else
                    <div style="border:1px solid #eee;border-radius:12px;padding:40px;text-align:center;color:#aaa;">
                        <i class="fa fa-map-signs" style="font-size:36px;margin-bottom:12px;display:block;color:#ddd;"></i>
                        <p>No tracking events yet. Check back once your package is picked up.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>