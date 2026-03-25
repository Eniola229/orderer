<header class="header_area">
    <div class="classy-nav-container breakpoint-off d-flex align-items-center justify-content-between">

        <nav class="classy-navbar" id="ordererNav">

            <a class="nav-brand" href="{{ route('home') }}">
                <img src="{{ asset('img/core-img/logo.png') }}" alt="Orderer">
            </a>

            <div class="classy-navbar-toggler">
                <span class="navbarToggler">
                    <span></span><span></span><span></span>
                </span>
            </div>

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

        <div class="header-meta d-flex clearfix justify-content-end align-items-center">

            {{-- Search --}}
            <div class="search-area">
                <form action="{{ route('search') }}" method="GET">
                    <input type="search"
                           name="q"
                           placeholder="Search products..."
                           value="{{ request('q') }}">
                    <button type="submit">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                </form>
            </div>

            {{-- Wishlist --}}
            <div class="favourite-area">
                <a href="{{ route('buyer.wishlist') }}">
                    <img src="{{ asset('img/core-img/heart.svg') }}" alt="Wishlist">
                </a>
            </div>

            {{-- Wallet balance --}}
            <div class="mx-3">
                <span class="wallet-badge">
                    ${{ number_format(auth('web')->user()->wallet_balance, 2) }}
                </span>
            </div>

            {{-- User dropdown --}}
            <div class="user-login-info dropdown">
                <a href="#"
                   id="userDropdown"
                   data-toggle="dropdown"
                   aria-haspopup="true"
                   aria-expanded="false">
                    @if(auth('web')->user()->avatar)
                        <img src="{{ auth('web')->user()->avatar }}"
                             alt="{{ auth('web')->user()->first_name }}"
                             style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                    @else
                        <img src="{{ asset('img/core-img/user.svg') }}" alt="Account">
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <div class="dropdown-header">
                        <strong>{{ auth('web')->user()->full_name }}</strong>
                        <br>
                        <small class="text-muted">{{ auth('web')->user()->email }}</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('buyer.dashboard') }}">
                        <i class="fa fa-tachometer mr-2"></i> My Dashboard
                    </a>
                    <a class="dropdown-item" href="{{ route('buyer.orders') }}">
                        <i class="fa fa-shopping-bag mr-2"></i> My Orders
                    </a>
                    <a class="dropdown-item" href="{{ route('buyer.wishlist') }}">
                        <i class="fa fa-heart mr-2"></i> Wishlist
                    </a>
                    <a class="dropdown-item" href="{{ route('buyer.wallet') }}">
                        <i class="fa fa-dollar mr-2"></i> Wallet
                    </a>
                    <a class="dropdown-item" href="{{ route('buyer.referral') }}">
                        <i class="fa fa-users mr-2"></i> Referrals
                    </a>
                    <a class="dropdown-item" href="{{ route('buyer.support') }}">
                        <i class="fa fa-life-ring mr-2"></i> Support
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('buyer.profile') }}">
                        <i class="fa fa-cog mr-2"></i> Settings
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fa fa-sign-out mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            {{-- Cart --}}
            <div class="cart-area">
                <a href="#" id="essenceCartBtn">
                    <img src="{{ asset('img/core-img/bag.svg') }}" alt="Cart">
                    <span id="cart-count">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                </a>
            </div>

        </div>
    </div>
</header>