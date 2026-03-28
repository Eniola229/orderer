<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand">
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
                    <label>Main</label>
                </li>

                <li class="nxl-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Management</label>
                </li>

                {{-- Sellers --}}
                @if(auth('admin')->user()->canModerateSellers())
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.sellers.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-users"></i></span>
                        <span class="nxl-mtext">Sellers</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.sellers.index') }}">All Sellers</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.sellers.pending') }}">Pending Approval</a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- Buyers --}}
                @if(auth('admin')->user()->canModerateBuyer())
                <li class="nxl-item {{ request()->routeIs('admin.buyers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.buyers.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-user-check"></i></span>
                        <span class="nxl-mtext">Buyers</span>
                    </a>
                </li>
                @endif

                {{-- Products --}}
                @if(auth('admin')->user()->canModerateSellers())
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-package"></i></span>
                        <span class="nxl-mtext">Products</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.products.index') }}">All Products</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.products.pending') }}">Pending Review</a>
                        </li>
                    </ul>
                </li>

            <li class="nxl-item">
                    <a href="{{ route('admin.services.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-package"></i></span>
                        <span class="nxl-mtext">Services</span>
                    </a>
                </li>
            <li class="nxl-item">
                    <a href="{{ route('admin.houses.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-home"></i></span>
                        <span class="nxl-mtext">Properties</span>
                    </a>
                </li>
                @endif

                {{-- Orders --}}
                @if(auth('admin')->user()->canEditOrders())
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-shopping-bag"></i></span>
                        <span class="nxl-mtext">Orders</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.orders.index') }}">All Orders</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.orders.disputes') }}">Disputes</a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- Finance --}}
                @if(auth('admin')->user()->canManageFinance())
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.finance.*') || request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                        <span class="nxl-mtext">Finance</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.withdrawals.index') }}">Withdrawals</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.finance.transactions') }}">Transactions</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.finance.escrow') }}">Escrow</a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- Ads --}}
                @if(auth('admin')->user()->canManageAds())
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-trending-up"></i></span>
                        <span class="nxl-mtext">Ads</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.ads.index') }}">All Ads</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.ads.pending') }}">Pending Approval</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.ads.categories') }}">Ad Categories</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.ads.slots') }}">Banner Slots</a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- Categories --}}
                @if(auth('admin')->user()->canManageCategories())
                <li class="nxl-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-grid"></i></span>
                        <span class="nxl-mtext">Categories</span>
                    </a>
                </li>
                @endif

                {{-- Brands --}}
                @if(auth('admin')->user()->canManageCategories())
                <li class="nxl-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.brands.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-award"></i></span>
                        <span class="nxl-mtext">Brands</span>
                    </a>
                </li>
                @endif

                {{-- Support --}}
                @if(auth('admin')->user()->canHandleSupport())
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-life-buoy"></i></span>
                        <span class="nxl-mtext">Support</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.support.index') }}">All Tickets</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.support.open') }}">Open Tickets</a>
                        </li>
                    </ul>
                </li>
                @endif

                <li class="nxl-item nxl-caption">
                    <label>Administration</label>
                </li>

                {{-- Admins (Super Admin or HR) --}}
                @if(auth('admin')->user()->canManageAdmins())
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-shield"></i></span>
                        <span class="nxl-mtext">Admins</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.admins.index') }}">All Admins</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('admin.admins.create') }}">Add Admin</a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- Logs (Super Admin only) --}}
                @if(auth('admin')->user()->canViewLogs())
                <li class="nxl-item {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.logs.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-activity"></i></span>
                        <span class="nxl-mtext">Activity Logs</span>
                    </a>
                </li>
                @endif

                <li class="nxl-item nxl-caption">
                    <label>Profile</label>
                </li>

                <li class="nxl-item">
                    <a href="{{ route('admin.profile.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-user"></i></span>
                        <span class="nxl-mtext">My Profile</span>
                    </a>
                </li>
                <li class="nxl-item">
                    <form method="POST" action="{{ route('admin.logout') }}">
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