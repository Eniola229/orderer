<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Orderer — Global E-commerce Marketplace</title>
    <link rel="icon" href="{{ asset('img/core-img/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orderer.css') }}">
</head>
<body>

{{-- Header --}}
@auth('web')
    @include('layouts.storefront.header-auth')
@else
    @include('layouts.storefront.header-guest')
@endauth

{{-- Cart sidebar --}}
@include('layouts.storefront.cart-sidebar')

{{-- Flash messages --}}
@include('layouts.partials.alerts')

{{-- Hero Banner Slider --}}
<section class="welcome_area bg-img background-overlay"
         style="background-image: url({{ $heroBanner ?? asset('img/bg-img/bg-1.jpg') }});">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12">
                <div class="hero-content">
                    <h6>New Arrivals</h6>
                    <h2>Shop Everything, <br>Delivered Everywhere</h2>
                    <a href="{{ route('shop.index') }}" class="btn essence-btn">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Top Categories --}}
<div class="top_catagory_area section-padding-80 clearfix">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading text-center">
                    <h2>Browse Categories</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            @foreach($categories->take(3) as $cat)
            <div class="col-12 col-sm-6 col-md-4">
                <a href="{{ route('shop.category', $cat->slug) }}" class="text-decoration-none">
                    <div class="single_catagory_area d-flex align-items-center justify-content-center bg-img"
                         style="{{ $cat->image ? 'background-image: url('.$cat->image.')' : 'background:#2ECC71' }}">
                        <div class="catagory-content">
                            <span style="color:#fff;font-weight:700;font-size:18px;">{{ $cat->name }}</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        {{-- All categories row --}}
        <div class="row mt-3 justify-content-center">
            @foreach($categories->skip(3)->take(6) as $cat)
            <div class="col-6 col-sm-4 col-md-2 mb-3 text-center">
                <a href="{{ route('shop.category', $cat->slug) }}"
                   class="d-flex flex-column align-items-center text-decoration-none"
                   style="padding:16px;border:1px solid #eee;border-radius:10px;transition:all .2s;"
                   onmouseover="this.style.borderColor='#2ECC71';this.style.background='#f0faf5';"
                   onmouseout="this.style.borderColor='#eee';this.style.background='#fff';">
                    @if($cat->icon)
                    <i class="{{ $cat->icon }}" style="font-size:24px;color:#2ECC71;margin-bottom:8px;"></i>
                    @else
                    <i class="fa fa-tag" style="font-size:24px;color:#2ECC71;margin-bottom:8px;"></i>
                    @endif
                    <span style="font-size:13px;font-weight:600;color:#333;">{{ $cat->name }}</span>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Flash Sales (if any) --}}
@if($flashSales->count())
<div class="cta-area" style="background:#fff7f0;padding:40px 0;">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 style="font-weight:800;color:#1a1a1a;">
                    <i class="fa fa-bolt" style="color:#FF6B00;"></i> Flash Sales
                </h2>
            </div>
            <div class="col-auto">
                <span id="flashTimer" style="font-size:22px;font-weight:800;color:#FF6B00;"></span>
            </div>
        </div>
        <div class="row">
            @foreach($flashSales->take(4) as $flash)
            @php
                $img = $flash->product->images->where('is_primary',true)->first() ?? $flash->product->images->first();
            @endphp
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <div class="single-product-wrapper">
                    <div class="product-img">
                        <a href="{{ route('product.show', $flash->product->slug) }}">
                            <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                        </a>
                        <div class="product-badge offer-badge">
                            <span>-{{ round((($flash->original_price - $flash->sale_price) / $flash->original_price) * 100) }}%</span>
                        </div>
                        <div class="product-favourite">
                            <a href="#" class="favme fa fa-heart" data-product="{{ $flash->product_id }}"></a>
                        </div>
                    </div>
                    <div class="product-description">
                        <span>{{ $flash->product->seller->business_name ?? '' }}</span>
                        <a href="{{ route('product.show', $flash->product->slug) }}">
                            <h6>{{ Str::limit($flash->product->name, 40) }}</h6>
                        </a>
                        <p class="product-price">
                            <span class="old-price">${{ number_format($flash->original_price, 2) }}</span>
                            ${{ number_format($flash->sale_price, 2) }}
                        </p>
                        <div class="hover-content">
                            <div class="add-to-cart-btn">
                                <a href="{{ route('product.show', $flash->product->slug) }}"
                                   class="btn essence-btn">View Deal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Featured / Promoted Products --}}
<section class="new_arrivals_area section-padding-80 clearfix">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading text-center">
                    <h2>Featured Products</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="popular-products-slides owl-carousel">
                    @foreach($featuredProducts as $product)
                    @php $img = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
                    <div class="single-product-wrapper">
                        <div class="product-img">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                            </a>
                            @if($product->sale_price)
                            <div class="product-badge offer-badge">
                                <span>SALE</span>
                            </div>
                            @endif
                            <div class="product-favourite">
                                <a href="#" class="favme fa fa-heart" data-product="{{ $product->id }}"></a>
                            </div>
                        </div>
                        <div class="product-description">
                            <span>{{ $product->seller->business_name ?? '' }}</span>
                            <a href="{{ route('product.show', $product->slug) }}">
                                <h6>{{ Str::limit($product->name, 35) }}</h6>
                            </a>
                            <p class="product-price">
                                @if($product->sale_price)
                                    <span class="old-price">${{ number_format($product->price, 2) }}</span>
                                    ${{ number_format($product->sale_price, 2) }}
                                @else
                                    ${{ number_format($product->price, 2) }}
                                @endif
                            </p>
                            <div class="hover-content">
                                <div class="add-to-cart-btn">
                                    <a href="#" class="btn essence-btn add-to-cart"
                                       data-product="{{ $product->id }}">Add to Cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Brands strip --}}
@if($brands->count())
<div class="brands-area d-flex align-items-center justify-content-between">
    @foreach($brands->take(6) as $brand)
    <div class="single-brands-logo">
        <a href="{{ route('brands.show', $brand->slug) }}">
            @if($brand->logo)
                <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" style="height:40px;object-fit:contain;">
            @else
                <span style="font-weight:700;color:#555;font-size:16px;">{{ $brand->name }}</span>
            @endif
        </a>
    </div>
    @endforeach
</div>
@endif

{{-- New Arrivals --}}
<section class="new_arrivals_area section-padding-80 clearfix" style="background:#f8f8f8;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading text-center">
                    <h2>New Arrivals</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            @foreach($newArrivals as $product)
            @php $img = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <div class="single-product-wrapper">
                    <div class="product-img">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                        </a>
                        <div class="product-badge new-badge"><span>New</span></div>
                        <div class="product-favourite">
                            <a href="#" class="favme fa fa-heart" data-product="{{ $product->id }}"></a>
                        </div>
                    </div>
                    <div class="product-description">
                        <span>{{ $product->seller->business_name ?? '' }}</span>
                        <a href="{{ route('product.show', $product->slug) }}">
                            <h6>{{ Str::limit($product->name, 35) }}</h6>
                        </a>
                        <p class="product-price">${{ number_format($product->price, 2) }}</p>
                        <div class="hover-content">
                            <div class="add-to-cart-btn">
                                <a href="#" class="btn essence-btn add-to-cart"
                                   data-product="{{ $product->id }}">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-12 text-center mt-3">
                <a href="{{ route('shop.index') }}" class="btn essence-btn">View All Products</a>
            </div>
        </div>
    </div>
</section>

{{-- Book a Rider CTA --}}
<div class="cta-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="cta-content bg-img background-overlay"
                     style="background-image: url({{ asset('img/bg-img/bg-5.jpg') }});">
                    <div class="h-100 d-flex align-items-center justify-content-end">
                        <div class="cta--text">
                            <h6>Fast Delivery</h6>
                            <h2>Book a Rider</h2>
                            <p style="color:#fff;opacity:.85;margin-bottom:16px;">
                                Local or international — we deliver anywhere
                            </p>
                            <a href="{{ route('rider.booking') }}" class="btn essence-btn">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Footer --}}
@include('layouts.storefront.footer')

<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>

<script>
// Flash sale countdown
@if($flashSales->count() && $flashSales->first()->ends_at)
(function() {
    const end = new Date('{{ $flashSales->first()->ends_at->toIso8601String() }}');
    function tick() {
        const now  = new Date();
        const diff = end - now;
        if (diff <= 0) { document.getElementById('flashTimer').textContent = 'Ended'; return; }
        const h = Math.floor(diff/3600000);
        const m = Math.floor((diff%3600000)/60000);
        const s = Math.floor((diff%60000)/1000);
        document.getElementById('flashTimer').textContent =
            String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
    }
    tick();
    setInterval(tick, 1000);
})();
@endif

// Add to cart via AJAX
document.querySelectorAll('.add-to-cart').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.dataset.product;
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('#cart-count, #cart-count-sidebar').forEach(el => {
                    el.textContent = data.count;
                });
            }
        });
    });
});

// Wishlist heart toggle
document.querySelectorAll('.favme').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        @auth('web')
        const productId = this.dataset.product;
        this.classList.toggle('active');
        fetch('{{ route("buyer.wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ product_id: productId })
        });
        @else
        window.location.href = '{{ route("login") }}';
        @endauth
    });
});
</script>

</body>
</html>
