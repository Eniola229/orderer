@include('components.g-header')

<div class="auth-main">
    @include('layouts.partials.alerts')
    @yield('content')
</div>

<script src="{{ asset('dashboard/assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/common-init.min.js') }}"></script>
@stack('scripts')
</body>
</html>