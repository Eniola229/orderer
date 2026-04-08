<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="description" content="Orderer — Buy, sell and deliver anything, anywhere in the world." />
    <meta name="keywords" content="ecommerce Nigeria, buy online, sell online, orderer, marketplace, delivery" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    {{-- Dynamic Title, Favicon & OG Variables --}}
    @php
        $routeName = Route::currentRouteName();

        // Page title
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
        } elseif (isset($house)) {
            $pageTitle = $house->title;
        } elseif (request()->is('brands*')) {
            $pageTitle = 'Brands';
        } else {
            $pageTitle = 'Orderer -- Global E-commerce Marketplace';
        }

        // OG image — use dynamic composited image for entity pages, plain image otherwise
        $ogImage = match(true) {
            isset($product) => route('og.image', ['type' => 'product', 'slug' => $product->slug]),
            isset($brand)   => route('og.image', ['type' => 'brand',   'slug' => $brand->slug]),
            isset($service) => route('og.image', ['type' => 'service', 'slug' => $service->slug]),
            isset($house)   => route('og.image', ['type' => 'house',   'slug' => $house->slug]),
            default         => asset('dashboard/assets/images/og-default.png'),
        };

        // Favicon — just the raw image, no overlay needed for browser tab
        $primaryProductImage = isset($product) ? $product->images->where('is_primary', true)->first() : null;
        $faviconUrl = $primaryProductImage?->image_url
            ?? (isset($brand) && $brand->logo
                ? $brand->logo
                : (isset($service) && is_array($service->portfolio_images) && count($service->portfolio_images)
                    ? (is_array($service->portfolio_images[0]) ? ($service->portfolio_images[0]['url'] ?? null) : $service->portfolio_images[0])
                    : (isset($house) && $house->images->first()
                        ? $house->images->first()->image_url
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
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:site_name" content="Orderer" />

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $pageTitle }} — Orderer" />
    <meta name="twitter:description" content="Buy, sell and deliver anything anywhere in the world." />
    <meta name="twitter:image" content="{{ $ogImage }}" />

    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/orderer-theme.css') }}" />
</head>
<body>
<!-- ================================================
     ORDERER HEADER — Full rebuild, no plugin needed
     ================================================ -->
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

            <!-- Wallet -->
            <a href="{{ route('buyer.wallet') }}" class="ord-wallet">
                ₦{{ number_format(auth('web')->user()->wallet_balance, 2) }}
            </a>

            <!-- Wishlist -->
            <a href="{{ route('buyer.wishlist') }}" class="ord-icon-btn" title="Wishlist">
                <img src="{{ asset('img/core-img/heart.svg') }}" alt="Wishlist">
            </a>

            <!-- User dropdown -->
            <div class="ord-user-drop">
                <button class="ord-user-btn" id="ordUserBtn">
                    @if(auth('web')->user()->avatar)
                        <img src="{{ auth('web')->user()->avatar }}" class="ord-avatar-img" alt="">
                    @else
                        <span class="ord-avatar-initials">
                            {{ strtoupper(substr(auth('web')->user()->first_name,0,1)) }}{{ strtoupper(substr(auth('web')->user()->last_name,0,1)) }}
                        </span>
                    @endif
                    <span class="ord-user-name">{{ auth('web')->user()->first_name }}</span>
                    <i class="fa fa-angle-down"></i>
                </button>
                <div class="ord-dropdown" id="ordDropdown">
                    <div class="ord-drop-header">
                        <strong>{{ auth('web')->user()->full_name }}</strong>
                        <small>{{ auth('web')->user()->email }}</small>
                    </div>
                    <a href="{{ route('buyer.dashboard') }}"><i class="fa fa-tachometer"></i> Dashboard</a>
                    <a href="{{ route('buyer.orders') }}"><i class="fa fa-shopping-bag"></i> My Orders</a>
                    <a href="{{ route('buyer.wallet') }}"><i class="fa fa-usd"></i> Wallet</a>
                    <a href="{{ route('buyer.wishlist') }}"><i class="fa fa-heart"></i> Wishlist</a>
                    <div class="ord-drop-divider"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="ord-drop-logout">
                            <i class="fa fa-sign-out"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cart -->
            <a href="#" id="essenceCartBtn" class="ord-icon-btn ord-cart" title="Cart">
                <img src="{{ asset('img/core-img/bag.svg') }}" alt="Cart">
                <span id="cart-count">0</span>
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
// User dropdown toggle
const ordUserBtn = document.getElementById('ordUserBtn');
const ordDropdown = document.getElementById('ordDropdown');

if (ordUserBtn && ordDropdown) {
    const toggleDropdown = (e) => {
        e.stopPropagation();
        e.preventDefault();
        document.querySelectorAll('.ord-dropdown.open').forEach(drop => {
            if (drop !== ordDropdown) drop.classList.remove('open');
        });
        ordDropdown.classList.toggle('open');
    };

    ordUserBtn.addEventListener('click', toggleDropdown);
    ordUserBtn.addEventListener('touchstart', toggleDropdown);

    const closeDropdown = (e) => {
        if (!ordDropdown.contains(e.target) && !ordUserBtn.contains(e.target)) {
            ordDropdown.classList.remove('open');
        }
    };

    document.addEventListener('click', closeDropdown);
    document.addEventListener('touchstart', closeDropdown);
}

// Mobile hamburger
const ordHamburger = document.getElementById('ordHamburger');
const ordNav = document.getElementById('ordNav');

if (ordHamburger && ordNav) {
    ordHamburger.addEventListener('click', (e) => {
        e.stopPropagation();
        ordHamburger.classList.toggle('open');
        ordNav.classList.toggle('open');
        if (ordDropdown) ordDropdown.classList.remove('open');
    });
}
</script>