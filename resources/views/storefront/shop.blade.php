@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

{{-- ── MOBILE CATEGORY SCROLL BAR ─────────────────────────── --}}
<div class="d-block d-md-none mobile-cat-scroll-wrap">
    <div class="mobile-cat-scroll">
        <a href="{{ route('shop.index') }}"
           class="mobile-cat-pill {{ !$currentCategory ? 'active' : '' }}">
            All
        </a>
        @foreach($allCategories as $cat)
        <a href="{{ route('shop.category', $cat->slug) }}"
           class="mobile-cat-pill {{ $currentCategory && $currentCategory->id === $cat->id ? 'active' : '' }}">
            {{ $cat->name }}
        </a>
        @if($cat->subcategories->count() && $currentCategory && $currentCategory->id === $cat->id)
            @foreach($cat->subcategories as $sub)
            <a href="{{ route('shop.subcategory', [$cat->slug, $sub->slug]) }}"
               class="mobile-cat-pill sub {{ request()->route('subcategory') === $sub->slug ? 'active' : '' }}">
                ↳ {{ $sub->name }}
            </a>
            @endforeach
        @endif
        @endforeach
    </div>
</div>
{{-- END MOBILE CATEGORY SCROLL BAR --}}

{{-- Breadcrumb (unchanged) --}}
<div class="breadcumb_area bg-img" style="background-image: url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12">
                <div class="page-title text-center">
                    <h2>{{ $currentCategory ? $currentCategory->name : 'All Products' }}</h2>
                </div> 
            </div>
        </div>
    </div>
</div>

<section class="shop_grid_area section-padding-80">
    <div class="container">
        <div class="row">

            {{-- Sidebar with collapsible sections on mobile --}}
            <div class="col-12 col-md-4 col-lg-3">
                
                {{-- Mobile Filter Toggle Button --}}
                <div class="d-block d-md-none mb-3">
                    <button class="btn btn-outline-success w-100" type="button" id="mobileFilterToggle">
                        <i class="fa fa-filter me-2"></i> Show Filters
                    </button>
                </div>

                <div class="shop_sidebar_area" id="sidebarFilters">
                    
                    {{-- Mobile: Show filters inline on desktop, collapsible on mobile --}}
                    <div class="collapse-filter-section d-block d-md-block">
                        
                        {{-- Categories (desktop only) --}}
                        <div class="widget catagory mb-50 d-none d-md-block">
                            <h6 class="widget-title mb-30">Categories</h6>
                            <div class="catagories-menu">
                                <ul id="menu-content2" class="menu-content collapse show">
                                    @foreach($allCategories as $cat)
                                    <li data-toggle="collapse" data-target="#cat{{ $cat->id }}">
                                        <a href="{{ route('shop.category', $cat->slug) }}"
                                           class="{{ $currentCategory && $currentCategory->id === $cat->id ? 'font-weight-bold' : '' }}">
                                            {{ $cat->name }}
                                            <span class="float-right text-muted" style="font-size:12px;">
                                                ({{ $cat->products_count ?? 0 }})
                                            </span>
                                        </a>
                                        @if($cat->subcategories->count())
                                        <ul class="sub-menu collapse {{ $currentCategory && $currentCategory->id === $cat->id ? 'show' : '' }}"
                                            id="cat{{ $cat->id }}">
                                            @foreach($cat->subcategories as $sub)
                                            <li>
                                                <a href="{{ route('shop.subcategory', [$cat->slug, $sub->slug]) }}">
                                                    {{ $sub->name }}
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Price filter (collapsible on mobile - hidden by default) --}}
                        <div class="widget price mb-50 filter-widget">
                            <h6 class="widget-title mb-30 d-flex justify-content-between align-items-center" 
                                style="cursor: pointer;" onclick="toggleFilterSection(this)">
                                Filter by Price
                                <i class="fa fa-chevron-down d-md-none filter-toggle-icon"></i>
                            </h6>
                            <div class="widget-desc filter-content collapsed">
                                <form action="{{ request()->url() }}" method="GET" id="filterForm">
                                    @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                                    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                                    <div class="widget-desc">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">₦</span>
                                                <input type="number" name="min_price" class="form-control"
                                                       placeholder="Min" value="{{ request('min_price') }}"
                                                       min="0" step="0.01">
                                            </div>
                                            <span class="text-muted">—</span>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">₦</span>
                                                <input type="number" name="max_price" class="form-control"
                                                       placeholder="Max" value="{{ request('max_price') }}"
                                                       min="0" step="0.01">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn essence-btn btn-sm w-100">Apply Filter</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Condition filter (collapsible on mobile - hidden by default) --}}
                        <div class="widget mb-50 filter-widget">
                            <h6 class="widget-title mb-30 d-flex justify-content-between align-items-center" 
                                style="cursor: pointer;" onclick="toggleFilterSection(this)">
                                Condition
                                <i class="fa fa-chevron-down d-md-none filter-toggle-icon"></i>
                            </h6>
                            <div class="widget-desc filter-content collapsed">
                                @foreach(['new' => 'New', 'used' => 'Used', 'refurbished' => 'Refurbished'] as $val => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input condition-check" type="checkbox"
                                           value="{{ $val }}" id="cond_{{ $val }}"
                                           {{ in_array($val, (array)request('condition', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cond_{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Brands filter (collapsible on mobile - hidden by default) --}}
                        @if($brands->count())
                        <div class="widget brands mb-50 filter-widget">
                            <h6 class="widget-title mb-30 d-flex justify-content-between align-items-center" 
                                style="cursor: pointer;" onclick="toggleFilterSection(this)">
                                Brands
                                <i class="fa fa-chevron-down d-md-none filter-toggle-icon"></i>
                            </h6>
                            <div class="widget-desc filter-content collapsed">
                                <ul>
                                    @foreach($brands as $brand)
                                    <li>
                                        <a href="{{ route('brands.show', $brand->slug) }}">
                                            {{ $brand->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif

                    </div>
                    
                </div>
            </div>

            {{-- Products grid --}}
            <div class="col-12 col-md-8 col-lg-9">
                <div class="shop_grid_product_area">

                    {{-- Sort topbar (unchanged) --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="product-topbar d-flex align-items-center justify-content-between">
                             <!--    <div class="total-products">
                                    <p><span style="color: green;">{{ $products->total() }}</span> products found</p>
                                </div> -->
                                <div class="product-sorting d-flex align-items-center">
                                    <p class="mb-0 mr-2">Sort by:</p>
                                    <form action="{{ request()->url() }}" method="GET">
                                        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                                        @if(request('min_price'))<input type="hidden" name="min_price" value="{{ request('min_price') }}">@endif
                                        @if(request('max_price'))<input type="hidden" name="max_price" value="{{ request('max_price') }}">@endif
                                        <select name="sort" id="sortByselect" onchange="this.form.submit()">
                                            <option value="latest"    {{ request('sort','latest') === 'latest'   ? 'selected' : '' }}>Latest</option>
                                            <option value="price_asc" {{ request('sort') === 'price_asc'         ? 'selected' : '' }}>Price: Low to High</option>
                                            <option value="price_desc"{{ request('sort') === 'price_desc'        ? 'selected' : '' }}>Price: High to Low</option>
                                            <option value="rating"    {{ request('sort') === 'rating'            ? 'selected' : '' }}>Highest Rated</option>
                                            <option value="popular"   {{ request('sort') === 'popular'           ? 'selected' : '' }}>Most Popular</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── SEARCH/CATEGORY BANNER AD ─────────────────────────────
                         Shows one full-width banner between topbar and products.
                         Uses 'search_results' slot on shop, 'category_page' on category.
                         ──────────────────────────────────────────────────────── --}}
                    @php
                        $bannerAd = isset($categoryBannerAds) && $categoryBannerAds->count()
                            ? $categoryBannerAds->first()
                            : (isset($searchBannerAds) && $searchBannerAds->count()
                                ? $searchBannerAds->first()
                                : null);
                    @endphp

                    @if($bannerAd)
                    <div class="row mb-30">
                        <div class="col-12">
                            <a href="{{ $bannerAd->clickTrackingUrl() }}"
                               style="display:block;position:relative;border-radius:10px;overflow:hidden;">

                                @if($bannerAd->media_type === 'image' && $bannerAd->media_url)
                                <img src="{{ $bannerAd->media_url }}"
                                     alt="{{ $bannerAd->title }}"
                                     style="width:100%;max-height:150px;object-fit:cover;border-radius:10px;">
                                @elseif($bannerAd->media_type === 'video' && $bannerAd->media_url)
                                <video autoplay muted loop playsinline
                                       style="width:100%;max-height:150px;object-fit:cover;border-radius:10px;">
                                    <source src="{{ $bannerAd->media_url }}">
                                </video>
                                @else
                                {{-- Text-only fallback banner --}}
                                <div style="background:linear-gradient(135deg,#1a1a2e,#2ECC71);border-radius:10px;padding:28px 32px;display:flex;align-items:center;justify-content:space-between;">
                                    <span style="color:#fff;font-size:18px;font-weight:700;">
                                        {{ $bannerAd->title }}
                                    </span>
                                    <span style="background:#fff;color:#2ECC71;padding:8px 18px;border-radius:20px;font-size:13px;font-weight:700;">
                                        Shop Now
                                    </span>
                                </div>
                                @endif

                                {{-- Sponsored label --}}
                                <span style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,.55);color:#fff;font-size:10px;padding:2px 8px;border-radius:8px;letter-spacing:.5px;">
                                    Sponsored
                                </span>
                            </a>
                        </div>
                    </div>
                    @endif
                    {{-- END BANNER AD --}}


                    {{-- ── SPONSORED PRODUCT CARDS (top_listing) ────────────────
                         Shown only on first page. Placed before regular products.
                         ──────────────────────────────────────────────────────── --}}
                    @if(isset($topListingAds) && $topListingAds->count() && $products->currentPage() === 1)
                    <div class="row mb-2">
                        <div class="col-12">
                            <p style="font-size:12px;color:#aaa;margin-bottom:8px;letter-spacing:.5px;text-transform:uppercase;font-weight:600;">
                                <i class="fa fa-star" style="color:#F39C12;"></i>
                                Sponsored
                            </p>
                        </div>
                        @foreach($topListingAds as $ad)
                        @php
                            $sp   = $ad->promotable;
                            if (!$sp) continue;
                            $spImg = $sp->images->where('is_primary',true)->first() ?? $sp->images->first();
                        @endphp
                        <div class="col-6 col-sm-6 col-lg-4">
                            <div class="single-product-wrapper" style="position:relative;">
                                <div style="position:absolute;top:8px;left:8px;z-index:3;background:#FEF9E7;color:#B7950B;border:1px solid #F9CA24;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;">
                                    Sponsored
                                </div>
                                <div class="product-img">
                                    <a href="{{ $ad->clickTrackingUrl() }}">
                                        <img src="{{ $spImg->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                                    </a>
                                    @if($sp->seller->is_verified_business)
                                    <div class="product-badge" style="background:#2ECC71;top:50px;left:20px;display:flex;align-items:center;gap:4px;">
                                        <i class="fa fa-check-circle" style="font-size:10px;"></i> Verified
                                    </div>
                                    @endif
                                    <div class="product-favourite">
                                        <a href="#" class="favme fa fa-heart" data-product="{{ $sp->id }}"></a>
                                    </div>
                                </div>
                                <div class="product-description">
                                    <span>{{ Str::limit($sp->seller->business_name ?? '', 10) }}</span>
                                    <a href="{{ $ad->clickTrackingUrl() }}">
                                        <h6>{{ Str::limit($sp->name, 40) }}</h6>
                                    </a>
                                    <p class="product-price">
                                        @if($sp->sale_price)
                                            <span class="old-price">₦{{ number_format($sp->price, 2) }}</span>
                                            ₦{{ number_format($sp->sale_price, 2) }}
                                        @else
                                            ₦{{ number_format($sp->price, 2) }}
                                        @endif
                                    </p>
                                    <div class="hover-content">
                                        <div class="add-to-cart-btn">
                                            <a href="#" class="btn essence-btn add-to-cart"
                                               data-product="{{ $sp->id }}">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        {{-- Divider between sponsored and organic --}}
                        <div class="col-12">
                            <hr style="border-color:#eee;margin:8px 0 16px;">
                        </div>
                    </div>
                    @endif
                    {{-- END SPONSORED PRODUCTS --}}

                    {{-- ── Best Sellers & Top Rated (tabbed) ───────────────────────── --}}
                    @if(isset($bestSellers) && $bestSellers->count() || isset($topRatedProducts) && $topRatedProducts->count())
                    <section style="background:#f4fdf8;padding:48px 0;margin-bottom:40px;">
                        <div class="container">

                            {{-- Section header with tabs --}}
                            <div class="row align-items-center mb-0">
                                <div class="col-12">
                                    <div class="ord-section-tabs">
                                        @if(isset($bestSellers) && $bestSellers->count())
                                        <button class="tab-btn active" onclick="switchTab('best-sellers', this)">
                                            <i class="fa fa-fire" style="color:#FF6B00;margin-right:6px;"></i> Best Sellers
                                        </button>
                                        @endif
                                        @if(isset($topRatedProducts) && $topRatedProducts->count())
                                        <button class="tab-btn {{ !isset($bestSellers) || !$bestSellers->count() ? 'active' : '' }}" onclick="switchTab('top-rated', this)">
                                            <i class="fa fa-star" style="color:#F39C12;margin-right:6px;"></i> Top Rated
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Best Sellers panel --}}
                            @if(isset($bestSellers) && $bestSellers->count())
                            <div id="tab-best-sellers" class="ord-tab-panel active">
                                <div class="row">
                                    @foreach($bestSellers as $product)
                                    @php $img = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
                                    <div class="col-6 col-sm-6 col-lg-4 mb-4">
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
                                                    @if($img && $product->images->skip(1)->first())
                                                    <img class="hover-img" src="{{ $product->images->skip(1)->first()->image_url }}" alt="">
                                                    @endif
                                                </a>
                                                @if($product->sale_price)
                                                <div class="product-badge offer-badge">
                                                    <span>-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
                                                </div>
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
                                                @if($product->total_sold)
                                                <p style="font-size:12px;color:#888;margin:4px 0;">
                                                    <i class="fa fa-shopping-bag" style="color:#FF6B00;font-size:11px;"></i>
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
                            @if(isset($topRatedProducts) && $topRatedProducts->count())
                            <div id="tab-top-rated" class="ord-tab-panel {{ !isset($bestSellers) || !$bestSellers->count() ? 'active' : '' }}">
                                <div class="row">
                                    @foreach($topRatedProducts as $product)
                                    @php $img = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
                                    <div class="col-12 col-sm-6 col-lg-4 mb-4">
                                        <div class="single-product-wrapper">
                                            <div class="product-img">
                                                <a href="{{ route('product.show', $product->slug) }}">
                                                    <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                                                    @if($img && $product->images->skip(1)->first())
                                                    <img class="hover-img" src="{{ $product->images->skip(1)->first()->image_url }}" alt="">
                                                    @endif
                                                </a>
                                                @if($product->sale_price)
                                                <div class="product-badge offer-badge">
                                                    <span>-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
                                                </div>
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
                                                <div style="display:flex;align-items:center;gap:6px;margin:4px 0;">
                                                    <span style="color:#F39C12;font-size:12px;letter-spacing:1px;">
                                                        @for($s = 1; $s <= 5; $s++)
                                                            {{ $s <= round($product->average_rating) ? '★' : '☆' }}
                                                        @endfor
                                                    </span>
                                                    <span style="font-size:11px;color:#888;">
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

                    {{-- Regular product grid --}}
                    <div class="row">
                        @forelse($products as $product)
                        @php $img = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
                        <div class="col-6 col-sm-6 col-lg-4">
                            <div class="single-product-wrapper">
                                <div class="product-img">
                                    <a href="{{ route('product.show', $product->slug) }}">
                                        <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                                        @if($img)
                                        <img class="hover-img"
                                             src="{{ $product->images->skip(1)->first()->image_url ?? $img->image_url }}"
                                             alt="">
                                        @endif
                                    </a>
                                    @if($product->sale_price)
                                    <div class="product-badge offer-badge">
                                        <span>-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
                                    </div>
                                    @endif
                                    @if($product->created_at->diffInDays() <= 7)
                                    <div class="product-badge new-badge"><span>New</span></div>
                                    @endif
                                    {{-- ADDED: verified badge for regular products --}}
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
                                        <h6>{{ Str::limit($product->name, 40) }}</h6>
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
                        @empty
                        <div class="col-12 text-center py-5">
                            <i class="fa fa-search" style="font-size:48px;color:#ddd;"></i>
                            <p class="mt-3 text-muted">No products found matching your criteria.</p>
                            <a href="{{ route('shop.index') }}" class="btn essence-btn mt-2">Clear Filters</a>
                        </div>
                        @endforelse
                    </div>

                    {{-- Pagination (unchanged) --}}
                    @if($products->hasPages())
                    <nav aria-label="navigation">
                        <ul class="pagination mt-50 mb-70">
                            <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $products->previousPageUrl() }}">
                                    <i class="fa fa-angle-left"></i>
                                </a>
                            </li>
                            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            <li class="page-item {{ $products->currentPage() === $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                            @endforeach
                            <li class="page-item {{ !$products->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $products->nextPageUrl() }}">
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    @endif

                </div>
            </div>
        </div>
    </div>
</section>

@include('layouts.storefront.footer')

<style>
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

    /* ── Mobile horizontal category scroll ── */
    .mobile-cat-scroll-wrap {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
    }
    .mobile-cat-scroll {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 10px 16px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .mobile-cat-scroll::-webkit-scrollbar { display: none; }

    .mobile-cat-pill {
        display: inline-block;
        white-space: nowrap;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        color: #555;
        background: #f4f4f4;
        border: 1.5px solid transparent;
        text-decoration: none;
        transition: all 0.18s;
        flex-shrink: 0;
    }
    .mobile-cat-pill.active {
        background: #2ECC71;
        color: #fff;
        border-color: #2ECC71;
        font-weight: 700;
    }
    .mobile-cat-pill.sub {
        font-size: 12px;
        background: #eafaf1;
        color: #27ae60;
    }
    .mobile-cat-pill.sub.active {
        background: #27ae60;
        color: #fff;
    }

    /* Mobile filter collapsible styles */
    @media (max-width: 767.98px) {
        .filter-widget .widget-title {
            padding: 12px 0;
            margin-bottom: 0 !important;
            border-bottom: 1px solid #f0f0f0;
        }
        .filter-widget .filter-content {
            padding: 12px 0;
            display: block;
        }
        .filter-widget .filter-content.collapsed {
            display: none;
        }
        .filter-toggle-icon {
            transition: transform 0.3s ease;
        }
        .filter-toggle-icon.rotated {
            transform: rotate(180deg);
        }
    }
</style>

<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
<script>
function switchTab(tabId, btn) {
    // Deactivate all tabs and panels
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.querySelectorAll('.ord-tab-panel').forEach(function(p) { p.classList.remove('active'); });
    // Activate clicked
    btn.classList.add('active');
    var panel = document.getElementById('tab-' + tabId);
    if (panel) panel.classList.add('active');
}

// Mobile filter toggle functionality
function toggleFilterSection(element) {
    // Only work on mobile devices
    if (window.innerWidth <= 767) {
        const widget = element.closest('.filter-widget');
        const content = widget.querySelector('.filter-content');
        const icon = element.querySelector('.filter-toggle-icon');
        
        if (content.classList.contains('collapsed')) {
            content.classList.remove('collapsed');
            if (icon) icon.classList.remove('rotated');
        } else {
            content.classList.add('collapsed');
            if (icon) icon.classList.add('rotated');
        }
    }
}

// Mobile sidebar toggle - hidden by default
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.getElementById('mobileFilterToggle');
    const sidebar = document.getElementById('sidebarFilters');

    if (mobileToggle && sidebar) {
        // Hide sidebar only on mobile initially
        if (window.innerWidth <= 767) {
            sidebar.style.display = 'none';
        }

        mobileToggle.addEventListener('click', function() {
            if (sidebar.style.display === 'none' || sidebar.style.display === '') {
                sidebar.style.display = 'block';
                mobileToggle.innerHTML = '<i class="fa fa-filter me-2"></i> Hide Filters';
            } else {
                sidebar.style.display = 'none';
                mobileToggle.innerHTML = '<i class="fa fa-filter me-2"></i> Show Filters';
            }
        });
    }
});
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