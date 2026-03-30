<header class="nxl-header">
    <div class="header-wrapper">

        <div class="header-left d-flex align-items-center gap-4">
            <a href="javascript:void(0);"
               class="nxl-head-mobile-toggler" id="mobile-collapse">
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

        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">

                {{-- Wallet pill --}}
                <div class="nxl-h-item d-none d-md-flex me-2">
                    <a href="{{ route('buyer.wallet') }}"
                       class="btn btn-sm"
                       style="background:#D5F5E3;color:#1E8449;font-weight:700;border:none;">
                        <i class="feather-dollar-sign me-1" style="font-size:13px;"></i>
                        ₦{{ number_format(auth('web')->user()->wallet_balance, 2) }}
                    </a>
                </div>

                {{-- Search --}}
                <div class="dropdown nxl-h-item nxl-header-search">
                    <a href="javascript:void(0);"
                       class="nxl-head-link me-0"
                       data-bs-toggle="dropdown"
                       data-bs-auto-close="outside">
                        <i class="feather-search"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-search-dropdown">
                        <div class="input-group search-form">
                            <span class="input-group-text">
                                <i class="feather-search fs-6 text-muted"></i>
                            </span>
                            <form action="{{ route('search') }}" method="GET" class="d-flex flex-grow-1">
                                <input type="text"
                                       name="q"
                                       class="form-control search-input-field"
                                       placeholder="Search products..."
                                       value="{{ request('q') }}" />
                            </form>
                        </div>
                    </div>
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

                {{-- Wishlist --}}
                <div class="nxl-h-item">
                    <a href="{{ route('buyer.wishlist') }}" class="nxl-head-link me-0">
                        <i class="feather-heart"></i>
                    </a>
                </div>

                {{-- Notifications --}}
                <div class="dropdown nxl-h-item">
                    <a class="nxl-head-link me-3"
                       data-bs-toggle="dropdown"
                       href="#"
                       data-bs-auto-close="outside">
                        <i class="feather-bell"></i>
                        @php
                            $buyerNotifCount = \App\Models\Notification::where('notifiable_type', 'App\Models\User')
                                ->where('notifiable_id', auth('web')->id())
                                ->whereNull('read_at')
                                ->count();
                        @endphp
                        @if($buyerNotifCount > 0)
                            <span class="badge bg-danger nxl-h-badge">{{ $buyerNotifCount }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
                        <div class="d-flex justify-content-between align-items-center notifications-head">
                            <h6 class="fw-bold text-dark mb-0">Notifications</h6>
                        </div>
                        @php
                            $buyerNotifs = \App\Models\Notification::where('notifiable_type', 'App\Models\User')
                                ->where('notifiable_id', auth('web')->id())
                                ->whereNull('read_at')
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp
                        @forelse($buyerNotifs as $notif)
                        <div class="notifications-item">
                            <div class="avatar-text avatar-md rounded bg-primary text-white me-3 border">
                                <i class="feather-bell"></i>
                            </div>
                            <div class="notifications-desc">
                                <span class="font-body text-truncate-2-line">{{ $notif->body }}</span>
                                <div class="notifications-date text-muted">
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
                            <a href="{{ route('buyer.dashboard') }}" class="fs-13 fw-semibold text-dark">
                                View all
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Profile --}}
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);"
                       data-bs-toggle="dropdown"
                       role="button"
                       data-bs-auto-close="outside">
                        @if(auth('web')->user()->avatar)
                            <img src="{{ auth('web')->user()->avatar }}"
                                 alt="profile"
                                 class="img-fluid user-avtar me-0"
                                 style="border-radius:50%;width:36px;height:36px;object-fit:cover;" />
                        @else
                            <div class="img-fluid user-avtar me-0 d-flex align-items-center justify-content-center"
                                 style="width:36px;height:36px;border-radius:50%;background:#2ECC71;color:#fff;font-weight:700;font-size:14px;">
                                {{ strtoupper(substr(auth('web')->user()->first_name, 0, 1)) }}
                            </div>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                @if(auth('web')->user()->avatar)
                                    <img src="{{ auth('web')->user()->avatar }}"
                                         alt="profile" class="img-fluid user-avtar"
                                         style="border-radius:50%;object-fit:cover;" />
                                @else
                                    <div class="user-avtar d-flex align-items-center justify-content-center"
                                         style="width:40px;height:40px;border-radius:50%;background:#2ECC71;color:#fff;font-weight:700;font-size:16px;flex-shrink:0;">
                                        {{ strtoupper(substr(auth('web')->user()->first_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="ms-2">
                                    <h6 class="text-dark mb-0">{{ auth('web')->user()->full_name }}</h6>
                                    <span class="fs-12 fw-medium text-muted">{{ auth('web')->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('buyer.profile') }}" class="dropdown-item">
                                <i class="feather-user"></i>
                                <span>Profile Details</span>
                            </a>
                            <a href="{{ route('buyer.wallet') }}" class="dropdown-item">
                                <i class="feather-credit-card"></i>
                                <span>My Wallet</span>
                            </a>
                            <a href="{{ route('buyer.orders') }}" class="dropdown-item">
                                <i class="feather-shopping-bag"></i>
                                <span>My Orders</span>
                            </a>
                            <a href="{{ route('buyer.referral') }}" class="dropdown-item">
                                <i class="feather-gift"></i>
                                <span>Referrals</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
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