<header class="header_area">
    <div class="classy-nav-container breakpoint-off d-flex align-items-center justify-content-between">

        <nav class="classy-navbar" id="ordererNav">
            <a class="nav-brand" href="{{ route('home') }}">
                <img src="{{ asset('img/core-img/logo.png') }}" alt="Orderer">
            </a>
            <div class="classy-navbar-toggler">
                <span class="navbarToggler"><span></span><span></span><span></span></span>
            </div>
            <div class="classy-menu">
                <div class="classycloseIcon">
                    <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
                </div>
                <div class="classynav">
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="#">Shop</a>
                            <div class="megamenu">
                                @foreach(\App\Models\Category::where('is_active',true)->with('subcategories')->take(3)->get() as $cat)
                                <ul class="single-mega cn-col-4">
                                    <li class="title">{{ $cat->name }}</li>
                                    @foreach($cat->subcategories->where('is_active',true)->take(6) as $sub)
                                    <li><a href="{{ route('shop.category', $sub->slug) }}">{{ $sub->name }}</a></li>
                                    @endforeach
                                </ul>
                                @endforeach
                                <div class="single-mega cn-col-4">
                                    <img src="{{ asset('img/bg-img/bg-6.jpg') }}" alt="">
                                </div>
                            </div>
                        </li>
                        <li><a href="{{ route('brands.index') }}">Brands</a></li>
                        <li><a href="{{ route('services.index') }}">Services</a></li>
                        <li><a href="{{ route('houses.index') }}">Properties</a></li>
                        <li><a href="{{ route('rider.booking') }}">Book Rider</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="header-meta d-flex clearfix align-items-center gap-2">

            {{-- Search --}}
            <div class="search-area">
                <form action="{{ route('search') }}" method="GET">
                    <input type="search" name="q" placeholder="Search products…"
                           value="{{ request('q') }}">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>

            {{-- Wishlist --}}
            <div class="favourite-area">
                <a href="{{ route('login') }}" title="Wishlist">
                    <img src="{{ asset('img/core-img/heart.svg') }}" alt="Wishlist">
                </a>
            </div>

            {{-- Account --}}
            <div class="user-login-info">
                <a href="{{ route('login') }}" title="Sign in">
                    <img src="{{ asset('img/core-img/user.svg') }}" alt="Login">
                </a>
            </div>

            {{-- Cart --}}
            <div class="cart-area">
                <a href="#" id="essenceCartBtn" title="Cart">
                    <img src="{{ asset('img/core-img/bag.svg') }}" alt="Cart">
                    <span id="cart-count">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                </a>
            </div>

            {{-- Start Selling CTA --}}
            <div class="ms-1">
                <a href="{{ route('seller.register') }}"
                   class="btn essence-btn"
                   style="padding:9px 20px; font-size:13px; font-weight:700; border-radius:8px; white-space:nowrap; letter-spacing:0.3px;">
                    Start Selling
                </a>
            </div>

        </div>
    </div>
</header>