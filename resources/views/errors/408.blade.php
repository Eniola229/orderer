@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>408 - Request Timeout</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center" style="padding: 60px 30px; border: 1px solid #eee; border-radius: 12px;">
                    <div class="mb-4">
                        <i class="fa fa-hourglass" style="font-size: 80px; color: #2ECC71;"></i>
                    </div>
                    <h1 style="font-size: 120px; font-weight: 800; margin-bottom: 20px; color: #2ECC71;">408</h1>
                    <h3 style="font-weight: 600; margin-bottom: 20px;">Request Timeout</h3>
                    <p style="color: #888; margin-bottom: 30px;">The server timed out waiting for the request. Please check your connection and try again.</p>
                    <a href="{{ url('/') }}" class="btn essence-btn" >
                        <i class="fa fa-refresh mr-2"></i> Try Again
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>