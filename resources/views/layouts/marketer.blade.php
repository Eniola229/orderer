<!DOCTYPE html>
<html lang="en">
<head>
    @include('components.g-header')
</head>
<body class="nxl-navbar-active">

    @include('layouts.partials.marketer-nav')
    @include('layouts.partials.marketer-header')

    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">@yield('page_title', 'Dashboard')</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('marketer.dashboard') }}">Marketer</a>
                        </li>
                        @yield('breadcrumb')
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    @yield('page_actions')
                </div>
            </div>

            @include('layouts.partials.alerts')

            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </main>

<script src="{{ asset('dashboard/assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/apexcharts.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/common-init.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/dashboard-init.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/theme-customizer-init.min.js') }}"></script>
</body>
</html>