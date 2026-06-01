<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="description" content="Orderer — Buy, sell and deliver anything, anywhere in the world." />
    <meta name="keywords" content="ecommerce Nigeria, buy online, sell online, orderer, marketplace, delivery" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="MXUdLHni2llDyaPRU3aVbFPPDRLod4XKutL_HofeL8s" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- X (Twitter) Pixel Base Code -->
    <script>
    !function(e,t,n,s,u,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);
    },s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='https://static.ads-twitter.com/uwt.js',
    a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))}(window,document,'script');
    twq('config','rc8yq'); 
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QXCBGT2TY0"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-QXCBGT2TY0');
    </script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18185039672">
    </script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'AW-18185039672');
    </script>
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

/* ── Search suggestion dropdown ── */
.ord-search { position: relative; }

.ord-suggest {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    min-width: 320px;
    background: var(--color-background-primary, #fff);
    border: 0.5px solid rgba(0,0,0,.15);
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.10);
    z-index: 9999;
    overflow: hidden;
    display: none;
}
.ord-suggest.open { display: block; }

.ord-suggest-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 14px;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    transition: background .12s;
}
.ord-suggest-item:hover,
.ord-suggest-item.active {
    background: rgba(0,0,0,.04);
}

.ord-suggest-thumb {
    width: 38px;
    height: 38px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
    background: rgba(0,0,0,.06);
}
.ord-suggest-thumb-placeholder {
    width: 38px;
    height: 38px;
    border-radius: 6px;
    flex-shrink: 0;
    background: rgba(0,0,0,.06);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: rgba(0,0,0,.3);
}

.ord-suggest-text { flex: 1; min-width: 0; }
.ord-suggest-label {
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: var(--color-text-primary, #111);
}
.ord-suggest-meta {
    font-size: 11px;
    color: var(--color-text-secondary, #666);
    margin-top: 1px;
}

.ord-suggest-badge {
    font-size: 10px;
    font-weight: 500;
    padding: 2px 7px;
    border-radius: 20px;
    background: rgba(0,0,0,.06);
    color: var(--color-text-secondary, #666);
    flex-shrink: 0;
}
.ord-suggest-badge.brand { background: #e8f4ff; color: #185fa5; }

.ord-suggest-empty {
    padding: 14px;
    font-size: 13px;
    color: var(--color-text-secondary, #888);
    text-align: center;
}
.ord-suggest-footer {
    border-top: 0.5px solid rgba(0,0,0,.08);
    padding: 8px 14px;
    font-size: 12px;
    color: var(--color-text-secondary, #888);
    text-align: center;
}
.ord-suggest-footer a {
    color: #2ECC71;
    font-weight: 500;
    text-decoration: none;
}
.ord-header,
.ord-header-inner,
.ord-actions,
.ord-search {
    overflow: visible !important;
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
        } elseif ($routeName === 'rider' || request()->is('rider*')) {
            $pageTitle = 'Book a Delivery';
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
    @stack('head')
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
(function () {
    const SUGGEST_URL = '{{ route("search.suggestions") }}';
    const SEARCH_URL  = '{{ route("search") }}';

    document.querySelectorAll('.ord-search').forEach(function (form) {
        const input = form.querySelector('input[name="q"]');
        if (!input) return;

        // Build the dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'ord-suggest';
        form.appendChild(dropdown);

        let debounceTimer = null;
        let activeIndex   = -1;
        let currentItems  = [];

        function highlight(index) {
            currentItems.forEach(function (el, i) {
                el.classList.toggle('active', i === index);
            });
        }

        function close() {
            dropdown.classList.remove('open');
            activeIndex = -1;
        }

        function open() {
            dropdown.classList.add('open');
        }

        function renderResults(data, q) {
            dropdown.innerHTML = '';
            activeIndex = -1;

            if (!data.length) {
                dropdown.innerHTML = '<div class="ord-suggest-empty">No results for "<strong>' + escHtml(q) + '</strong>"</div>';
                open();
                return;
            }

            data.forEach(function (item) {
                var a = document.createElement('a');
                a.href = item.url;
                a.className = 'ord-suggest-item';

                // Thumb
                if (item.image) {
                    var img = document.createElement('img');
                    img.className = 'ord-suggest-thumb';
                    img.src = item.image;
                    img.alt = '';
                    img.loading = 'lazy';
                    a.appendChild(img);
                } else {
                    var ph = document.createElement('div');
                    ph.className = 'ord-suggest-thumb-placeholder';
                    ph.textContent = item.type === 'brand' ? '🏷' : '📦';
                    a.appendChild(ph);
                }

                // Text block
                var text = document.createElement('div');
                text.className = 'ord-suggest-text';

                var label = document.createElement('div');
                label.className = 'ord-suggest-label';
                label.textContent = item.label;
                text.appendChild(label);

                if (item.price) {
                    var meta = document.createElement('div');
                    meta.className = 'ord-suggest-meta';
                    meta.textContent = item.price;
                    text.appendChild(meta);
                }
                a.appendChild(text);

                // Badge
                var badge = document.createElement('span');
                badge.className = 'ord-suggest-badge' + (item.type === 'brand' ? ' brand' : '');
                badge.textContent = item.type === 'brand' ? 'Brand' : 'Product';
                a.appendChild(badge);

                dropdown.appendChild(a);
            });

            // "See all results" footer
            var footer = document.createElement('div');
            footer.className = 'ord-suggest-footer';
            var link = document.createElement('a');
            link.href = SEARCH_URL + '?q=' + encodeURIComponent(q);
            link.textContent = 'See all results for "' + q + '" →';
            footer.appendChild(link);
            dropdown.appendChild(footer);

            currentItems = Array.from(dropdown.querySelectorAll('.ord-suggest-item'));
            open();
        }

        function fetchSuggestions(q) {
            fetch(SUGGEST_URL + '?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) { renderResults(data, q); })
            .catch(function () { close(); });
        }

        input.addEventListener('input', function () {
            var q = input.value.trim();
            clearTimeout(debounceTimer);
            if (q.length < 2) { close(); return; }
            debounceTimer = setTimeout(function () { fetchSuggestions(q); }, 220);
        });

        // Keyboard navigation
        input.addEventListener('keydown', function (e) {
            if (!dropdown.classList.contains('open')) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, currentItems.length - 1);
                highlight(activeIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                highlight(activeIndex);
            } else if (e.key === 'Enter' && activeIndex >= 0) {
                e.preventDefault();
                currentItems[activeIndex].click();
            } else if (e.key === 'Escape') {
                close();
            }
        });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (!form.contains(e.target)) close();
        });

        // Prevent form submit hiding results (let Enter key handle navigation)
        input.addEventListener('focus', function () {
            if (input.value.trim().length >= 2 && dropdown.innerHTML) open();
        });

        function escHtml(str) {
            return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
    });
})();
</script>

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