<aside class="seller-sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <a href="{{ route('home') }}">
            <img src="{{ asset('img/core-img/logo.png') }}" alt="Orderer">
        </a>
    </div>

    {{-- Seller info --}}
    <div class="sidebar-seller-info">
        <div class="seller-avatar">
            @if(auth('seller')->user()->avatar)
                <img src="{{ auth('seller')->user()->avatar }}" alt="">
            @else
                {{ strtoupper(substr(auth('seller')->user()->first_name, 0, 1)) }}
            @endif
        </div>
        <div>
            <p class="seller-name">{{ auth('seller')->user()->business_name }}</p>
            <p class="seller-status">
                @if(auth('seller')->user()->is_verified_business)
                    &#10003; Verified Seller
                @else
                    Individual Seller
                @endif
            </p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        <p class="nav-section-title">Main</p>

        <a href="{{ route('seller.dashboard') }}"
           class="{{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
            <i class="fa fa-tachometer"></i>
            <span>Dashboard</span>
        </a>

        <p class="nav-section-title">Listings</p>

        <a href="{{ route('seller.products.index') }}"
           class="{{ request()->routeIs('seller.products.*') ? 'active' : '' }}">
            <i class="fa fa-th-large"></i>
            <span>Products</span>
        </a>

        <a href="{{ route('seller.services.index') }}"
           class="{{ request()->routeIs('seller.services.*') ? 'active' : '' }}">
            <i class="fa fa-cogs"></i>
            <span>Services</span>
        </a>

        <a href="{{ route('seller.houses.index') }}"
           class="{{ request()->routeIs('seller.houses.*') ? 'active' : '' }}">
            <i class="fa fa-home"></i>
            <span>Properties</span>
        </a>

        <p class="nav-section-title">Business</p>

        <a href="{{ route('seller.orders.index') }}"
           class="{{ request()->routeIs('seller.orders.*') ? 'active' : '' }}">
            <i class="fa fa-shopping-bag"></i>
            <span>Orders</span>
            {{-- Show count of pending orders --}}
        </a>

        <a href="{{ route('seller.wallet.index') }}"
           class="{{ request()->routeIs('seller.wallet.*') ? 'active' : '' }}">
            <i class="fa fa-dollar"></i>
            <span>Wallet</span>
        </a>

        <a href="{{ route('seller.ads.index') }}"
           class="{{ request()->routeIs('seller.ads.*') ? 'active' : '' }}">
            <i class="fa fa-bullhorn"></i>
            <span>Ads</span>
        </a>

        <a href="{{ route('seller.withdrawals.index') }}"
           class="{{ request()->routeIs('seller.withdrawals.*') ? 'active' : '' }}">
            <i class="fa fa-money"></i>
            <span>Withdrawals</span>
        </a>

        <p class="nav-section-title">Account</p>

        <a href="{{ route('seller.brand.index') }}"
           class="{{ request()->routeIs('seller.brand.*') ? 'active' : '' }}">
            <i class="fa fa-star"></i>
            <span>My Brand</span>
        </a>

        <a href="{{ route('seller.profile') }}"
           class="{{ request()->routeIs('seller.profile') ? 'active' : '' }}">
            <i class="fa fa-user"></i>
            <span>Profile</span>
        </a>

        <a href="{{ route('seller.support') }}"
           class="{{ request()->routeIs('seller.support*') ? 'active' : '' }}">
            <i class="fa fa-life-ring"></i>
            <span>Support</span>
        </a>

    </nav>

    {{-- Bottom --}}
    <div class="sidebar-bottom">
        <a href="{{ route('home') }}" target="_blank">
            <i class="fa fa-external-link"></i>
            <span>View Store</span>
        </a>
        <form action="{{ route('seller.logout') }}" method="POST">
            @csrf
            <button type="submit"
                    style="background:none;border:none;padding:8px 0;cursor:pointer;display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.5);font-size:13px;width:100%;">
                <i class="fa fa-sign-out"></i>
                <span>Sign Out</span>
            </button>
        </form>
    </div>

</aside>