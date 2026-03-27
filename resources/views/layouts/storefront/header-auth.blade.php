<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="description" content="Orderer — Buy, sell and deliver anything, anywhere in the world." />
    <meta name="keywords" content="ecommerce Nigeria, buy online, sell online, orderer, marketplace, delivery" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
        } elseif (request()->is('brands*')) {
            $pageTitle = 'Brands';
        } else {
            $pageTitle = 'Global E-commerce Marketplace';
        } 

        $primaryProductImage = isset($product) ? $product->images->where('is_primary', true)->first() : null;
        $faviconUrl = $primaryProductImage
            ? asset($primaryProductImage->image)
            : (isset($brand) && $brand->logo ? asset($brand->logo) : asset('dashboard/assets/images/favicon.png'));

        $ogImage = $primaryProductImage
            ? asset($primaryProductImage->image)
            : (isset($brand) && $brand->logo ? asset($brand->logo) : asset('dashboard/assets/images/favicon.png'));
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
                        @foreach(\App\Models\Category::where('is_active',true)->with('subcategories')->take(3)->get() as $cat)
                        <ul class="mega-col">
                            <li class="mega-title">{{ $cat->name }}</li>
                            @foreach($cat->subcategories->where('is_active',true)->take(6) as $sub)
                            <li><a href="{{ route('shop.category', $sub->slug) }}">{{ $sub->name }}</a></li>
                            @endforeach
                        </ul>
                        @endforeach
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
                <i class="fa fa-usd"></i>
                {{ number_format(auth('web')->user()->wallet_balance, 2) }}
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
                <span id="cart-count">{{ session('cart') ? count(session('cart')) : 0 }}</span>
            </a>

            <!-- Mobile hamburger -->
            <button class="ord-hamburger" id="ordHamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>

        </div>
    </div>
</header>
<script>
    // ================================================
// ORDERER HEADER — JS (drop this in your app.js
// or in a <script> at bottom of layout)
// ================================================

// ================================================
// ORDERER HEADER — JS (fixed for mobile)
// ================================================

// User dropdown toggle - works on both mobile and desktop
const ordUserBtn = document.getElementById('ordUserBtn');
const ordDropdown = document.getElementById('ordDropdown');

if (ordUserBtn && ordDropdown) {
    // Handle both click and touch events for mobile
    const toggleDropdown = (e) => {
        e.stopPropagation();
        e.preventDefault();
        
        // Close any other open dropdowns/megamenus
        document.querySelectorAll('.ord-dropdown.open').forEach(drop => {
            if (drop !== ordDropdown) {
                drop.classList.remove('open');
            }
        });
        
        // Toggle this dropdown
        ordDropdown.classList.toggle('open');
        
        // Optional: Add debugging to see if it's toggling
        console.log('Dropdown toggled, open class:', ordDropdown.classList.contains('open'));
    };
    
    ordUserBtn.addEventListener('click', toggleDropdown);
    ordUserBtn.addEventListener('touchstart', toggleDropdown);
    
    // Close dropdown when clicking/tapping outside
    const closeDropdown = (e) => {
        if (!ordDropdown.contains(e.target) && !ordUserBtn.contains(e.target)) {
            ordDropdown.classList.remove('open');
        }
    };
    
    document.addEventListener('click', closeDropdown);
    document.addEventListener('touchstart', closeDropdown);
}

// Also make sure the mobile nav hamburger doesn't interfere
const ordHamburger = document.getElementById('ordHamburger');
const ordNav = document.getElementById('ordNav');

if (ordHamburger && ordNav) {
    ordHamburger.addEventListener('click', (e) => {
        e.stopPropagation();
        ordHamburger.classList.toggle('open');
        ordNav.classList.toggle('open');
        
        // Close user dropdown when opening mobile menu
        if (ordDropdown) {
            ordDropdown.classList.remove('open');
        }
    });
}
</script>