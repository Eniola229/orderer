{{-- resources/views/layouts/auth.blade.php --}}
@include('components.g-header')

{{-- Alerts outside auth-main so they don't break the flex row --}}
@include('layouts.partials.alerts')

<div class="auth-main">
    @yield('content')
</div>

<script src="{{ asset('dashboard/assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/common-init.min.js') }}"></script>
@stack('scripts')
</body>
</html>