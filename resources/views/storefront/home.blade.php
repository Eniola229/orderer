{{-- ============================================================
     storefront/home.blade.php
       1. Hero banner: if active ads exist → rotating ad carousel
                       else → original static hero
       2. Sponsored Products: top_listing ads shown before new arrivals
     ============================================================ --}}

@php
    // Pull in DocType + head from the storefront layout wrapper if used,
    // otherwise this file is included raw inside the full HTML shell.
    // The original file began directly with the header include, so we
    // preserve that exactly.
@endphp

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

{{-- ── Hero Banner ──────────────────────────────────────────────
     If active homepage hero ads exist → show rotating ad slideshow
     Otherwise → show original static hero banner
     ──────────────────────────────────────────────────────────── --}}
@if(isset($heroBannerAds) && $heroBannerAds->count())



{{-- AD HERO BANNER CAROUSEL --}}
<section class="welcome_area" style="position:relative;overflow:hidden;min-height:520px;">

    {{-- Slides --}}
    <div id="heroAdCarousel" class="carousel slide" data-ride="carousel" data-interval="5000"
         style="min-height:520px;">
        <ol class="carousel-indicators">
            @foreach($heroBannerAds as $i => $ad)
            <li data-target="#heroAdCarousel"
                data-slide-to="{{ $i }}"
                class="{{ $i === 0 ? 'active' : '' }}"
                style="background:#2ECC71;width:10px;height:10px;border-radius:50%;"></li>
            @endforeach
        </ol>

        <div class="carousel-inner" style="min-height:520px;">
            @foreach($heroBannerAds as $i => $ad)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}"
                 style="min-height:520px;">

                @if($ad->media_type === 'video' && $ad->media_url)
                {{-- Video ad --}}
                <video autoplay muted loop playsinline
                       style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;">
                    <source src="{{ $ad->media_url }}">
                </video>
                @elseif($ad->media_url)
                {{-- Image ad --}}
                <div class="bg-img background-overlay"
                     style="position:absolute;inset:0;background-image:url({{ $ad->media_url }});background-size:cover;background-position:center;z-index:0;"></div>
                @else
                {{-- Fallback colour --}}
                <div style="position:absolute;inset:0;background:linear-gradient(135deg,#1a1a2e,#2ECC71);z-index:0;"></div>
                @endif

                {{-- Overlay + content --}}
                <div style="position:absolute;inset:0;background:rgba(0,0,0,.45);z-index:1;"></div>
                <div class="container h-100" style="position:relative;z-index:2;min-height:520px;">
                    <div class="row h-100 align-items-center" style="min-height:520px;">
                        <div class="col-12 col-md-7">
                            <div class="hero-content">
                                <span style="display:inline-block;background:#2ECC71;color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:12px;letter-spacing:1px;text-transform:uppercase;margin-bottom:10px;">
                                    Sponsored
                                </span>
                                <h2 style="color:#fff;font-size:clamp(24px,4vw,46px);font-weight:800;line-height:1.2;margin-bottom:16px;">
                                    {{ $ad->title }}
                                </h2>
                                <a href="{{ $ad->clickTrackingUrl() }}"
                                   class="btn essence-btn"
                                   style="padding:12px 28px;font-size:15px;">
                                    Shop Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        {{-- Prev / Next --}}
        @if($heroBannerAds->count() > 1)
        <a class="carousel-control-prev" href="#heroAdCarousel" role="button" data-slide="prev"
           style="width:5%;">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#heroAdCarousel" role="button" data-slide="next"
           style="width:5%;">
            <span class="carousel-control-next-icon"></span>
        </a>
        @endif
    </div>

</section>
{{-- END AD HERO BANNER --}}

@else

{{-- ORIGINAL STATIC HERO (unchanged) --}}
<section class="welcome_area bg-img background-overlay"
         style="background-image: url({{ $heroBanner ?? asset('img/bg-img/bg-1.png') }});">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12">
                <div class="hero-content">
                    <h6>Orderer</h6>
                    <h2>Shop Everything, <br>Delivered Everywhere</h2>
                    <a href="{{ route('shop.index') }}" class="btn essence-btn">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

@endif
{{-- END HERO --}}


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

        {{-- Scrollable Categories Container --}}
        <div class="row">
            <div class="col-12">
                <div style="overflow-x: auto; overflow-y: hidden; white-space: nowrap; padding-bottom: 15px; -webkit-overflow-scrolling: touch;">
                    <div style="display: inline-flex; gap: 12px; flex-wrap: nowrap;">
                        @foreach($categories as $cat)
                        @php
                            // Extract icon name from feather class (e.g., "feather-smartphone" -> "smartphone")
                            $iconName = $cat->icon ? str_replace('feather-', '', $cat->icon) : 'tag';
                            // List of valid Feather icons (common ones)
                            $validFeatherIcons = [
                                'smartphone', 'shirt', 'home', 'heart', 'activity', 'book', 'gamepad', 
                                'coffee', 'truck', 'baby', 'paw', 'gift', 'monitor', 'camera', 'music', 
                                'tool', 'sun', 'feather', 'tag', 'shopping-bag', 'headphones', 'watch', 
                                'tv', 'tablet', 'laptop', 'printer', 'speaker', 'mic', 'film', 'video', 
                                'image', 'folder', 'file', 'archive', 'cloud', 'database', 'server', 
                                'cpu', 'hard-drive', 'battery', 'wifi', 'bluetooth', 'cast', 'mail', 
                                'message-square', 'phone', 'bell', 'calendar', 'clock', 'map-pin', 'navigation'
                            ];
                            // Use default if icon name is not in the valid list
                            $finalIcon = in_array($iconName, $validFeatherIcons) ? $iconName : 'tag';
                        @endphp
                        <div style="display: inline-block; width: 100px; text-align: center;">
                            <a href="{{ route('shop.category', $cat->slug) }}"
                               class="d-flex flex-column align-items-center text-decoration-none"
                               style="padding: 15px 10px; background: #fff; border-radius: 12px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.05);"
                               onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(46,204,113,0.15)'; this.style.borderColor='#2ECC71';"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'; this.style.borderColor='transparent';">
                                
                                {{-- Feather Icon with fallback --}}
                                <i data-feather="{{ $finalIcon }}" style="width: 32px; height: 32px; color: #2ECC71; margin-bottom: 10px;"></i>
                                    
                                <span style="font-size: 13px; font-weight: 500; color: #333; white-space: normal; word-break: keep-all;">
                                    {{ $cat->name }}
                                </span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Flash Sales (unchanged) --}}
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


{{-- Featured / Promoted Products (unchanged) --}}
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


{{-- ── SPONSORED PRODUCTS (top_listing ads) ────────────────────
     Shown only when there are active top_listing ads.
     Sits between Featured and Brands strip.
     ─────────────────────────────────────────────────────────── --}}
@if(isset($topListingAds) && $topListingAds->count())
<section style="background:#fffdf5;padding:40px 0;">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12 d-flex align-items-center gap-3">
                <h2 style="font-weight:800;color:#1a1a1a;margin:0;">Sponsored</h2>
                <span style="background:#FEF9E7;color:#B7950B;border:1px solid #F9CA24;padding:2px 10px;border-radius:12px;font-size:11px;font-weight:700;text-transform:uppercase;">
                    Ad
                </span>
            </div>
        </div>
        <div class="row">
            @foreach($topListingAds as $ad)
            @php
                $sponsoredProduct = $ad->promotable;
                if (!$sponsoredProduct) continue;
                $sImg = $sponsoredProduct->images->where('is_primary',true)->first()
                        ?? $sponsoredProduct->images->first();
            @endphp
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <div class="single-product-wrapper" style="position:relative;">

                    {{-- Sponsored badge on card --}}
                    <div style="position:absolute;top:8px;left:8px;z-index:3;background:#FEF9E7;color:#B7950B;border:1px solid #F9CA24;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;">
                        Sponsored
                    </div>

                    <div class="product-img">
                        <a href="{{ $ad->clickTrackingUrl() }}">
                            <img src="{{ $sImg->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                        </a>
                        <div class="product-favourite">
                            <a href="#" class="favme fa fa-heart" data-product="{{ $sponsoredProduct->id }}"></a>
                        </div>
                    </div>
                    <div class="product-description">
                        <span>{{ $sponsoredProduct->seller->business_name ?? '' }}</span>
                        <a href="{{ $ad->clickTrackingUrl() }}">
                            <h6>{{ Str::limit($sponsoredProduct->name, 35) }}</h6>
                        </a>
                        <p class="product-price">
                            @if($sponsoredProduct->sale_price)
                                <span class="old-price">${{ number_format($sponsoredProduct->price, 2) }}</span>
                                ${{ number_format($sponsoredProduct->sale_price, 2) }}
                            @else
                                ${{ number_format($sponsoredProduct->price, 2) }}
                            @endif
                        </p>
                        <div class="hover-content">
                            <div class="add-to-cart-btn">
                                <a href="#" class="btn essence-btn add-to-cart"
                                   data-product="{{ $sponsoredProduct->id }}">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
{{-- END SPONSORED PRODUCTS --}}


{{-- Brands Strip (unchanged) --}}
@if($brands->count())
<section class="ord-brands-strip">
    <div class="container">
        <div class="ord-brands-header">
            <span class="ord-brands-label">Trusted Brands</span>
            <a href="{{ route('brands.index') }}" class="ord-brands-viewall">
                View all brands <i class="fa fa-arrow-right"></i>
            </a>
        </div>

        @php $brandList = $brands->take(8); @endphp

        @if($brandList->count() >= 5)
        <div class="ord-brands-track-wrap">
            <div class="ord-brands-track">
                @foreach($brandList as $brand)
                <a href="{{ route('brands.show', $brand->slug) }}" class="ord-brand-item" title="{{ $brand->name }}">
                    @if($brand->logo)
                        <img src="{{ $brand->logo }}" alt="{{ $brand->name }}">
                    @endif
                    <span class="ord-brand-name">{{ $brand->name }}</span>
                </a>
                @endforeach
                @foreach($brandList as $brand)
                <a href="{{ route('brands.show', $brand->slug) }}" class="ord-brand-item" title="{{ $brand->name }}" aria-hidden="true" tabindex="-1">
                    @if($brand->logo)
                        <img src="{{ $brand->logo }}" alt="">
                    @endif
                    <span class="ord-brand-name">{{ $brand->name }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @else
        <div class="ord-brands-static">
            @foreach($brandList as $brand)
            <a href="{{ route('brands.show', $brand->slug) }}" class="ord-brand-item" title="{{ $brand->name }}">
                @if($brand->logo)
                    <img src="{{ $brand->logo }}" alt="{{ $brand->name }}">
                @endif
                <span class="ord-brand-name">{{ $brand->name }}</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif


{{-- New Arrivals (unchanged) --}}
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


{{-- Book a Rider CTA (unchanged) --}}
<div class="cta-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="cta-content bg-img background-overlay"
                     style="background-image: url({{ asset('img/bg-img/l-banner.png') }});">
                    <div class="h-100 d-flex align-items-center justify-content-end">
                        <div class="cta--text">
                            <a href="{{ route('rider.booking') }}" class="btn essence-btn">Book A Delivery</a>
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
<script src="{{ asset('dashboard/assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/apexcharts.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/vendors/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/common-init.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/dashboard-init.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/theme-customizer-init.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();
</script>
<script>
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    } else {
        // If feather is not loaded, try to load it
        var featherScript = document.createElement('script');
        featherScript.src = 'https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js';
        featherScript.onload = function() {
            feather.replace();
        };
        document.head.appendChild(featherScript);
    }
</script>

@stack('scripts')
</body>

<style>
    /* Custom scrollbar styling */
    .top_catagory_area .col-12 > div::-webkit-scrollbar {
        height: 6px;
    }
    
    .top_catagory_area .col-12 > div::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .top_catagory_area .col-12 > div::-webkit-scrollbar-thumb {
        background: #2ECC71;
        border-radius: 10px;
    }
    
    .top_catagory_area .col-12 > div::-webkit-scrollbar-thumb:hover {
        background: #27ae60;
    }
    
    /* Smooth hover transition */
    .top_catagory_area a {
        transition: all 0.3s ease;
    }
</style>

<script>
    // Re-initialize Feather icons after the page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
<script>
// Flash sale countdown (unchanged)
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

// Add to cart via AJAX (unchanged)
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

// Wishlist heart toggle (unchanged)
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
