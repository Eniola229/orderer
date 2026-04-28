<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('marketer.dashboard') }}" class="b-brand">
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

                <li class="nxl-item {{ request()->routeIs('marketer.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('marketer.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Account</label>
                </li>

                <li class="nxl-item {{ request()->routeIs('marketer.profile') ? 'active' : '' }}">
                    <a href="{{ route('marketer.profile') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-user"></i></span>
                        <span class="nxl-mtext">My Profile</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Session</label>
                </li>

                <li class="nxl-item">
                    <form method="POST" action="{{ route('marketer.logout') }}">
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