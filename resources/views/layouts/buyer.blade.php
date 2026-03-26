@include('components.g-header')

@include('layouts.partials.buyer-nav')
@include('layouts.partials.buyer-header')

<main class="nxl-container">
    <div class="nxl-content">

        {{-- Page Header --}}
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">@yield('page_title', 'Dashboard')</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('buyer.dashboard') }}">Account</a>
                    </li>
                    @yield('breadcrumb')
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                @yield('page_actions')
            </div>
        </div>

        {{-- Alerts --}}
        @include('layouts.partials.alerts')

        {{-- Main content --}}
        <div class="main-content">
            @yield('content')
        </div>

    </div>
</main>

@include('layouts.partials.buyer-footer')
