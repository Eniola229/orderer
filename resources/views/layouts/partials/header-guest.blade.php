<header class="header_area">
    <div class="classy-nav-container breakpoint-off d-flex align-items-center justify-content-between">

        <nav class="classy-navbar" id="ordererNav">

            {{-- Logo --}}
            <a class="nav-brand" href="{{ route('home') }}">
                <img src="{{ asset('img/core-img/logo.png') }}" alt="Orderer">
            </a>

            {{-- Mobile toggler --}}
            <div class="classy-navbar-toggler">
                <span class="navbarToggler">
                    <span></span><span></span><span></span>
                </span>
            </div>

            {{-- Menu --}}
            <div class="classy-menu">
                <div class="classycloseIcon">
                    <div class="cross-wrap">
                        <span class="top"></span>
                        <span class="bottom"></span>
                    </div>
                </div>

                <div class="classynav">
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>

                        {{-- Shop with mega menu --}}
                        <li><a href="#">Shop</a>
                            <div class="megamenu">
                                @foreach(\App\Models\Category::where('is_active', true)->with('subcategories')->take(3)->get() as $category)
                                <ul class="single-mega cn-col-4">
                                    <li class="title">{{ $category->name }}</li>
                                    @foreach($category->subcategories->where('is_active', true)->take(6) as $sub)
                                    <li>
                                        <a href="{{ route('shop.category', $sub->slug) }}">
                                            {{ $sub->name }}
                                        </a>
                                    </li>
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
                        <li><a href="{{ route('rider.booking') }}">Book a Rider</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        {{-- Header right meta --}}
        <div class="header-meta d-flex clearfix justify-content-end align-items-center">

            {{-- Search --}}
            <div class="search-area">
                <form action="{{ route('search') }}" method="GET">
                    @csrf
                    <input type="search"
                           name="q"
                           id="headerSearch"
                           placeholder="Search products..."
                           value="{{ request('q') }}">
                    <button type="submit">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                </form>
            </div>

            {{-- Wishlist --}}
            <div class="favourite-area">
                <a href="{{ route('login') }}">
                    <img src="{{ asset('img/core-img/heart.svg') }}" alt="Wishlist">
                </a>
            </div>

            {{-- Login --}}
            <div class="user-login-info">
                <a href="{{ route('login') }}">
                    <img src="{{ asset('img/core-img/user.svg') }}" alt="Login">
                </a>
            </div>

            {{-- Cart --}}
            <div class="cart-area">
                <a href="#" id="essenceCartBtn">
                    <img src="{{ asset('img/core-img/bag.svg') }}" alt="Cart">
                    <span id="cart-count">0</span>
                </a>
            </div>

            {{-- Sell on Orderer --}}
            <div class="ml-3">
                <a href="{{ route('seller.register') }}"
                   class="btn essence-btn"
                   style="padding: 8px 16px; font-size: 13px;">
                    Start Selling
                </a>
            </div>

        </div>
    </div>
</header>