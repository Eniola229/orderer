@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>503 - Service Unavailable</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center" style="padding: 60px 30px; border: 1px solid #eee; border-radius: 12px;">
                    <div class="mb-4">
                        <i class="fa fa-wrench" style="font-size: 80px; color: #2ECC71;"></i>
                    </div>
                    <h1 style="font-size: 120px; font-weight: 800; margin-bottom: 20px; color: #2ECC71;">503</h1>
                    <h3 style="font-weight: 600; margin-bottom: 20px;">Service Unavailable</h3>
                    <p style="color: #888; margin-bottom: 30px;">Sorry, we are currently performing maintenance. Please check back soon.</p>
                    <a href="{{ url('/') }}" class="btn essence-btn">
                        <i class="fa fa-refresh mr-2"></i> Home Page
                    </a>
                    <a href="javascript:history.back()" class="btn essence-btn ml-2" style="background:transparent; border:2px solid #2ECC71; color:#2ECC71;">
                        <i class="fa fa-arrow-left mr-2"></i> Go Back
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