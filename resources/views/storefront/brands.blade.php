{{-- resources/views/storefront/brands.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Brands — Orderer</title>
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
        <div class="page-title text-center"><h2>All Brands</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">
        <div class="row">
            @forelse($brands as $brand)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <a href="{{ route('brands.show', $brand->slug) }}" class="text-decoration-none">
                    <div style="border:1px solid #eee;border-radius:12px;padding:24px;text-align:center;transition:all .2s;"
                         onmouseover="this.style.borderColor='#2ECC71';this.style.boxShadow='0 4px 20px rgba(46,204,113,.12)';"
                         onmouseout="this.style.borderColor='#eee';this.style.boxShadow='none';">
                        @if($brand->logo)
                            <img src="{{ $brand->logo }}"
                                 style="height:60px;object-fit:contain;margin-bottom:12px;" alt="{{ $brand->name }}">
                        @else
                            <div style="width:60px;height:60px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;margin:0 auto 12px;">
                                {{ strtoupper(substr($brand->name, 0, 1)) }}
                            </div>
                        @endif
                        <h6 style="font-weight:700;color:#1a1a1a;margin-bottom:4px;">{{ $brand->name }}</h6>
                        <div style="color:#F39C12;font-size:13px;">
                            @for($i=1;$i<=5;$i++) {{ $i<=round($brand->average_rating)?'★':'☆' }} @endfor
                            <span style="color:#888;font-size:12px;">({{ $brand->total_reviews }})</span>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">
                <p>No brands yet.</p>
            </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $brands->links() }}</div>
    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>
