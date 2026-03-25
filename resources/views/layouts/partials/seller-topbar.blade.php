<div class="seller-topbar">
    <h5 class="page-title">@yield('page_title', 'Dashboard')</h5>

    <div class="topbar-right">

        {{-- Wallet balance --}}
        <span class="wallet-pill">
            <i class="fa fa-dollar"></i>
            Wallet: ${{ number_format(auth('seller')->user()->wallet_balance, 2) }}
        </span>

        {{-- Ads balance --}}
        <span class="ads-pill">
            <i class="fa fa-bullhorn"></i>
            Ads: ${{ number_format(auth('seller')->user()->ads_balance, 2) }}
        </span>

        {{-- Notifications --}}
        <a href="#" class="notif-btn">
            <i class="fa fa-bell-o"></i>
            <span class="notif-dot"></span>
        </a>

        {{-- Profile dropdown --}}
        <div class="dropdown">
            <a href="#"
               id="sellerTopDropdown"
               data-toggle="dropdown"
               aria-haspopup="true"
               aria-expanded="false"
               style="display:flex;align-items:center;gap:8px;text-decoration:none;color:#333;">
                <div style="width:34px;height:34px;border-radius:50%;background:#2ECC71;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;overflow:hidden;">
                    @if(auth('seller')->user()->avatar)
                        <img src="{{ auth('seller')->user()->avatar }}"
                             style="width:100%;height:100%;object-fit:cover;" alt="">
                    @else
                        {{ strtoupper(substr(auth('seller')->user()->first_name, 0, 1)) }}
                    @endif
                </div>
                <span style="font-size:13px;font-weight:600;">
                    {{ auth('seller')->user()->first_name }}
                </span>
                <i class="fa fa-angle-down" style="font-size:12px;color:#aaa;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="{{ route('seller.profile') }}">
                    <i class="fa fa-user mr-2"></i> Profile
                </a>
                <a class="dropdown-item" href="{{ route('seller.brand.index') }}">
                    <i class="fa fa-star mr-2"></i> My Brand
                </a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('seller.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fa fa-sign-out mr-2"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>