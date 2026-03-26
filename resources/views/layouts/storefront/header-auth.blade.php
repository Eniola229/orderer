<style>
    
</style>
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

            {{-- Wallet pill --}}
            <a href="{{ route('buyer.wallet') }}"
               style="background:#D5F5E3; color:#1E8449; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; text-decoration:none; white-space:nowrap; display:flex; align-items:center; gap:4px;">
                <i class="fa fa-usd" style="font-size:11px;"></i>
                {{ number_format(auth('web')->user()->wallet_balance, 2) }}
            </a>

            {{-- Wishlist --}}
            <div class="favourite-area">
                <a href="{{ route('buyer.wishlist') }}" title="Wishlist">
                    <img src="{{ asset('img/core-img/heart.svg') }}" alt="Wishlist">
                </a>
            </div>

            {{-- User dropdown --}}
            <div class="user-login-info dropdown">
                <a href="#" id="userDrop" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false"
                   style="display:flex; align-items:center; gap:6px; text-decoration:none; border:1.5px solid #e8e8e8; border-radius:8px; padding:4px 10px 4px 4px;">
                    @if(auth('web')->user()->avatar)
                        <img src="{{ auth('web')->user()->avatar }}"
                             style="width:28px; height:28px; border-radius:6px; object-fit:cover;" alt="">
                    @else
                        <span style="width:28px; height:28px; border-radius:6px; background:#2ECC71; color:#fff; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center;">
                            {{ strtoupper(substr(auth('web')->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth('web')->user()->last_name, 0, 1)) }}
                        </span>
                    @endif
                    <span style="font-size:13px; font-weight:600; color:#333; max-width:90px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ auth('web')->user()->first_name }}
                    </span>
                    <i class="fa fa-angle-down" style="font-size:11px; color:#888;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right"
                     style="border:1px solid #eee; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.1); padding:0; overflow:hidden; min-width:210px;">
                    <div class="dropdown-header" style="background:#f8f8f8; padding:12px 16px;">
                        <strong style="display:block; font-size:14px; color:#1a1a1a;">{{ auth('web')->user()->full_name }}</strong>
                        <small style="color:#888; font-size:12px;">{{ auth('web')->user()->email }}</small>
                    </div>
                    <div class="dropdown-divider" style="margin:4px 0;"></div>
                    <a class="dropdown-item" href="{{ route('buyer.dashboard') }}" style="font-size:13px; padding:9px 16px;">
                        <i class="fa fa-tachometer mr-2" style="width:16px; color:#888;"></i> Dashboard
                    </a>
                    <a class="dropdown-item" href="{{ route('buyer.orders') }}" style="font-size:13px; padding:9px 16px;">
                        <i class="fa fa-shopping-bag mr-2" style="width:16px; color:#888;"></i> My Orders
                    </a>
                    <a class="dropdown-item" href="{{ route('buyer.wallet') }}" style="font-size:13px; padding:9px 16px;">
                        <i class="fa fa-usd mr-2" style="width:16px; color:#888;"></i> Wallet
                    </a>
                    <a class="dropdown-item" href="{{ route('buyer.wishlist') }}" style="font-size:13px; padding:9px 16px;">
                        <i class="fa fa-heart mr-2" style="width:16px; color:#888;"></i> Wishlist
                    </a>
                    <div class="dropdown-divider" style="margin:4px 0;"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item"
                                style="font-size:13px; padding:9px 16px; color:#E74C3C; width:100%; text-align:left; background:none; border:none; cursor:pointer;">
                            <i class="fa fa-sign-out mr-2" style="width:16px;"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            {{-- Cart --}}
            <div class="cart-area">
                <a href="#" id="essenceCartBtn" title="Cart">
                    <img src="{{ asset('img/core-img/bag.svg') }}" alt="Cart">
                    <span id="cart-count">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                </a>
            </div>

        </div>
    </div>
</header>