<header class="nxl-header">
    <div class="header-wrapper">

        {{-- Left: toggles --}}
        <div class="header-left d-flex align-items-center gap-4">
            <a href="javascript:void(0);"
               class="nxl-head-mobile-toggler"
               id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display:none;">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Right: actions --}}
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">

                {{-- Wallet pill --}}
                <div class="nxl-h-item d-none d-md-flex me-2">
                    <a href="{{ route('seller.wallet.index') }}"
                       class="btn btn-sm"
                       style="background:#D5F5E3;color:#1E8449;font-weight:700;border:none;">
                        <i class="feather-dollar-sign me-1" style="font-size:13px;"></i>
                        ${{ number_format(auth('seller')->user()->wallet_balance, 2) }}
                    </a>
                </div>

                {{-- Ads balance pill --}}
                <div class="nxl-h-item d-none d-md-flex me-2">
                    <a href="{{ route('seller.ads.index') }}"
                       class="btn btn-sm"
                       style="background:#FEF9E7;color:#B7950B;font-weight:700;border:none;">
                        <i class="feather-trending-up me-1" style="font-size:13px;"></i>
                        Ads: ${{ number_format(auth('seller')->user()->ads_balance, 2) }}
                    </a>
                </div>

                {{-- Fullscreen --}}
                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);"
                           class="nxl-head-link me-0"
                           onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>

                {{-- Dark/light --}}
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display:none;">
                        <i class="feather-sun"></i>
                    </a>
                </div>

                {{-- Notifications --}}
                <div class="dropdown nxl-h-item">
                    <a class="nxl-head-link me-3"
                       data-bs-toggle="dropdown"
                       href="#"
                       role="button"
                       data-bs-auto-close="outside">
                        <i class="feather-bell"></i>
                        @php
                            $sellerNotifCount = \App\Models\Notification::where('notifiable_type', 'App\Models\Seller')
                                ->where('notifiable_id', auth('seller')->id())
                                ->whereNull('read_at')
                                ->count();
                        @endphp
                        @if($sellerNotifCount > 0)
                            <span class="badge bg-danger nxl-h-badge">
                                {{ $sellerNotifCount }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
                        <div class="d-flex justify-content-between align-items-center notifications-head">
                            <h6 class="fw-bold text-dark mb-0">Notifications</h6>
                            @if($sellerNotifCount > 0)
                            <a href="{{ route('seller.notifications.read') }}"
                               class="fs-12 text-muted">Mark all read</a>
                            @endif
                        </div>

                        @php
                            $sellerNotifs = \App\Models\Notification::where('notifiable_type', 'App\Models\Seller')
                                ->where('notifiable_id', auth('seller')->id())
                                ->whereNull('read_at')
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp

                        @forelse($sellerNotifs as $notif)
                        <div class="notifications-item">
                            <div class="avatar-text avatar-md rounded bg-primary text-white me-3 border">
                                <i class="feather-bell"></i>
                            </div>
                            <div class="notifications-desc">
                                <span class="font-body text-truncate-2-line">{{ $notif->body }}</span>
                                <div class="notifications-date text-muted border-bottom border-bottom-dashed">
                                    {{ $notif->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="feather-check-circle fs-3 mb-2 d-block"></i>
                            No new notifications
                        </div>
                        @endforelse

                        <div class="text-center notifications-footer">
                            <a href="{{ route('seller.dashboard') }}"
                               class="fs-13 fw-semibold text-dark">
                                View all
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Profile dropdown --}}
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);"
                       data-bs-toggle="dropdown"
                       role="button"
                       data-bs-auto-close="outside">
                        @if(auth('seller')->user()->avatar)
                            <img src="{{ auth('seller')->user()->avatar }}"
                                 alt="profile"
                                 class="img-fluid user-avtar me-0"
                                 style="border-radius:50%;width:36px;height:36px;object-fit:cover;" />
                        @else
                            <div class="img-fluid user-avtar me-0 d-flex align-items-center justify-content-center"
                                 style="width:36px;height:36px;border-radius:50%;background:#2ECC71;color:#fff;font-weight:700;font-size:14px;">
                                {{ strtoupper(substr(auth('seller')->user()->first_name, 0, 1)) }}
                            </div>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                @if(auth('seller')->user()->avatar)
                                    <img src="{{ auth('seller')->user()->avatar }}"
                                         alt="profile"
                                         class="img-fluid user-avtar"
                                         style="border-radius:50%;object-fit:cover;" />
                                @else
                                    <div class="user-avtar d-flex align-items-center justify-content-center"
                                         style="width:40px;height:40px;border-radius:50%;background:#2ECC71;color:#fff;font-weight:700;font-size:16px;flex-shrink:0;">
                                        {{ strtoupper(substr(auth('seller')->user()->first_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="ms-2">
                                    <h6 class="text-dark mb-0">
                                        {{ auth('seller')->user()->business_name }}
                                    </h6>
                                    <span class="fs-12 fw-medium text-muted">
                                        {{ auth('seller')->user()->email }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('seller.profile') }}" class="dropdown-item">
                                <i class="feather-user"></i>
                                <span>Profile Settings</span>
                            </a>
                            <a href="{{ route('seller.wallet.index') }}" class="dropdown-item">
                                <i class="feather-credit-card"></i>
                                <span>Wallet & Earnings</span>
                            </a>
                            <a href="{{ route('seller.brand.index') }}" class="dropdown-item">
                                <i class="feather-star"></i>
                                <span>My Brand</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('seller.logout') }}">
                                @csrf
                                <button type="submit"
                                        class="dropdown-item border-0 w-100 text-start text-danger">
                                    <i class="feather-log-out"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</header>