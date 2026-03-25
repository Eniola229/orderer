<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="@yield('meta_description', 'Orderer - Buy, Sell and Deliver Anything')">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Orderer') — Orderer</title>

    <link rel="icon" href="{{ asset('img/core-img/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orderer.css') }}">

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