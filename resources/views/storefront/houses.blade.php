<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Properties — Orderer</title>
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
        <div class="page-title text-center"><h2>Properties</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">

        {{-- Listing type filter --}}
        <div class="d-flex gap-2 mb-4 justify-content-center flex-wrap">
            @foreach(['all' => 'All', 'sale' => 'For Sale', 'rent' => 'For Rent', 'shortlet' => 'Shortlet'] as $val => $label)
            <a href="{{ request()->fullUrlWithQuery(['type' => $val]) }}"
               class="btn btn-sm {{ request('type', 'all') === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        <div class="row">
            @forelse($houses as $house)
            @php $img = $house->images->where('is_primary',true)->first() ?? $house->images->first(); @endphp
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <div style="border:1px solid #eee;border-radius:12px;overflow:hidden;height:100%;">
                    <div style="position:relative;">
                        <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}"
                             style="width:100%;height:200px;object-fit:cover;" alt="">
                        <span style="position:absolute;top:12px;left:12px;background:#2ECC71;color:#fff;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700;">
                            {{ ucfirst($house->listing_type) }}
                        </span>
                        <span style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,.5);color:#fff;padding:4px 10px;border-radius:12px;font-size:12px;">
                            {{ ucfirst($house->property_type) }}
                        </span>
                    </div>
                    <div style="padding:20px;">
                        <h6 style="font-weight:700;margin-bottom:6px;">{{ Str::limit($house->title, 50) }}</h6>
                        <p style="color:#888;font-size:13px;margin-bottom:10px;">
                            <i class="fa fa-map-marker mr-1" style="color:#2ECC71;"></i>
                            {{ $house->city }}, {{ $house->state }}, {{ $house->country }}
                        </p>
                        <div style="display:flex;gap:16px;margin-bottom:12px;font-size:13px;color:#666;">
                            @if($house->bedrooms !== null)
                            <span><i class="fa fa-bed mr-1"></i>{{ $house->bedrooms }} Bed</span>
                            @endif
                            @if($house->bathrooms !== null)
                            <span><i class="fa fa-bath mr-1"></i>{{ $house->bathrooms }} Bath</span>
                            @endif
                            @if($house->size_sqm)
                            <span><i class="fa fa-arrows-alt mr-1"></i>{{ $house->size_sqm }} sqm</span>
                            @endif
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <span style="font-size:20px;font-weight:800;color:#2ECC71;">
                                ${{ number_format($house->price, 0) }}
                            </span>
                            <a href="#" class="btn essence-btn btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa fa-home" style="font-size:48px;color:#ddd;margin-bottom:16px;display:block;"></i>
                <p>No properties listed yet.</p>
            </div>
            @endforelse
        </div>
        <div>{{ $houses->links() }}</div>
    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>
