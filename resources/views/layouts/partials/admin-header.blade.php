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

                {{-- Role badge --}}
                <div class="nxl-h-item d-none d-md-flex me-3">
                    <span class="badge"
                          style="background:#D5F5E3;color:#1E8449;font-size:11px;font-weight:700;padding:5px 10px;">
                        {{ str_replace('_', ' ', strtoupper(auth('admin')->user()->role)) }}
                    </span>
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
                            <input type="text"
                                   class="form-control search-input-field"
                                   placeholder="Search users, orders..." />
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

                {{-- Notifications --}}
                <div class="dropdown nxl-h-item">
                    <a class="nxl-head-link me-3"
                       data-bs-toggle="dropdown"
                       href="#"
                       data-bs-auto-close="outside">
                        <i class="feather-bell"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
                        <div class="d-flex justify-content-between align-items-center notifications-head">
                            <h6 class="fw-bold text-dark mb-0">Admin Alerts</h6>
                        </div>
                        @php
                            $pendingSellers  = \App\Models\Seller::where('is_approved', false)->where('verification_status', 'pending')->count();
                            $pendingProducts = \App\Models\Product::where('status', 'pending')->count();
                            $pendingAds      = \App\Models\Ad::where('status', 'pending')->count();
                            $pendingWithdr   = \App\Models\WithdrawalRequest::where('status', 'pending')->count();
                        @endphp

                        @if($pendingSellers > 0)
                        <div class="notifications-item">
                            <div class="avatar-text avatar-md rounded bg-primary text-white me-3 border">
                                <i class="feather-users"></i>
                            </div>
                            <div class="notifications-desc">
                                <a href="{{ route('admin.sellers.pending') }}" class="font-body">
                                    {{ $pendingSellers }} seller(s) awaiting approval
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($pendingProducts > 0)
                        <div class="notifications-item">
                            <div class="avatar-text avatar-md rounded bg-primary text-white me-3 border">
                                <i class="feather-package"></i>
                            </div>
                            <div class="notifications-desc">
                                <a href="{{ route('admin.products.pending') }}" class="font-body">
                                    {{ $pendingProducts }} product(s) awaiting review
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($pendingWithdr > 0)
                        <div class="notifications-item">
                            <div class="avatar-text avatar-md rounded bg-primary text-white me-3 border">
                                <i class="feather-dollar-sign"></i>
                            </div>
                            <div class="notifications-desc">
                                <a href="{{ route('admin.withdrawals.index') }}" class="font-body">
                                    {{ $pendingWithdr }} withdrawal(s) pending
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($pendingSellers === 0 && $pendingProducts === 0 && $pendingWithdr === 0)
                        <div class="text-center py-4 text-muted">
                            <i class="feather-check-circle fs-3 mb-2 d-block"></i>
                            All clear!
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Profile --}}
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);"
                       data-bs-toggle="dropdown"
                       role="button"
                       data-bs-auto-close="outside">
                        <div class="img-fluid user-avtar me-0 d-flex align-items-center justify-content-center"
                             style="width:36px;height:36px;border-radius:50%;background:#2ECC71;color:#fff;font-weight:700;font-size:14px;">
                            {{ strtoupper(substr(auth('admin')->user()->first_name, 0, 1)) }}
                        </div>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <div class="user-avtar d-flex align-items-center justify-content-center"
                                     style="width:40px;height:40px;border-radius:50%;background:#2ECC71;color:#fff;font-weight:700;font-size:16px;flex-shrink:0;">
                                    {{ strtoupper(substr(auth('admin')->user()->first_name, 0, 1)) }}
                                </div>
                                <div class="ms-2">
                                    <h6 class="text-dark mb-0">{{ auth('admin')->user()->full_name }}</h6>
                                    <span class="fs-12 fw-medium text-muted">{{ auth('admin')->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('admin.logout') }}">
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