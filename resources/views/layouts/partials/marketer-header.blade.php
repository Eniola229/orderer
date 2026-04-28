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
                        MARKETER
                    </span>
                </div>

                {{-- Marketing code chip --}}
                @if(auth('marketer')->user()->marketing_code)
                <div class="nxl-h-item d-none d-lg-flex me-3">
                    <span class="text-muted fs-12 me-2">Your code:</span>
                    <code style="background:#1a1f2e;color:#2ECC71;font-size:11px;font-weight:700;
                                 letter-spacing:1.5px;padding:4px 10px;border-radius:5px;cursor:pointer;"
                          onclick="navigator.clipboard.writeText('{{ auth('marketer')->user()->marketing_code }}')"
                          title="Click to copy">
                        {{ auth('marketer')->user()->marketing_code }}
                    </code>
                </div>
                @endif

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

                {{-- Profile dropdown --}}
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);"
                       data-bs-toggle="dropdown"
                       role="button"
                       data-bs-auto-close="outside">
                        <div class="img-fluid user-avtar me-0 d-flex align-items-center justify-content-center"
                             style="width:36px;height:36px;border-radius:50%;background:#2ECC71;color:#fff;font-weight:700;font-size:14px;">
                            {{ strtoupper(substr(auth('marketer')->user()->first_name, 0, 1)) }}
                        </div>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <div class="user-avtar d-flex align-items-center justify-content-center"
                                     style="width:40px;height:40px;border-radius:50%;background:#2ECC71;color:#fff;font-weight:700;font-size:16px;flex-shrink:0;">
                                    {{ strtoupper(substr(auth('marketer')->user()->first_name, 0, 1)) }}
                                </div>
                                <div class="ms-2">
                                    <h6 class="text-dark mb-0">{{ auth('marketer')->user()->full_name }}</h6>
                                    <span class="fs-12 fw-medium text-muted">{{ auth('marketer')->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a class="dropdown-item" href="{{ route('marketer.profile') }}">
                                <i class="feather-user"></i>
                                <span>My Profile</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('marketer.logout') }}">
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