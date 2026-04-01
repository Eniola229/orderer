<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('home') }}" class="b-brand">
                <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                     alt="Orderer"
                     class="logo logo-lg"
                     style="width:140px;height:auto;display:block;margin:0 auto;" />
                <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                     alt="" class="logo logo-sm" />
            </a>
        </div>

        <div class="navbar-content">
            <ul class="nxl-navbar">

                <li class="nxl-item nxl-caption">
                    <label>Menu</label>
                </li>

                <li class="nxl-item {{ request()->routeIs('buyer.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('buyer.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('buyer.orders*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-shopping-bag"></i></span>
                        <span class="nxl-mtext">Orders</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('buyer.orders') }}">Order History</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('shop.index') }}">Browse Shop</a>
                        </li>
                    </ul>
                </li>
                
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('buyer.bookings*') || request()->routeIs('rider.booking*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-truck"></i></span>
                        <span class="nxl-mtext">Deliveries</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item {{ request()->routeIs('buyer.bookings*') ? 'active' : '' }}">
                            <a class="nxl-link" href="{{ route('buyer.bookings') }}">
                                My Deliveries
                            </a>
                        </li>
                        <li class="nxl-item {{ request()->routeIs('rider.booking*') ? 'active' : '' }}">
                            <a class="nxl-link" href="{{ route('rider.booking') }}">
                                Book a Delivery
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('buyer.wallet*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                        <span class="nxl-mtext">Wallet</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('buyer.wallet') }}">My Wallet</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item {{ request()->routeIs('buyer.wishlist*') ? 'active' : '' }}">
                    <a href="{{ route('buyer.wishlist') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-heart"></i></span>
                        <span class="nxl-mtext">Wishlist</span>
                    </a>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('buyer.referral*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-gift"></i></span>
                        <span class="nxl-mtext">Referral</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('buyer.referral') }}">Referral Dashboard</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('buyer.support*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-life-buoy"></i></span>
                        <span class="nxl-mtext">Support</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('buyer.support') }}">My Tickets</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Account</label>
                </li>

                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('buyer.profile*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-settings"></i></span>
                        <span class="nxl-mtext">Settings</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('buyer.profile') }}">Profile</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('buyer.profile') }}">Change Password</a>
                        </li>
                    </ul>
                </li>

                <li class="nxl-item">
                    <form method="POST" action="{{ route('logout') }}">
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