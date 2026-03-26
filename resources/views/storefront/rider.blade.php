{{-- rider.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Book a Rider — Orderer</title>
    <link rel="icon" href="{{ asset('img/core-img/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orderer.css') }}">
</head>
<body>
@auth('web')@include('layouts.storefront.header-auth')@else@include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Book a Rider</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">

        {{-- Hero info cards --}}
        <div class="row justify-content-center mb-5">
            <div class="col-md-4 mb-3">
                <div style="text-align:center;padding:30px;border:1px solid #eee;border-radius:12px;">
                    <i class="fa fa-map-marker" style="font-size:36px;color:#2ECC71;margin-bottom:12px;display:block;"></i>
                    <h6 style="font-weight:700;">Local Delivery</h6>
                    <p style="color:#888;font-size:13px;">Book a rider for same-day delivery within your city or state.</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div style="text-align:center;padding:30px;border:1px solid #eee;border-radius:12px;">
                    <i class="fa fa-globe" style="font-size:36px;color:#2ECC71;margin-bottom:12px;display:block;"></i>
                    <h6 style="font-weight:700;">International Shipping</h6>
                    <p style="color:#888;font-size:13px;">Send packages anywhere in the world via our shipping partners.</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div style="text-align:center;padding:30px;border:1px solid #eee;border-radius:12px;">
                    <i class="fa fa-shield" style="font-size:36px;color:#2ECC71;margin-bottom:12px;display:block;"></i>
                    <h6 style="font-weight:700;">Insured &amp; Tracked</h6>
                    <p style="color:#888;font-size:13px;">All deliveries are tracked. Get real-time updates on your shipment.</p>
                </div>
            </div>
        </div>

        {{-- Booking form --}}
        @auth('web')
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div style="border:1px solid #eee;border-radius:12px;padding:32px;">
                    <h4 style="font-weight:800;margin-bottom:24px;">Book a Delivery</h4>
                    <form action="{{ route('rider.book') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Delivery Type</label>
                                <select name="delivery_type" class="form-select" required>
                                    <option value="" disabled selected>Select delivery type</option>
                                    <option value="local">🇳🇬 Local (Within Nigeria)</option>
                                    <option value="international">🌍 International</option>
                                </select>
                            </div>                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Item Description</label>
                                <input type="text" name="item_description" class="form-control"
                                       placeholder="What are you sending?" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Pickup Address</label>
                                <input type="text" name="pickup_address" class="form-control"
                                       placeholder="Full pickup address" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Pickup City / Country</label>
                                <input type="text" name="pickup_city" class="form-control"
                                       placeholder="City, Country" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Delivery Address</label>
                                <input type="text" name="delivery_address" class="form-control"
                                       placeholder="Full delivery address" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Delivery City / Country</label>
                                <input type="text" name="delivery_city" class="form-control"
                                       placeholder="City, Country" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Estimated Weight (kg)</label>
                            <input type="number" name="weight_kg" class="form-control"
                                   step="0.1" min="0.1" placeholder="0.0">
                        </div>

                        <button type="submit" class="btn essence-btn w-100" style="">
                            <i class="fa fa-truck mr-2"></i> Request Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-4">
            <p class="text-muted mb-3">Please sign in to book a rider.</p>
            <a href="{{ route('login') }}" class="btn essence-btn">Sign In</a>
        </div>
        @endauth

    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>
