<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="description" content="Orderer — Buy, sell and deliver anything, anywhere in the world." />
    <meta name="keywords" content="ecommerce Nigeria, buy online, sell online, orderer, marketplace, delivery" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/theme.min.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .swal-deny-visible {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    .has-mega:hover .ord-megamenu {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
    }
    .mega-col {
        display: block !important;
    }
    .mega-col li {
        display: block !important;
    }
    .mega-col li a {
        display: block !important;
    }
    
</style>

    {{-- Dynamic Title & Favicon Variables --}}
    @php
        $routeName = Route::currentRouteName();
        if ($routeName === 'home' || request()->is('/')) {
            $pageTitle = 'Home';
        } elseif ($routeName === 'shop' || request()->is('shop*')) {
            $pageTitle = 'Shop';
        } elseif (isset($product)) {
            $pageTitle = $product->name;
        } elseif (isset($brand)) {
            $pageTitle = $brand->name;
        } elseif (isset($service)) {
            $pageTitle = $service->title;
        } elseif (isset($property)) {
            $pageTitle = $property->title;
        } elseif (request()->is('brands*')) {
            $pageTitle = 'Brands';
        } else {
            $pageTitle = 'Orderer -- Global E-commerce Marketplace';
        } 

        $primaryProductImage = isset($product) ? $product->images->where('is_primary', true)->first() : null;
        $faviconUrl = $primaryProductImage
            ? asset($primaryProductImage->image_url)
            : (isset($brand) && $brand->logo ? asset($brand->logo) 
            : (isset($service) && isset($service->portfolio_images[0]['url']) ? $service->portfolio_images[0]['url']
            : (isset($property) && $property->images->first() ? $property->images->first()->image_url 
            : asset('dashboard/assets/images/favicon.png'))));

        $ogImage = $primaryProductImage
            ? asset($primaryProductImage->image_url)
            : (isset($brand) && $brand->logo ? asset($brand->logo) 
            : (isset($service) && isset($service->portfolio_images[0]['url']) ? $service->portfolio_images[0]['url']
            : (isset($property) && $property->images->first() ? $property->images->first()->image_url 
            : asset('dashboard/assets/images/favicon.png'))));
    @endphp

    <title>{{ $pageTitle }} — Orderer</title>

    {{-- Dynamic Favicon --}}
    <link rel="shortcut icon" type="image/x-icon" href="{{ $faviconUrl }}" />
    <link rel="icon" href="{{ $faviconUrl }}">

    {{-- OG Meta --}}
    <meta property="og:title" content="{{ $pageTitle }} — Orderer" />
    <meta property="og:description" content="Buy, sell and deliver anything anywhere in the world." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta property="og:site_name" content="Orderer" />

    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/orderer-theme.css') }}" />
</head>
<body>

<header class="ord-header">
    <div class="ord-header-inner">

        <!-- Logo -->
        <a class="ord-logo" href="{{ route('home') }}">
            <img src="{{ asset('img/core-img/logo.png') }}" alt="Orderer">
        </a>

        <!-- Nav -->
        <nav class="ord-nav" id="ordNav">
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li class="has-mega">
                    <a href="{{ route('shop.index') }}">Shop <i class="fa fa-angle-down"></i></a>
                    <div class="ord-megamenu">
                        @foreach(\App\Models\Category::where('is_active',true)->with('subcategories')->take(4)->get() as $cat)
                        <ul class="mega-col">
                            <li class="mega-title">{{ $cat->name }}</li>
                            @foreach($cat->subcategories->where('is_active',true)->take(6) as $sub)
                            <li><a href="{{ route('shop.category', $sub->slug) }}">{{ $sub->name }}</a></li>
                            @endforeach
                        </ul>
                        @endforeach
                        <ul class="mega-col">
                            <li class="mega-title">Browse</li>
                            <li><a href="{{ route('shop.index') }}">All Products</a></li>
                            <li><a href="{{ route('brands.index') }}">All Brands</a></li>
                        </ul>
                    </div>
                </li>
                <li><a href="{{ route('brands.index') }}">Brands</a></li>
                <li><a href="{{ route('services.index') }}">Services</a></li>
                <li><a href="{{ route('houses.index') }}">Properties</a></li>
                <li><a href="{{ route('rider.booking') }}">Book a Delivery</a></li>
                <li><a href="{{ route('contact') }}">Contact</a></li>
            </ul>
        </nav>

        <!-- Actions -->
        <div class="ord-actions">

            <!-- Search -->
            <form class="ord-search" action="{{ route('search') }}" method="GET">
                <input type="search" name="q" placeholder="Search products…" value="{{ request('q') }}">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>

            <!-- Wishlist -->
            <a href="{{ route('login') }}" class="ord-icon-btn" title="Wishlist">
                <img src="{{ asset('img/core-img/heart.svg') }}" alt="Wishlist">
            </a>

            <!-- Login -->
            <a href="{{ route('login') }}" class="ord-icon-btn" title="Sign in">
                <img src="{{ asset('img/core-img/user.svg') }}" alt="Login">
            </a>

            <!-- Cart -->
            <a href="#" id="essenceCartBtn" class="ord-icon-btn ord-cart" title="Cart">
                <img src="{{ asset('img/core-img/bag.svg') }}" alt="Cart">
                <span id="cart-count">0</span>
            </a>

            <!-- Start Selling CTA -->
            <a href="{{ route('seller.register') }}" class="ord-sell-btn">
                Start Selling
            </a>

            <!-- Mobile hamburger -->
            <button class="ord-hamburger" id="ordHamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>

        </div>
    </div>
</header>
<script>
// ── Global cart alert with checkout + continue shopping buttons ──
window.cartToast = function(message = 'Added to cart!', icon = 'success') {

    if (icon !== 'success') {
        // Simple toast for errors/warnings — no buttons needed
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        return;
    }

    // Success — show popup with action buttons
    Swal.fire({
        icon: 'success',
        title: 'Added to Cart!',
        text: message,
        showConfirmButton: true,
        confirmButtonText: 'Checkout',
        confirmButtonColor: '#2ECC71',
        showDenyButton: true,
        denyButtonText: 'Continue Shopping',
        denyButtonColor: '#6c757d',
        reverseButtons: true,
        allowOutsideClick: true,
        timer: 6000,
        timerProgressBar: true,
        customClass: {
            denyButton: 'swal-deny-visible'
        },
        didOpen: (popup) => {
            popup.addEventListener('mouseenter', Swal.stopTimer);
            popup.addEventListener('mouseleave', Swal.resumeTimer);
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route("checkout") }}';
        }
    });
};
</script>
<script>
// Mobile hamburger toggle
const ordHamburger = document.getElementById('ordHamburger');
const ordNav       = document.getElementById('ordNav');

if (ordHamburger && ordNav) {
    ordHamburger.addEventListener('click', () => {
        ordHamburger.classList.toggle('open');
        ordNav.classList.toggle('open');
    });
}
</script>