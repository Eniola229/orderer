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
<style>
    
    #heroAdCarousel .carousel-item {
        min-height: 520px !important;
        position: relative !important;
        overflow: hidden !important;
    }
    
    #heroAdCarousel .ad-bg {
        position: absolute !important;
        top: 0 !important; left: 0 !important;
        width: 100% !important; height: 100% !important;
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        z-index: 0 !important;
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    #heroAdCarousel .ad-content-wrap {
        position: relative !important;
        z-index: 2 !important;
        min-height: 520px !important;
        display: flex !important;
        align-items: center !important;
    }
    
    /* MOBILE FIXES */
    @media (max-width: 768px) {
        /* Remove fixed height on mobile */
        #heroAdCarousel .carousel-item {
            min-height: auto !important;
            height: auto !important;
        }
        
        #heroAdCarousel .ad-content-wrap {
            min-height: auto !important;
            padding: 60px 0 !important;
        }
        
        /* Fix text touching sides - add padding to the content wrapper */
        #heroAdCarousel .ad-content-wrap .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        
        /* Give the text box proper margins on mobile */
        #heroAdCarousel .ad-content-wrap .col-12,
        #heroAdCarousel .ad-content-wrap .col-md-7 {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }
        
        /* Reduce the gap after hero section */
        .welcome_area {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
        
        /* Remove gap between hero and categories */
        .top_catagory_area {
            margin-top: -20px !important;
            padding-top: 20px !important;
        }
    }


    /* Sell on Orderer Section Responsive Styles */
    .sell-on-orderer-section {
        position: relative;
        overflow: hidden;
    }

    .sell-content .badge {
        display: inline-block;
        animation: fadeInUp 0.6s ease;
    }

    .sell-features div[class*="col-"] {
        animation: fadeInUp 0.6s ease;
        animation-fill-mode: both;
    }

    .sell-features div[class*="col-"]:nth-child(1) { animation-delay: 0.1s; }
    .sell-features div[class*="col-"]:nth-child(2) { animation-delay: 0.2s; }
    .sell-features div[class*="col-"]:nth-child(3) { animation-delay: 0.3s; }
    .sell-features div[class*="col-"]:nth-child(4) { animation-delay: 0.4s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .sell-on-orderer-section {
            padding: 40px 0 !important;
        }
        
        .sell-content {
            text-align: center !important;
        }
        
        .sell-features .row {
            justify-content: center;
        }
        
        .sell-features .col-sm-6 {
            display: flex;
            justify-content: center;
        }
        
        .sell-image {
            margin-top: 30px;
        }
        
        /* Simplified banner version */
        .sell-card {
            margin: 0 15px;
        }
        
        .sell-card h3 {
            font-size: 22px;
        }
        
        .sell-card p {
            font-size: 14px;
        }
        
        .sell-card .btn {
            padding: 10px 25px;
            font-size: 14px;
        }
    }

    /* Tablet Styles */
    @media (min-width: 769px) and (max-width: 1024px) {
        .sell-on-orderer-section {
            padding: 50px 0 !important;
        }
    }

    /* ── Best Sellers & Top Rated tab styles ─────────────────── */
    .ord-section-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        border-bottom: 2px solid #f0f0f0;
    }
    .ord-section-tabs .tab-btn {
        background: none;
        border: none;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        color: #999;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s;
    }
    .ord-section-tabs .tab-btn.active {
        color: #2ECC71;
        border-bottom-color: #2ECC71;
    }
    .ord-tab-panel { display: none; }
    .ord-tab-panel.active { display: block; }

    /* Hero animations */
    .float-icon {
        position: absolute;
        font-size: 28px;
        animation: floatUpDown 4s ease-in-out infinite;
        opacity: 0.6;
    }
    @keyframes floatUpDown {
        0%, 100% { transform: translateY(0px) rotate(-5deg); }
        50% { transform: translateY(-18px) rotate(5deg); }
    }
    .particle {
        position: absolute;
        width: 3px;
        height: 3px;
        background: #2ECC71;
        border-radius: 50%;
        animation: particleFloat 6s infinite;
        opacity: 0.4;
    }
    .particle:nth-child(1) { left:10%;top:20%;animation-delay:0s;animation-duration:5s; }
    .particle:nth-child(2) { left:30%;top:70%;animation-delay:1s;animation-duration:7s; }
    .particle:nth-child(3) { left:50%;top:30%;animation-delay:2s;animation-duration:6s; }
    .particle:nth-child(4) { left:70%;top:80%;animation-delay:0.5s;animation-duration:8s; }
    .particle:nth-child(5) { left:85%;top:15%;animation-delay:1.5s;animation-duration:5.5s; }
    .particle:nth-child(6) { left:20%;top:50%;animation-delay:3s;animation-duration:7s; }
    .particle:nth-child(7) { left:60%;top:60%;animation-delay:2.5s;animation-duration:6.5s; }
    .particle:nth-child(8) { left:90%;top:40%;animation-delay:1s;animation-duration:9s; }
    @keyframes particleFloat {
        0% { transform: translateY(0) scale(1); opacity:0.4; }
        50% { transform: translateY(-80px) scale(1.5); opacity:0.8; }
        100% { transform: translateY(-160px) scale(0); opacity:0; }
    }
    .delivery-bike {
        font-size: 28px;
        animation: bikeRide 8s linear infinite;
        white-space: nowrap;
    }
    @keyframes bikeRide {
        0% { left: -100px; }
        100% { left: 110%; }
    }
    @keyframes pulse-dot {
        0%, 100% { opacity:1; transform:scale(1); }
        50% { opacity:0.4; transform:scale(1.4); }
    }
    /* Text cycling animation */
    .text-cycle::after {
        content: 'Everything';
        animation: cycleText 4s steps(1) infinite;
        color: #F39C12;
        font-style: italic;
    }
    @keyframes cycleText {
        0%   { content: 'Everything'; }
        25%  { content: 'Electronics'; }
        50%  { content: 'Fashion'; }
        75%  { content: 'Gadgets'; }
    }
    /* Remove gap between hero and categories */
    .welcome_area + .top_catagory_area {
        margin-top: 0 !important;
        padding-top: 30px !important;
    }

    /* Delivery section animations */
    .moving-bike {
        position: absolute;
        font-size: 26px;
        bottom: 0;
        animation: driveAcross 10s linear infinite;
    }
    @keyframes driveAcross {
        0%   { left: -60px; }
        100% { left: 110%; }
    }
    .pin-float {
        position: absolute;
        font-size: 20px;
        opacity: 0.5;
        animation: pinBounce 3s ease-in-out infinite;
    }
    @keyframes pinBounce {
        0%, 100% { transform: translateY(0); opacity:0.5; }
        50% { transform: translateY(-12px); opacity:0.9; }
    }
    .road-dash {
        height: 100%;
        background: repeating-linear-gradient(90deg, #2ECC71 0px, #2ECC71 30px, transparent 30px, transparent 60px);
        animation: dashMove 1s linear infinite;
        width: 200%;
    }
    @keyframes dashMove {
        0% { transform: translateX(0); }
        100% { transform: translateX(-60px); }
    }
    .vehicle-card:hover {
        border-color: #2ECC71 !important;
        background: rgba(46,204,113,0.1) !important;
    }
@media (max-width: 575.98px) {
    .popular-products-slides .product-description h6 {
        font-size: 12px !important;
    }
    .popular-products-slides .product-price,
    .popular-products-slides .product-price .old-price {
        font-size: 13px !important;
    }
    .popular-products-slides .product-img .product-badge {
        font-size: 12px !important;
        height: 25px !important;
        line-height: 25px !important;
        padding: 0 10px !important;
        top: 20px !important;
        left: 20px !important;
    }
    .popular-products-slides .product-img .product-favourite a {
        width: 45px !important;
        height: 25px !important;
        font-size: 14px !important;
        line-height: 25px !important;
        top: 20px !important;
        right: 20px !important;
    }
    .popular-products-slides .product-description .hover-content {
        position: absolute !important;
        top: -70px !important;
        left: 20px !important;
        right: 20px !important;
        width: calc(100% - 40px) !important;
    }
    .popular-products-slides .product-description .hover-content .essence-btn,
    .popular-products-slides .product-description .hover-content a.essence-btn {
        min-width: 170px !important;
        width: 100% !important;
        height: 50px !important;
        line-height: 50px !important;
        padding: 0 40px !important;
        font-size: 12px !important;
        letter-spacing: 1.5px !important;
    }
}
@media (max-width: 575.98px) {
    .popular-products-slides .product-description h6 {
        font-size: 14px !important;
    }
    .popular-products-slides .product-price,
    .popular-products-slides .product-price .old-price {
        font-size: 14px !important;
    }
}
</style>
 
@if(isset($heroBannerAds) && $heroBannerAds->count())

<section class="welcome_area" style="position:relative;overflow:hidden;min-height:520px;">

    <div id="heroAdCarousel" class="carousel slide" data-ride="carousel" data-interval="5000">

        <ol class="carousel-indicators">
            @foreach($heroBannerAds as $i => $ad)
            <li data-target="#heroAdCarousel"
                data-slide-to="{{ $i }}"
                class="{{ $i === 0 ? 'active' : '' }}"
                style="background:#2ECC71;width:10px;height:10px;border-radius:50%;"></li>
            @endforeach
            <li data-target="#heroAdCarousel"
                data-slide-to="{{ $heroBannerAds->count() }}"
                style="background:#2ECC71;width:10px;height:10px;border-radius:50%;"></li>
        </ol>

        <div class="carousel-inner">

            {{-- Ad slides --}}
            @foreach($heroBannerAds as $i => $ad)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">

                @if($ad->media_type === 'video' && $ad->media_url)
                    {{-- Video: use background on a div to avoid theme CSS conflicts --}}
                    <video autoplay muted loop playsinline
                           style="position:absolute!important;top:0!important;left:0!important;width:100%!important;height:100%!important;object-fit:cover!important;z-index:0!important;display:block!important;opacity:1!important;visibility:visible!important;">
                        <source src="{{ $ad->media_url }}">
                    </video>
                @elseif($ad->media_url)
                    {{-- Use background-image on a div instead of <img> to avoid theme CSS hiding images --}}
                    <div class="ad-bg" style="background-image: url('{{ $ad->media_url }}');"></div>
                @else
                    <div class="ad-bg" style="background: linear-gradient(135deg,#1a1a2e,#2ECC71);"></div>
                @endif

                <div class="container ad-content-wrap">
                    <div class="row w-100">
                        <div class="col-12 col-md-7" style="background:rgba(0,0,0,0.35);border-radius:12px;padding:24px;">
                            <div class="hero-content">
                                <span style="display:inline-block;background:#2ECC71;color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:12px;letter-spacing:1px;text-transform:uppercase;margin-bottom:10px;">
                                    Sponsored
                                </span>
                                <h3 style="color:#fff;font-size:clamp(24px,4vw,46px);font-weight:800;line-height:1.2;margin-bottom:16px;text-shadow: 0 2px 8px rgba(0,0,0,0.6), 0 1px 3px rgba(0,0,0,0.8);">
                                    {{ $ad->title }}
                                </h3>
                                <a href="{{ $ad->clickTrackingUrl() }}" class="btn essence-btn">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            @endforeach

           {{-- Static hero as last slide - Animated --}}
<div class="carousel-item">
    <div style="position:absolute;inset:0;background:linear-gradient(135deg,#0a0a1a 0%,#1a1a2e 50%,#0d2d1a 100%);z-index:0;"></div>

    {{-- Floating particles --}}
    <div class="hero-particles" style="position:absolute;inset:0;z-index:1;overflow:hidden;">
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div>
    </div>

    {{-- Floating product/service icons - LEFT SIDE --}}
    <div style="position:absolute;inset:0;z-index:1;overflow:hidden;">

        {{-- LEFT COLUMN --}}
        {{-- Smartphone --}}
        <div class="float-icon" style="top:12%;left:4%;animation-delay:0s;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="2.5"/></svg>
        </div>
        {{-- House --}}
        <div class="float-icon" style="top:35%;left:3%;animation-delay:1.4s;">
            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#F39C12" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
        </div>
        {{-- Shopping bag --}}
        <div class="float-icon" style="top:62%;left:4%;animation-delay:2.8s;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#E74C3C" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        </div>
        {{-- Watch --}}
        <div class="float-icon" style="top:80%;left:6%;animation-delay:0.8s;">
            <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#9B59B6" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="7"/><polyline points="12 9 12 12 13.5 13.5"/><path d="M16.51 17.35l-.35 3.83a2 2 0 01-2 1.82H9.83a2 2 0 01-2-1.82l-.35-3.83m.01-10.7l.35-3.83A2 2 0 019.83 1h4.35a2 2 0 012 1.82l.35 3.83"/></svg>
        </div>

        {{-- RIGHT COLUMN --}}
        {{-- Laptop + person (monitor with person silhouette) --}}
        <div class="float-icon" style="top:10%;right:5%;animation-delay:0.5s;">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="13" rx="2"/><path d="M8 21h8M12 17v4"/><circle cx="12" cy="9" r="2.5" fill="rgba(46,204,113,0.3)"/><path d="M8.5 13c0-1.93 1.57-3.5 3.5-3.5s3.5 1.57 3.5 3.5" stroke-dasharray="3,2"/></svg>
        </div>
        {{-- Camera --}}
        <div class="float-icon" style="top:30%;right:4%;animation-delay:1.8s;">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#3498DB" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </div>
        {{-- Plumber / wrench tool --}}
        <div class="float-icon" style="top:52%;right:5%;animation-delay:3.2s;">
            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#E67E22" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
        </div>
        {{-- Gift/box --}}
        <div class="float-icon" style="top:72%;right:6%;animation-delay:1.2s;">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#F39C12" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
        </div>

        {{-- EXTRA scattered --}}
        {{-- Headphones --}}
        <div class="float-icon" style="top:18%;right:18%;animation-delay:2.2s;">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#9B59B6" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0118 0v6"/><path d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3z"/></svg>
        </div>
        {{-- Building/apartment --}}
        <div class="float-icon" style="top:55%;right:18%;animation-delay:0.4s;">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#1ABC9C" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="1"/><path d="M9 22V12h6v10"/><rect x="8" y="5" width="3" height="3" rx="0.5"/><rect x="13" y="5" width="3" height="3" rx="0.5"/><rect x="8" y="10" width="3" height="3" rx="0.5"/><rect x="13" y="10" width="3" height="3" rx="0.5"/></svg>
        </div>
        {{-- Diamond gem --}}
        <div class="float-icon" style="top:40%;right:20%;animation-delay:1.6s;">
            <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#E74C3C" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 22 22 7 12 2"/><polyline points="2 7 12 12 22 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
        </div>

    </div>

    {{-- Moving delivery bike SVG --}}
    <div style="position:absolute;bottom:18%;z-index:2;animation:bikeRide 8s linear infinite;white-space:nowrap;display:flex;align-items:center;gap:8px;">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M15 6h-5l-1 4h8l-2-4z"/><path d="M5.5 17.5L9 8"/><path d="M15 6l2 5h1.5"/></svg>
        <span style="font-size:12px;color:#2ECC71;font-weight:700;">Fast Delivery</span>
    </div>

    {{-- Content --}}
    <div class="container ad-content-wrap" style="position:relative;z-index:3;">
        <div class="row w-100 align-items-center">
            <div class="col-12 col-md-7">
                <div class="hero-content">
                    {{-- Badge --}}
                    <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(46,204,113,0.15);border:1px solid rgba(46,204,113,0.4);border-radius:50px;padding:6px 16px;margin-bottom:16px;">
                        <span style="width:8px;height:8px;background:#2ECC71;border-radius:50%;display:inline-block;animation:pulse-dot 1.5s infinite;"></span>
                        <span style="color:#2ECC71;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Shop · Hire · Rent · Deliver</span>
                    </div>
                    <h2 style="color:#fff;font-size:clamp(28px,5vw,52px);font-weight:900;line-height:1.1;margin-bottom:16px;text-shadow:0 2px 20px rgba(0,0,0,0.5);">
                        Shop <span class="text-cycle"></span><br>
                        <span style="color:#2ECC71;">Delivered to Your Door</span>
                    </h2>
                    <p style="color:rgba(255,255,255,0.75);font-size:15px;margin-bottom:24px;line-height:1.7;">
                        Products · Services · Properties · Fast Delivery — all in one place.
                    </p>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <a href="{{ route('shop.index') }}" class="btn essence-btn" style="background:#2ECC71;border:none;font-weight:700;">
                            Shop Now →
                        </a>
                 <!--        <a href="{{ route('seller.register') }}" class="btn" style="border:2px solid rgba(255,255,255,0.4);color:#fff;padding:0 24px;border-radius:0;font-size:12px;font-weight:700;letter-spacing:1px;line-height:50px;height:50px;display:inline-block;">
                            Sell With Us
                        </a>
 -->                    </div>
                    {{-- Live stats --}}
                    <div style="display:flex;gap:24px;margin-top:28px;flex-wrap:wrap;">
                        <div>
                            <p style="color:#2ECC71;font-size:20px;font-weight:800;margin:0;">10K+</p>
                            <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:0;">Products</p>
                        </div>
                        <div style="width:1px;background:rgba(255,255,255,0.1);"></div>
                        <div>
                            <p style="color:#2ECC71;font-size:20px;font-weight:800;margin:0;">500+</p>
                            <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:0;">Sellers</p>
                        </div>
                        <div style="width:1px;background:rgba(255,255,255,0.1);"></div>
                        <div>
                            <p style="color:#2ECC71;font-size:20px;font-weight:800;margin:0;">Services</p>
                            <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:0;">& Properties</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</section>

@else

<section class="welcome_area" style="position:relative;overflow:hidden;min-height:520px;background:linear-gradient(135deg,#0a0a1a 0%,#1a1a2e 50%,#0d2d1a 100%);">

    {{-- Floating particles --}}
    <div class="hero-particles" style="position:absolute;inset:0;z-index:1;overflow:hidden;">
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div>
    </div>

    {{-- Floating icons --}}
    <div style="position:absolute;inset:0;z-index:1;overflow:hidden;">
        <div class="float-icon" style="top:12%;left:4%;animation-delay:0s;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="2.5"/></svg>
        </div>
        <div class="float-icon" style="top:35%;left:3%;animation-delay:1.4s;">
            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#F39C12" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
        </div>
        <div class="float-icon" style="top:62%;left:4%;animation-delay:2.8s;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#E74C3C" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        </div>
        <div class="float-icon" style="top:80%;left:6%;animation-delay:0.8s;">
            <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#9B59B6" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="7"/><polyline points="12 9 12 12 13.5 13.5"/><path d="M16.51 17.35l-.35 3.83a2 2 0 01-2 1.82H9.83a2 2 0 01-2-1.82l-.35-3.83m.01-10.7l.35-3.83A2 2 0 019.83 1h4.35a2 2 0 012 1.82l.35 3.83"/></svg>
        </div>
        <div class="float-icon" style="top:10%;right:5%;animation-delay:0.5s;">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="13" rx="2"/><path d="M8 21h8M12 17v4"/><circle cx="12" cy="9" r="2.5" fill="rgba(46,204,113,0.3)"/><path d="M8.5 13c0-1.93 1.57-3.5 3.5-3.5s3.5 1.57 3.5 3.5" stroke-dasharray="3,2"/></svg>
        </div>
        <div class="float-icon" style="top:30%;right:4%;animation-delay:1.8s;">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#3498DB" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </div>
        <div class="float-icon" style="top:52%;right:5%;animation-delay:3.2s;">
            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#E67E22" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
        </div>
        <div class="float-icon" style="top:72%;right:6%;animation-delay:1.2s;">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#F39C12" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
        </div>
        <div class="float-icon" style="top:18%;right:18%;animation-delay:2.2s;">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#9B59B6" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0118 0v6"/><path d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3z"/></svg>
        </div>
        <div class="float-icon" style="top:55%;right:18%;animation-delay:0.4s;">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#1ABC9C" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="1"/><path d="M9 22V12h6v10"/><rect x="8" y="5" width="3" height="3" rx="0.5"/><rect x="13" y="5" width="3" height="3" rx="0.5"/><rect x="8" y="10" width="3" height="3" rx="0.5"/><rect x="13" y="10" width="3" height="3" rx="0.5"/></svg>
        </div>
        <div class="float-icon" style="top:40%;right:20%;animation-delay:1.6s;">
            <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#E74C3C" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 22 22 7 12 2"/><polyline points="2 7 12 12 22 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
        </div>
    </div>

    {{-- Moving delivery bike --}}
    <div style="position:absolute;bottom:18%;z-index:2;animation:bikeRide 8s linear infinite;white-space:nowrap;display:flex;align-items:center;gap:8px;">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M15 6h-5l-1 4h8l-2-4z"/><path d="M5.5 17.5L9 8"/><path d="M15 6l2 5h1.5"/></svg>
        <span style="font-size:12px;color:#2ECC71;font-weight:700;">Fast Delivery</span>
    </div>

    {{-- Content --}}
    <div class="container" style="position:relative;z-index:3;min-height:520px;display:flex;align-items:center;">
        <div class="row w-100 align-items-center">
            <div class="col-12 col-md-7">
                <div class="hero-content">
                    <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(46,204,113,0.15);border:1px solid rgba(46,204,113,0.4);border-radius:50px;padding:6px 16px;margin-bottom:16px;">
                        <span style="width:8px;height:8px;background:#2ECC71;border-radius:50%;display:inline-block;animation:pulse-dot 1.5s infinite;"></span>
                        <span style="color:#2ECC71;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Shop · Hire · Rent · Deliver</span>
                    </div>
                    <h2 style="color:#fff;font-size:clamp(28px,5vw,52px);font-weight:900;line-height:1.1;margin-bottom:16px;text-shadow:0 2px 20px rgba(0,0,0,0.5);">
                        Shop <span class="text-cycle"></span><br>
                        <span style="color:#2ECC71;">Delivered to Your Door</span>
                    </h2>
                    <p style="color:rgba(255,255,255,0.75);font-size:15px;margin-bottom:24px;line-height:1.7;">
                        Products · Services · Properties · Fast Delivery — all in one place.
                    </p>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <a href="{{ route('shop.index') }}" class="btn essence-btn" style="background:#2ECC71;border:none;font-weight:700;">
                            Shop Now →
                        </a>
                 <!--        <a href="{{ route('seller.register') }}" class="btn" style="border:2px solid rgba(255,255,255,0.4);color:#fff;padding:0 24px;border-radius:0;font-size:12px;font-weight:700;letter-spacing:1px;line-height:50px;height:50px;display:inline-block;">
                            Sell With Us
                        </a>
 -->                    </div>
                    <div style="display:flex;gap:24px;margin-top:28px;flex-wrap:wrap;">
                        <div>
                            <p style="color:#2ECC71;font-size:20px;font-weight:800;margin:0;">10K+</p>
                            <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:0;">Products</p>
                        </div>
                        <div style="width:1px;background:rgba(255,255,255,0.1);"></div>
                        <div>
                            <p style="color:#2ECC71;font-size:20px;font-weight:800;margin:0;">500+</p>
                            <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:0;">Sellers</p>
                        </div>
                        <div style="width:1px;background:rgba(255,255,255,0.1);"></div>
                        <div>
                            <p style="color:#2ECC71;font-size:20px;font-weight:800;margin:0;">Services</p>
                            <p style="color:rgba(255,255,255,0.5);font-size:11px;margin:0;">& Properties</p>
                        </div>
                    </div>
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

<div class="container" style="margin-top: -30px; margin-bottom: 20px;">
    <a href="{{ route('seller.register') }}" style="display: flex; align-items: center; justify-content: space-between; background: #2ECC71; border-radius: 12px; padding: 10px 20px; text-decoration: none;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa fa-rocket" style="color: white;"></i>
            <span style="color: white; font-weight: 500;">Sell on Orderer</span>
            <span style="color: rgba(255,255,255,0.9); font-size: 13px;">Millions of Customers • Easy setup</span>
        </div>
        <span style="background: white; color: #2ECC71; padding: 4px 16px; border-radius: 50px; font-size: 13px; font-weight: 600;">Join Now →</span>
    </a>
</div>

{{-- Flash Sales --}}
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
            <div class="col-6 col-sm-6 col-lg-3 mb-4">
                <div class="single-product-wrapper">
                    <div class="product-img">
                        <a href="{{ route('product.show', $flash->product->slug) }}">
                            <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                        </a>
                        {{-- FIXED: removed broken nested badge, clean discount + verified --}}
                        <div class="product-badge offer-badge">
                            <span>-{{ round((($flash->original_price - $flash->sale_price) / $flash->original_price) * 100) }}%</span>
                        </div>
                        @if($flash->product->seller->is_verified_business)
                        <div class="product-badge" style="background:#2ECC71;top:50px;left:20px;display:flex;align-items:center;gap:4px;">
                            <i class="fa fa-check-circle" style="font-size:10px;"></i> Verified
                        </div>
                        @endif
                        <div class="product-favourite">
                            <a href="#" class="favme fa fa-heart" data-product="{{ $flash->product_id }}"></a>
                        </div>
                    </div>
                    <div class="product-description">
                        <span>{{ Str::limit($flash->product->seller->business_name ?? '', 10) }}</span>
                        <a href="{{ route('product.show', $flash->product->slug) }}">
                            <h6>{{ Str::limit($flash->product->name, 40) }}</h6>
                        </a>
                        <p class="product-price">
                            <span class="old-price">₦{{ number_format($flash->original_price, 2) }}</span>
                            ₦{{ number_format($flash->sale_price, 2) }}
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
                            <div class="product-badge offer-badge"><span>SALE</span></div>
                            @endif
                            @if($product->seller->is_verified_business)
                            <div class="product-badge" style="background:#2ECC71;top:50px;left:20px;display:flex;align-items:center;gap:4px;">
                                <i class="fa fa-check-circle" style="font-size:10px;"></i> Verified
                            </div>
                            @endif
                            <div class="product-favourite">
                                <a href="#" class="favme fa fa-heart" data-product="{{ $product->id }}"></a>
                            </div>
                        </div>
                        <div class="product-description">
                            <span>{{ Str::limit($product->seller->business_name ?? '', 10) }}</span>
                            <a href="{{ route('product.show', $product->slug) }}">
                                <h6>{{ Str::limit($product->name, 35) }}</h6>
                            </a>
                            <p class="product-price">
                                @if($product->sale_price)
                                    <span class="old-price">₦{{ number_format($product->price, 2) }}</span>
                                    ₦{{ number_format($product->sale_price, 2) }}
                                @else
                                    ₦{{ number_format($product->price, 2) }}
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
            <div class="col-6 col-sm-6 col-lg-3 mb-4">
                <div class="single-product-wrapper" style="position:relative;">

                    {{-- Sponsored badge on card --}}
                    <div style="position:absolute;top:8px;left:8px;z-index:3;background:#FEF9E7;color:#B7950B;border:1px solid #F9CA24;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;">
                        Sponsored
                    </div>

                    <div class="product-img">
                        <a href="{{ $ad->clickTrackingUrl() }}">
                            <img src="{{ $sImg->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                        </a>
                        {{-- ADDED: verified badge for sponsored products --}}
                        @if($sponsoredProduct->seller->is_verified_business)
                        <div class="product-badge" style="background:#2ECC71;top:50px;left:20px;display:flex;align-items:center;gap:4px;">
                            <i class="fa fa-check-circle" style="font-size:10px;"></i> Verified
                        </div>
                        @endif
                        <div class="product-favourite">
                            <a href="#" class="favme fa fa-heart" data-product="{{ $sponsoredProduct->id }}"></a>
                        </div>
                    </div>
                    <div class="product-description">
                        <span>{{ Str::limit($sponsoredProduct->seller->business_name ?? '', 10) }}</span>
                        <a href="{{ $ad->clickTrackingUrl() }}">
                            <h6>{{ Str::limit($sponsoredProduct->name, 35) }}</h6>
                        </a>
                        <p class="product-price">
                            @if($sponsoredProduct->sale_price)
                                <span class="old-price">₦{{ number_format($sponsoredProduct->price, 2) }}</span>
                                ₦{{ number_format($sponsoredProduct->sale_price, 2) }}
                            @else
                                ₦{{ number_format($sponsoredProduct->price, 2) }}
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


{{-- ── Best Sellers & Top Rated (tabbed) ───────────────────────── --}}
@if($bestSellers->count() || $topRatedProducts->count())
<section style="background:#f4fdf8;padding:48px 0;">
    <div class="container">

        {{-- Section header with tabs --}}
        <div class="row align-items-center mb-0">
            <div class="col-12">
                <div class="ord-section-tabs">
                    @if($bestSellers->count())
                    <button class="tab-btn active" onclick="switchTab('best-sellers', this)">
                        <i class="fa fa-fire" style="color:#FF6B00;margin-right:6px;"></i> Best Sellers
                    </button>
                    @endif
                    @if($topRatedProducts->count())
                    <button class="tab-btn {{ !$bestSellers->count() ? 'active' : '' }}" onclick="switchTab('top-rated', this)">
                        <i class="fa fa-star" style="color:#F39C12;margin-right:6px;"></i> Top Rated
                    </button>
                    @endif
                    <div class="ml-auto" style="display:flex;align-items:center;margin-left:auto;">
                        <a href="{{ route('shop.index', ['sort' => 'popular']) }}" style="font-size:13px;color:#2ECC71;font-weight:600;text-decoration:none;">
                            View all <i class="fa fa-arrow-right" style="font-size:11px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Best Sellers panel --}}
        @if($bestSellers->count())
        <div id="tab-best-sellers" class="ord-tab-panel active">
            <div class="row">
                @foreach($bestSellers as $product)
                @php $img = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
                <div class="col-6 col-sm-6 col-lg-3 mb-4">
                    <div class="single-product-wrapper" style="position:relative;">
                        {{-- Best seller rank badge --}}
                        @php $loop_index = $loop->index + 1; @endphp
                        @if($loop_index <= 3)
                        <div style="position:absolute;top:8px;left:8px;z-index:3;width:26px;height:26px;background:{{ $loop_index === 1 ? '#FFD700' : ($loop_index === 2 ? '#C0C0C0' : '#CD7F32') }};color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;box-shadow:0 2px 6px rgba(0,0,0,0.2);">
                            #{{ $loop_index }}
                        </div>
                        @endif

                        <div class="product-img">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                            </a>
                            @if($product->sale_price)
                            <div class="product-badge offer-badge"><span>SALE</span></div>
                            @endif
                            @if($product->seller->is_verified_business)
                            <div class="product-badge" style="background:#2ECC71;top:50px;left:20px;display:flex;align-items:center;gap:4px;">
                                <i class="fa fa-check-circle" style="font-size:10px;"></i> Verified
                            </div>
                            @endif
                            <div class="product-favourite">
                                <a href="#" class="favme fa fa-heart" data-product="{{ $product->id }}"></a>
                            </div>
                        </div>
                        <div class="product-description">
                            <span>{{ Str::limit($product->seller->business_name ?? '', 10) }}</span>
                            <a href="{{ route('product.show', $product->slug) }}">
                                <h6>{{ Str::limit($product->name, 35) }}</h6>
                            </a>
                            {{-- Sold count --}}
                            @if($product->total_sold)
                            <p style="font-size:11px;color:#888;margin:-4px 0 4px;">
                                <i class="fa fa-shopping-bag" style="color:#FF6B00;font-size:10px;"></i>
                                {{ number_format($product->total_sold) }} sold
                            </p>
                            @endif
                            <p class="product-price">
                                @if($product->sale_price)
                                    <span class="old-price">₦{{ number_format($product->price, 2) }}</span>
                                    ₦{{ number_format($product->sale_price, 2) }}
                                @else
                                    ₦{{ number_format($product->price, 2) }}
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
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Top Rated panel --}}
        @if($topRatedProducts->count())
        <div id="tab-top-rated" class="ord-tab-panel {{ !$bestSellers->count() ? 'active' : '' }}">
            <div class="row">
                @foreach($topRatedProducts as $product)
                @php $img = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
                <div class="col-6 col-sm-6 col-lg-3 mb-4">
                    <div class="single-product-wrapper">
                        <div class="product-img">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                            </a>
                            @if($product->sale_price)
                            <div class="product-badge offer-badge"><span>SALE</span></div>
                            @endif
                            @if($product->seller->is_verified_business)
                            <div class="product-badge" style="background:#2ECC71;top:50px;left:20px;display:flex;align-items:center;gap:4px;">
                                <i class="fa fa-check-circle" style="font-size:10px;"></i> Verified
                            </div>
                            @endif
                            <div class="product-favourite">
                                <a href="#" class="favme fa fa-heart" data-product="{{ $product->id }}"></a>
                            </div>
                        </div>
                        <div class="product-description">
                            <span>{{ Str::limit($product->seller->business_name ?? '', 10) }}</span>
                            <a href="{{ route('product.show', $product->slug) }}">
                                <h6>{{ Str::limit($product->name, 35) }}</h6>
                            </a>
                            {{-- Star rating display --}}
                            <div style="display:flex;align-items:center;gap:4px;margin:-2px 0 4px;">
                                <span style="color:#F39C12;font-size:12px;letter-spacing:1px;">
                                    @for($s = 1; $s <= 5; $s++)
                                        {{ $s <= round($product->average_rating) ? '★' : '☆' }}
                                    @endfor
                                </span>
                                <span style="font-size:11px;color:#888;">
                                    {{ number_format($product->average_rating, 1) }}
                                    ({{ number_format($product->total_reviews) }})
                                </span>
                            </div>
                            <p class="product-price">
                                @if($product->sale_price)
                                    <span class="old-price">₦{{ number_format($product->price, 2) }}</span>
                                    ₦{{ number_format($product->sale_price, 2) }}
                                @else
                                    ₦{{ number_format($product->price, 2) }}
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
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>
@endif
{{-- END BEST SELLERS & TOP RATED --}}


{{-- Brands Strip --}}
@if($brands->count())
<section class="ord-brands-strip">
    <div class="container">
        <div class="ord-brands-header">
            <span class="ord-brands-label">Top Rated Brands</span>
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
                    @if($brand->average_rating > 0)
                    <span style="display:block;font-size:10px;color:#F39C12;margin-top:2px;">
                        ★ {{ number_format($brand->average_rating, 1) }}
                    </span>
                    @endif
                </a>
                @endforeach
                @foreach($brandList as $brand)
                <a href="{{ route('brands.show', $brand->slug) }}" class="ord-brand-item" title="{{ $brand->name }}" aria-hidden="true" tabindex="-1">
                    @if($brand->logo)
                        <img src="{{ $brand->logo }}" alt="">
                    @endif
                    <span class="ord-brand-name">{{ $brand->name }}</span>
                    @if($brand->average_rating > 0)
                    <span style="display:block;font-size:10px;color:#F39C12;margin-top:2px;">
                        ★ {{ number_format($brand->average_rating, 1) }}
                    </span>
                    @endif
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
                @if($brand->average_rating > 0)
                <span style="display:block;font-size:10px;color:#F39C12;margin-top:2px;">
                    ★ {{ number_format($brand->average_rating, 1) }}
                </span>
                @endif
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>
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
            <div class="col-6 col-sm-6 col-lg-3 mb-4">
                <div class="single-product-wrapper">
                    <div class="product-img">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                        </a>
                        <div class="product-badge new-badge"><span>New</span></div>
                        @if($product->seller->is_verified_business)
                        <div class="product-badge" style="background:#2ECC71;top:50px;left:20px;display:flex;align-items:center;gap:4px;">
                            <i class="fa fa-check-circle" style="font-size:10px;"></i> Verified
                        </div>
                        @endif
                        <div class="product-favourite">
                            <a href="#" class="favme fa fa-heart" data-product="{{ $product->id }}"></a>
                        </div>
                    </div>
                    <div class="product-description">
                        <span>{{ Str::limit($product->seller->business_name ?? '', 10) }}</span>
                        <a href="{{ route('product.show', $product->slug) }}">
                            <h6>{{ Str::limit($product->name, 35) }}</h6>
                        </a>
                        <p class="product-price">
                            @if($product->sale_price)
                                <span class="old-price">₦{{ number_format($product->price, 2) }}</span>
                                ₦{{ number_format($product->sale_price, 2) }}
                            @else
                                ₦{{ number_format($product->price, 2) }}
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


{{-- Book a Delivery CTA - Live Map Style --}}
<div style="background:linear-gradient(135deg,#0a0a1a,#1a1a2e,#0d2d1a);padding:60px 0;overflow:hidden;position:relative;">

    <div class="container" style="position:relative;z-index:2;">
        <div class="row align-items-center">

            {{-- Left: Text --}}
            <div class="col-12 col-md-5 mb-4 mb-md-0">
                <span style="display:inline-block;background:rgba(46,204,113,0.15);border:1px solid rgba(46,204,113,0.4);border-radius:50px;padding:4px 14px;font-size:11px;color:#2ECC71;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:12px;">
                    ⚡ Express Delivery
                </span>
                <h2 style="color:#fff;font-size:clamp(24px,4vw,40px);font-weight:900;line-height:1.2;margin-bottom:12px;">
                    Need Something<br>
                    <span style="color:#2ECC71;">Delivered Fast?</span>
                </h2>
                <p style="color:rgba(255,255,255,0.65);font-size:14px;line-height:1.8;margin-bottom:24px;">
                    Bikes, vans &amp; more — pick up and drop off anywhere.<br>
                    Track in real time.
                </p>

                {{-- Vehicle selector --}}
                <div style="display:flex;gap:10px;margin-bottom:20px;">
                    <div class="vehicle-card active-vehicle" onclick="selectVehicle(this,'bike')" style="flex:1;background:rgba(46,204,113,0.15);border:2px solid #2ECC71;border-radius:10px;padding:12px 8px;text-align:center;cursor:pointer;transition:all .2s;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:0 auto 4px;"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M15 6h-5l-1 4h8l-2-4z"/><path d="M5.5 17.5L9 8"/><path d="M15 6l2 5h1.5"/></svg>
                        <div style="color:#fff;font-size:11px;font-weight:600;">Bike</div>
                        <div style="color:#2ECC71;font-size:10px;">Fastest</div>
                    </div>
                    <div class="vehicle-card" onclick="selectVehicle(this,'van')" style="flex:1;background:rgba(255,255,255,0.05);border:2px solid rgba(255,255,255,0.1);border-radius:10px;padding:12px 8px;text-align:center;cursor:pointer;transition:all .2s;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:0 auto 4px;"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        <div style="color:#fff;font-size:11px;font-weight:600;">Van</div>
                        <div style="color:#aaa;font-size:10px;">Bulky items</div>
                    </div>
                    <div class="vehicle-card" onclick="selectVehicle(this,'tricycle')" style="flex:1;background:rgba(255,255,255,0.05);border:2px solid rgba(255,255,255,0.1);border-radius:10px;padding:12px 8px;text-align:center;cursor:pointer;transition:all .2s;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:0 auto 4px;"><circle cx="6" cy="18" r="3"/><circle cx="18" cy="18" r="3"/><path d="M6 18v-5l3-5h6l3 5v5"/><path d="M9 8h6"/><path d="M12 8v5"/></svg>
                        <div style="color:#fff;font-size:11px;font-weight:600;">Tricycle</div>
                        <div style="color:#aaa;font-size:10px;">Medium loads</div>
                    </div>
                </div>

                {{-- Features --}}
                <div style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span style="color:rgba(255,255,255,0.7);font-size:12px;">Live tracking</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span style="color:rgba(255,255,255,0.7);font-size:12px;">Insured</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span style="color:rgba(255,255,255,0.7);font-size:12px;">30–60 min</span>
                    </div>
                </div>

                <a href="{{ route('rider.booking') }}"
                   style="display:inline-block;background:#2ECC71;color:#fff;padding:14px 32px;border-radius:10px;font-weight:800;font-size:15px;text-decoration:none;letter-spacing:.5px;transition:all .2s;"
                   onmouseover="this.style.background='#27ae60';this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.background='#2ECC71';this.style.transform='none'">
                    Book a Delivery Now →
                </a>
                <p style="color:rgba(255,255,255,0.35);font-size:11px;margin:10px 0 0;">
                    Available 7 days · 8am – 10pm
                </p>
            </div>

            {{-- Right: Animated SVG Map --}}
            <div class="col-12 col-md-7">
                <div style="border-radius:16px;overflow:hidden;border:1px solid rgba(46,204,113,0.2);box-shadow:0 20px 60px rgba(0,0,0,0.4);">
                <svg id="deliveryMap" viewBox="0 0 600 380" xmlns="http://www.w3.org/2000/svg" style="width:100%;display:block;background:#1a2a1e;">

                    {{-- Map grid / road network --}}
                    {{-- Horizontal roads --}}
                    <line x1="0" y1="80" x2="600" y2="80" stroke="#2a3a2a" stroke-width="18"/>
                    <line x1="0" y1="180" x2="600" y2="180" stroke="#2a3a2a" stroke-width="18"/>
                    <line x1="0" y1="290" x2="600" y2="290" stroke="#2a3a2a" stroke-width="18"/>
                    {{-- Vertical roads --}}
                    <line x1="120" y1="0" x2="120" y2="380" stroke="#2a3a2a" stroke-width="18"/>
                    <line x1="300" y1="0" x2="300" y2="380" stroke="#2a3a2a" stroke-width="18"/>
                    <line x1="480" y1="0" x2="480" y2="380" stroke="#2a3a2a" stroke-width="18"/>

                    {{-- Road center dashes --}}
                    <line x1="0" y1="80" x2="600" y2="80" stroke="#3a5a3a" stroke-width="1.5" stroke-dasharray="16,12"/>
                    <line x1="0" y1="180" x2="600" y2="180" stroke="#3a5a3a" stroke-width="1.5" stroke-dasharray="16,12"/>
                    <line x1="0" y1="290" x2="600" y2="290" stroke="#3a5a3a" stroke-width="1.5" stroke-dasharray="16,12"/>
                    <line x1="120" y1="0" x2="120" y2="380" stroke="#3a5a3a" stroke-width="1.5" stroke-dasharray="16,12"/>
                    <line x1="300" y1="0" x2="300" y2="380" stroke="#3a5a3a" stroke-width="1.5" stroke-dasharray="16,12"/>
                    <line x1="480" y1="0" x2="480" y2="380" stroke="#3a5a3a" stroke-width="1.5" stroke-dasharray="16,12"/>

                    {{-- City blocks (buildings) --}}
                    <rect x="135" y="95" width="150" height="75" rx="4" fill="#1e2e20" stroke="#2ECC71" stroke-width="0.5" stroke-opacity="0.3"/>
                    <rect x="150" y="105" width="40" height="55" rx="2" fill="#243024"/>
                    <rect x="200" y="108" width="35" height="52" rx="2" fill="#1f2d1f"/>
                    <rect x="245" y="100" width="30" height="60" rx="2" fill="#243024"/>

                    <rect x="315" y="95" width="150" height="75" rx="4" fill="#1e2e20" stroke="#2ECC71" stroke-width="0.5" stroke-opacity="0.3"/>
                    <rect x="325" y="105" width="45" height="55" rx="2" fill="#243024"/>
                    <rect x="380" y="100" width="35" height="60" rx="2" fill="#1f2d1f"/>
                    <rect x="425" y="108" width="30" height="52" rx="2" fill="#243024"/>

                    <rect x="135" y="200" width="150" height="75" rx="4" fill="#1e2e20" stroke="#2ECC71" stroke-width="0.5" stroke-opacity="0.3"/>
                    <rect x="145" y="210" width="50" height="55" rx="2" fill="#243024"/>
                    <rect x="205" y="205" width="40" height="60" rx="2" fill="#1f2d1f"/>
                    <rect x="255" y="212" width="25" height="53" rx="2" fill="#243024"/>

                    <rect x="315" y="200" width="150" height="75" rx="4" fill="#1e2e20" stroke="#2ECC71" stroke-width="0.5" stroke-opacity="0.3"/>
                    <rect x="320" y="208" width="45" height="57" rx="2" fill="#243024"/>
                    <rect x="375" y="202" width="38" height="63" rx="2" fill="#1f2d1f"/>
                    <rect x="423" y="210" width="32" height="55" rx="2" fill="#243024"/>

                    {{-- Small block top-left & bottom --}}
                    <rect x="10" y="95" width="95" height="75" rx="4" fill="#1e2e20" stroke="#2ECC71" stroke-width="0.5" stroke-opacity="0.2"/>
                    <rect x="495" y="95" width="95" height="75" rx="4" fill="#1e2e20" stroke="#2ECC71" stroke-width="0.5" stroke-opacity="0.2"/>
                    <rect x="10" y="200" width="95" height="75" rx="4" fill="#1e2e20" stroke="#2ECC71" stroke-width="0.5" stroke-opacity="0.2"/>
                    <rect x="495" y="200" width="95" height="75" rx="4" fill="#1e2e20" stroke="#2ECC71" stroke-width="0.5" stroke-opacity="0.2"/>
                    <rect x="135" y="305" width="150" height="65" rx="4" fill="#1e2e20" stroke="#2ECC71" stroke-width="0.5" stroke-opacity="0.2"/>
                    <rect x="315" y="305" width="150" height="65" rx="4" fill="#1e2e20" stroke="#2ECC71" stroke-width="0.5" stroke-opacity="0.2"/>

                    {{-- Route path (dotted green) --}}
                    <path d="M 80 290 L 80 180 L 300 180 L 300 80 L 420 80" stroke="#2ECC71" stroke-width="2.5" fill="none" stroke-dasharray="10,6" opacity="0.7">
                        <animate attributeName="stroke-dashoffset" from="0" to="-32" dur="0.8s" repeatCount="indefinite"/>
                    </path>

                    {{-- Route path 2 --}}
                    <path d="M 520 290 L 480 290 L 480 180 L 300 180" stroke="#F39C12" stroke-width="2" fill="none" stroke-dasharray="8,8" opacity="0.5">
                        <animate attributeName="stroke-dashoffset" from="0" to="-32" dur="1.2s" repeatCount="indefinite"/>
                    </path>

                    {{-- Location pin: Pickup --}}
                    <g transform="translate(72,270)">
                        <circle cx="0" cy="0" r="14" fill="#2ECC71" opacity="0.2">
                            <animate attributeName="r" values="14;20;14" dur="2s" repeatCount="indefinite"/>
                            <animate attributeName="opacity" values="0.2;0;0.2" dur="2s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="0" cy="0" r="7" fill="#2ECC71"/>
                        <text x="0" y="4" text-anchor="middle" font-size="9" fill="#fff" font-weight="bold">P</text>
                    </g>
                    <rect x="86" y="255" width="52" height="16" rx="4" fill="#2ECC71"/>
                    <text x="112" y="267" text-anchor="middle" font-size="9" fill="#fff" font-weight="700">Pickup</text>

                    {{-- Location pin: Dropoff --}}
                    <g transform="translate(420,62)">
                        <circle cx="0" cy="0" r="14" fill="#E74C3C" opacity="0.2">
                            <animate attributeName="r" values="14;20;14" dur="2.5s" repeatCount="indefinite"/>
                            <animate attributeName="opacity" values="0.2;0;0.2" dur="2.5s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="0" cy="0" r="7" fill="#E74C3C"/>
                        <text x="0" y="4" text-anchor="middle" font-size="9" fill="#fff" font-weight="bold">D</text>
                    </g>
                    <rect x="430" y="47" width="56" height="16" rx="4" fill="#E74C3C"/>
                    <text x="458" y="59" text-anchor="middle" font-size="9" fill="#fff" font-weight="700">Drop-off</text>

                    {{-- Rider 1: bike moving along route --}}
                    <g id="rider1">
                        <animateMotion dur="6s" repeatCount="indefinite" rotate="auto">
                            <mpath href="#riderPath1"/>
                        </animateMotion>
                        <circle cx="0" cy="0" r="10" fill="#2ECC71" opacity="0.9"/>
                        {{-- Bike SVG simplified --}}
                        <circle cx="-4" cy="3" r="3" fill="none" stroke="#fff" stroke-width="1.2"/>
                        <circle cx="4" cy="3" r="3" fill="none" stroke="#fff" stroke-width="1.2"/>
                        <path d="M-4,3 L0,-2 L4,3" stroke="#fff" stroke-width="1.2" fill="none"/>
                        <circle cx="0" cy="-4" r="1.5" fill="#fff"/>
                    </g>
                    <path id="riderPath1" d="M 80 290 L 80 180 L 300 180 L 300 80 L 420 80" fill="none"/>

                    {{-- Rider 2: van on cross street --}}
                    <g id="rider2">
                        <animateMotion dur="9s" repeatCount="indefinite" rotate="auto">
                            <mpath href="#riderPath2"/>
                        </animateMotion>
                        <circle cx="0" cy="0" r="10" fill="#F39C12" opacity="0.9"/>
                        {{-- Van simplified --}}
                        <rect x="-5" y="-2" width="10" height="6" rx="1" fill="none" stroke="#fff" stroke-width="1.2"/>
                        <circle cx="-3" cy="4" r="1.5" fill="#fff"/>
                        <circle cx="3" cy="4" r="1.5" fill="#fff"/>
                        <rect x="2" y="-4" width="4" height="4" rx="0.5" fill="none" stroke="#fff" stroke-width="1"/>
                    </g>
                    <path id="riderPath2" d="M 520 290 L 480 290 L 480 180 L 300 180" fill="none"/>

                    {{-- Rider 3: tricycle on horizontal road --}}
                    <g id="rider3">
                        <animateMotion dur="12s" repeatCount="indefinite" rotate="auto">
                            <mpath href="#riderPath3"/>
                        </animateMotion>
                        <circle cx="0" cy="0" r="10" fill="#9B59B6" opacity="0.9"/>
                        {{-- Tricycle simplified --}}
                        <circle cx="-4" cy="3" r="2.5" fill="none" stroke="#fff" stroke-width="1.2"/>
                        <circle cx="4" cy="3" r="2.5" fill="none" stroke="#fff" stroke-width="1.2"/>
                        <circle cx="0" cy="4" r="2" fill="none" stroke="#fff" stroke-width="1"/>
                        <path d="M-4,1 L0,-3 L4,1" stroke="#fff" stroke-width="1.2" fill="none"/>
                    </g>
                    <path id="riderPath3" d="M 0 80 L 120 80 L 120 180 L 480 180 L 480 290 L 600 290" fill="none"/>

                    {{-- Legend --}}
                    <rect x="10" y="10" width="130" height="56" rx="6" fill="rgba(0,0,0,0.5)" stroke="rgba(46,204,113,0.3)" stroke-width="1"/>
                    <circle cx="26" cy="28" r="6" fill="#2ECC71"/>
                    <text x="38" y="32" font-size="10" fill="#fff" font-family="sans-serif">Bike Rider</text>
                    <circle cx="26" cy="46" r="6" fill="#F39C12"/>
                    <text x="38" y="50" font-size="10" fill="#fff" font-family="sans-serif">Van</text>
                    <circle cx="90" cy="28" r="6" fill="#9B59B6"/>
                    <text x="102" y="32" font-size="10" fill="#fff" font-family="sans-serif">Keke</text>

                    {{-- ETA badge --}}
                    <rect x="460" y="310" width="130" height="56" rx="8" fill="rgba(46,204,113,0.15)" stroke="#2ECC71" stroke-width="1"/>
                    <text x="525" y="332" text-anchor="middle" font-size="10" fill="rgba(255,255,255,0.6)" font-family="sans-serif">Estimated arrival</text>
                    <text x="525" y="352" text-anchor="middle" font-size="18" fill="#2ECC71" font-weight="bold" font-family="sans-serif">~35 min</text>

                    {{-- "LIVE" badge --}}
                    <rect x="480" y="12" width="50" height="20" rx="10" fill="#E74C3C"/>
                    <circle cx="492" cy="22" r="3" fill="#fff">
                        <animate attributeName="opacity" values="1;0.2;1" dur="1s" repeatCount="indefinite"/>
                    </circle>
                    <text x="515" y="27" text-anchor="middle" font-size="10" fill="#fff" font-weight="bold" font-family="sans-serif">LIVE</text>

                </svg>
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
document.addEventListener('DOMContentLoaded', function () {
    // Destroy any existing carousel init and reinitialize
    $('#heroAdCarousel').carousel({
        interval: 5000,
        ride: 'carousel'
    });

    // Force backgrounds back after theme JS runs
    function fixAdBackgrounds() {
        // Check if we're on mobile (screen width 768px or less)
        const isMobile = window.innerWidth <= 768;
        
        // Adjust carousel item height on mobile
        const carouselItems = document.querySelectorAll('#heroAdCarousel .carousel-item');
        if (isMobile) {
            carouselItems.forEach(function(item) {
                item.style.minHeight = 'auto';
                item.style.height = 'auto';
            });
        } else {
            carouselItems.forEach(function(item) {
                item.style.minHeight = '520px';
                item.style.height = '';
            });
        }
        
        document.querySelectorAll('#heroAdCarousel .ad-bg').forEach(function(el) {
            var bg = el.getAttribute('style');
            if (bg) {
                // Use 'contain' on mobile to show full image, 'cover' on desktop
                const bgSize = isMobile ? 'contain' : 'cover';
                
                el.style.cssText = bg + 
                    'position:absolute!important;' +
                    'top:0!important;left:0!important;' +
                    'width:100%!important;height:100%!important;' +
                    'background-size:' + bgSize + '!important;' +
                    'background-position:center!important;' +
                    'background-repeat:no-repeat!important;' +
                    'display:block!important;' +
                    'opacity:1!important;' +
                    'visibility:visible!important;' +
                    'z-index:0!important;';
            }
        });
        
        // Remove gap after hero on mobile
        if (isMobile) {
            const welcomeSection = document.querySelector('.welcome_area');
            if (welcomeSection) {
                welcomeSection.style.marginBottom = '0';
                welcomeSection.style.paddingBottom = '0';
            }
        }
    }
    // Run immediately and also after carousel slides
    fixAdBackgrounds();
    $('#heroAdCarousel').on('slide.bs.carousel slid.bs.carousel', fixAdBackgrounds);

    // Run again after 500ms to catch any late theme JS
    setTimeout(fixAdBackgrounds, 500);
    setTimeout(fixAdBackgrounds, 1000);
});
</script>
<script>
    // Re-initialize Feather icons after the page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
<script>
// ── Best Sellers / Top Rated tab switcher ─────────────────────────
function switchTab(tabId, btn) {
    // Deactivate all tabs and panels
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.querySelectorAll('.ord-tab-panel').forEach(function(p) { p.classList.remove('active'); });
    // Activate clicked
    btn.classList.add('active');
    var panel = document.getElementById('tab-' + tabId);
    if (panel) panel.classList.add('active');
}

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

document.querySelectorAll('.add-to-cart').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.dataset.product;
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.loadCart();
                window.cartToast('Item added to cart!');
            } else {
                window.cartToast(data.message ?? 'Could not add item.', 'error');
            }
        })
        .catch(() => {
            window.cartToast('Something went wrong.', 'error');
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
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(r => r.json())
        .then(data => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: data.added ? 'success' : 'info',
                title: data.message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
        })
        .catch(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Something went wrong.',
                showConfirmButton: false,
                timer: 2500,
            });
        });
        @else
        window.location.href = '{{ route("login") }}';
        @endauth
    });
});
</script>

</body>
</html>