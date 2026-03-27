<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Orderer — Buy, sell and deliver anything, anywhere in the world." />
    <meta name="keywords" content="ecommerce Nigeria, buy online, sell online, orderer, marketplace, delivery" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Orderer || @yield('title', 'Global E-commerce Marketplace')</title>

    <meta property="og:title" content="Orderer – Global E-commerce Marketplace" />
    <meta property="og:description" content="Buy, sell and deliver anything anywhere in the world." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ asset('dashboard/assets/images/favicon.png') }}" />
    <meta property="og:site_name" content="Orderer" />

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('dashboard/assets/images/favicon.png') }}" />


    <link rel="icon" href="{{ asset('img/core-img/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/orderer-theme.css') }}" />

    @stack('styles')
</head>

<body>

    {{-- Header: guest or auth --}}
    @auth('web')
        @include('layouts.partials.header-auth')
    @else
        @include('layouts.partials.header-guest')
    @endauth

    {{-- Right side cart --}}
    @include('layouts.partials.cart-sidebar')

    {{-- Flash messages --}}
    @include('layouts.partials.alerts')

    {{-- Page content --}}
    @yield('content')

    {{-- Footer --}}
    @include('layouts.partials.footer')

    {{-- Core JS --}}
    <script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins.js') }}"></script>
    <script src="{{ asset('js/classy-nav.min.js') }}"></script>
    <script src="{{ asset('js/active.js') }}"></script>

    @stack('scripts')

</body>
</html>