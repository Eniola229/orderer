<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('home') }}" class="b-brand">
                <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                     alt="Orderer"
                     class="logo logo-lg"
                     style="width:140px;height:auto;display:block;margin:0 auto;" />
                <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                     alt="Orderer"
                     class="logo logo-sm" />
            </a>
        </div>

        <div class="navbar-content">
            <ul class="nxl-navbar">

                <li class="nxl-item nxl-caption">
                    <label>Main</label>
                </li>

                <li class="nxl-item {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('seller.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Listings</label>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('seller.products.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-package"></i></span>
                        <span class="nxl-mtext">Products</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.products.index') }}">All Products</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.products.create') }}">Add Product</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('seller.services.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-tool"></i></span>
                        <span class="nxl-mtext">Services</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.services.index') }}">All Services</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.services.create') }}">Add Service</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('seller.houses.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-home"></i></span>
                        <span class="nxl-mtext">Properties</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.houses.index') }}">All Properties</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.houses.create') }}">Add Property</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Business</label>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-shopping-bag"></i></span>
                        <span class="nxl-mtext">Orders</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.orders.index') }}">All Orders</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.orders.index', ['status' => 'pending']) }}">Pending</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('seller.wallet.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                        <span class="nxl-mtext">Wallet</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.wallet.index') }}">My Wallet</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.withdrawals.index') }}">All Withdrawals</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.withdrawals.create') }}">Withdraw</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item {{ request()->routeIs('seller.ads.*') ? 'active' : '' }}">
                    <a href="{{ route('seller.ads.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-trending-up"></i></span>
                        <span class="nxl-mtext">Promotions</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Account</label>
                </li>

                <li class="nxl-item {{ request()->routeIs('seller.brand.*') ? 'active' : '' }}">
                    <a href="{{ route('seller.brand.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-star"></i></span>
                        <span class="nxl-mtext">My Brand</span>
                    </a>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('seller.profile') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-settings"></i></span>
                        <span class="nxl-mtext">Settings</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.profile') }}">Profile</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('seller.profile') }}">Change Password</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item">
                    <a href="{{ route('seller.support') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-life-buoy"></i></span>
                        <span class="nxl-mtext">Support</span>
                    </a>
                </li>

                <li class="nxl-item">
                    <form method="POST" action="{{ route('seller.logout') }}">
                        @csrf
                        <button type="submit"
                                class="nxl-link border-0 bg-transparent w-100 text-start">
                            <span class="nxl-micon">
                                <i class="feather-power text-danger"></i>
                            </span>
                            <span class="nxl-mtext text-danger">Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </div>
</nav>