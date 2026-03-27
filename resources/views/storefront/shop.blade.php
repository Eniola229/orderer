@auth('web')
    @include('layouts.storefront.header-auth')
@else
    @include('layouts.storefront.header-guest')
@endauth

@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

{{-- Breadcrumb --}}
<div class="breadcumb_area bg-img" style="background-image: url({{ asset('img/bg-img/breadcumb.jpg') }});">
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

            {{-- Sidebar --}}
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
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="min_price" class="form-control"
                                               placeholder="Min" value="{{ request('min_price') }}"
                                               min="0" step="0.01">
                                    </div>
                                    <span class="text-muted">—</span>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
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
                    <div class="row">
                        <div class="col-12">
                            <div class="product-topbar d-flex align-items-center justify-content-between">
                                <div class="total-products">
                                    <p><span>{{ $products->total() }}</span> products found</p>
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
                                    <div class="product-favourite">
                                        <a href="#" class="favme fa fa-heart" data-product="{{ $product->id }}"></a>
                                    </div>
                                </div>
                                <div class="product-description">
                                    <span>{{ $product->seller->business_name ?? '' }}</span>
                                    <a href="{{ route('product.show', $product->slug) }}">
                                        <h6>{{ Str::limit($product->name, 40) }}</h6>
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
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <i class="fa fa-search" style="font-size:48px;color:#ddd;"></i>
                            <p class="mt-3 text-muted">No products found matching your criteria.</p>
                            <a href="{{ route('shop.index') }}" class="btn essence-btn mt-2">Clear Filters</a>
                        </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
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
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ product_id: this.dataset.product, quantity: 1 })
        }).then(r => r.json()).then(data => {
            if (data.success) {
                document.querySelectorAll('#cart-count, #cart-count-sidebar').forEach(el => el.textContent = data.count);
            }
        });
    });
});
document.querySelectorAll('.favme').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        @auth('web')
        const pid = this.dataset.product;
        this.classList.toggle('active');
        fetch('{{ route("buyer.wishlist.toggle") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ product_id: pid })
        });
        @else
        window.location.href = '{{ route("login") }}';
        @endauth
    });
});
</script>
</body>
</html>
