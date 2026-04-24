{{-- ============================================================
       1. Search/category banner — between topbar and product grid
       2. Sponsored products — first 4 items in grid marked "Sponsored"
     ============================================================ --}}

@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

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

            {{-- Sidebar (unchanged) --}}
            <div class="col-12 col-md-4 col-lg-3">
                <div class="shop_sidebar_area">

                    {{-- Categories --}}
                    <div class="widget catagory mb-50">
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

                    {{-- Price filter --}}
                    <div class="widget price mb-50">
                        <h6 class="widget-title mb-30">Filter by Price</h6>
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

                    {{-- Condition --}}
                    <div class="widget mb-50">
                        <h6 class="widget-title mb-30">Condition</h6>
                        <div class="widget-desc">
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

                    {{-- Brands sidebar --}}
                    @if($brands->count())
                    <div class="widget brands mb-50">
                        <h6 class="widget-title mb-30">Brands</h6>
                        <div class="widget-desc">
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

            {{-- Products grid --}}
            <div class="col-12 col-md-8 col-lg-9">
                <div class="shop_grid_product_area">

                    {{-- Sort topbar (unchanged) --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="product-topbar d-flex align-items-center justify-content-between">
                                <div class="total-products">
                                    <p><span style="color: green;">{{ $products->total() }}</span> products found</p>
                                </div>
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
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="single-product-wrapper" style="position:relative;">
                                <div style="position:absolute;top:8px;left:8px;z-index:3;background:#FEF9E7;color:#B7950B;border:1px solid #F9CA24;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;">
                                    Sponsored
                                </div>
                                <div class="product-img">
                                    <a href="{{ $ad->clickTrackingUrl() }}">
                                        <img src="{{ $spImg->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                                    </a>
                                    {{-- ADDED: verified badge for sponsored products --}}
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


                    {{-- Regular product grid --}}
                    <div class="row">
                        @forelse($products as $product)
                        @php $img = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
                        <div class="col-12 col-sm-6 col-lg-4">
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

<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
<script>
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