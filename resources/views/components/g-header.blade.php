<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/theme.min.css') }}" />

    {{-- Orderer green brand override --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/orderer-theme.css') }}" />
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- X (Twitter) Pixel Base Code -->
    <script>
    !function(e,t,n,s,u,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);
    },s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='https://static.ads-twitter.com/uwt.js',
    a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))}(window,document,'script');
    twq('config','rc8yq'); 
    </script>
    <!-- End X Pixel Base Code -->
    @stack('styles')
</head>
<body>