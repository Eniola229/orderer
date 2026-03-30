{{-- ============================================================
       - Search results banner between the filter card and results
     ============================================================ --}}

@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Properties</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">

        {{-- Search Bar (unchanged) --}}
        <div class="row mb-4">
            <div class="col-12">
                <form action="{{ route('houses.index') }}" method="GET" class="search-form">
                    <div class="input-group" style="box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-radius: 50px;">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search by title, location, or description..."
                               value="{{ request('search') }}"
                               style="border-radius: 50px 0 0 50px; border-color: green; padding: 12px 20px;">
                        <button class="btn essence-btn" type="submit">
                            <i class="fa fa-search mr-2"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Filters Section (unchanged) --}}
        <div class="card mb-4" style="border: 1px solid #eee; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div class="card-body" style="padding: 20px;">
                <div class="row g-2 g-md-3">
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Listing Type</label>
                        <select name="listing_type" id="listing_type" class="form-select filter-select" onchange="applyFilters()" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px; background: #fff;">
                            <option value="">All Types</option>
                            <option value="sale" {{ request('listing_type') == 'sale' ? 'selected' : '' }}>For Sale</option>
                            <option value="rent" {{ request('listing_type') == 'rent' ? 'selected' : '' }}>For Rent</option>
                            <option value="shortlet" {{ request('listing_type') == 'shortlet' ? 'selected' : '' }}>Shortlet</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Property Type</label>
                        <select name="property_type" id="property_type" class="form-select filter-select" onchange="applyFilters()" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px; background: #fff;">
                            <option value="">All Types</option>
                            <option value="house" {{ request('property_type') == 'house' ? 'selected' : '' }}>House</option>
                            <option value="apartment" {{ request('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="condo" {{ request('property_type') == 'condo' ? 'selected' : '' }}>Condo</option>
                            <option value="land" {{ request('property_type') == 'land' ? 'selected' : '' }}>Land</option>
                            <option value="commercial" {{ request('property_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Min Price</label>
                        <input type="number" name="min_price" id="min_price" class="form-control filter-input"
                               placeholder="Min" value="{{ request('min_price') }}" onchange="applyFilters()"
                               style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Max Price</label>
                        <input type="number" name="max_price" id="max_price" class="form-control filter-input"
                               placeholder="Max" value="{{ request('max_price') }}" onchange="applyFilters()"
                               style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Bedrooms</label>
                        <select name="bedrooms" id="bedrooms" class="form-select filter-select" onchange="applyFilters()" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                            <option value="">Any</option>
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>{{ $i }}+ Bed</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Bathrooms</label>
                        <select name="bathrooms" id="bathrooms" class="form-select filter-select" onchange="applyFilters()" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                            <option value="">Any</option>
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}" {{ request('bathrooms') == $i ? 'selected' : '' }}>{{ $i }}+ Bath</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">City</label>
                        <input type="text" name="city" id="city" class="form-control filter-input"
                               placeholder="City" value="{{ request('city') }}" onchange="applyFilters()"
                               style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">State</label>
                        <input type="text" name="state" id="state" class="form-control filter-input"
                               placeholder="State" value="{{ request('state') }}" onchange="applyFilters()"
                               style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Sort By</label>
                        <select name="sort" id="sort" class="form-select filter-select" onchange="applyFilters()" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3 d-flex align-items-end">
                        <a href="{{ route('houses.index') }}" class="btn w-100" style="background: #f5f5f5; border-radius: 8px; padding: 8px 10px; font-size: 13px; font-weight: 500; color: #666; text-align: center;">
                            <i class="fa fa-refresh mr-1"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Results Count (unchanged) --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <p class="mb-0 text-muted" style="font-size: 14px;">Found <strong>{{ $houses->total() }}</strong> properties</p>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="border-radius: 8px; padding: 6px 12px; font-size: 13px;">
                    <i class="fa fa-eye mr-1"></i> View: {{ request('per_page', 12) }} per page
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="changePerPage(12)">12 per page</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changePerPage(24)">24 per page</a></li>
                    <li><a class="dropdown-item" href="#" onclick="changePerPage(48)">48 per page</a></li>
                </ul>
            </div>
        </div>

        {{-- ── SEARCH RESULTS BANNER AD ──────────────────────────────
             Placed between the results count row and the properties grid.
             Fetched inline since houses() doesn't pass ad vars currently.
             ──────────────────────────────────────────────────────── --}}
        @php
            $housesBannerAd = \App\Helpers\AdHelper::forSlot('search_results', 1)->first();
            if ($housesBannerAd) {
                \App\Helpers\AdHelper::recordImpression($housesBannerAd->id, auth('web')->id());
            }
        @endphp

        @if($housesBannerAd)
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ $housesBannerAd->clickTrackingUrl() }}"
                   style="display:block;position:relative;border-radius:10px;overflow:hidden;">
                    @if($housesBannerAd->media_type === 'image' && $housesBannerAd->media_url)
                    <img src="{{ $housesBannerAd->media_url }}"
                         alt="{{ $housesBannerAd->title }}"
                         style="width:100%;max-height:140px;object-fit:cover;border-radius:10px;">
                    @else
                    <div style="background:linear-gradient(135deg,#1a1a2e,#2ECC71);border-radius:10px;padding:24px 28px;display:flex;align-items:center;justify-content:space-between;">
                        <span style="color:#fff;font-size:17px;font-weight:700;">{{ $housesBannerAd->title }}</span>
                        <span style="background:#fff;color:#2ECC71;padding:7px 16px;border-radius:18px;font-size:13px;font-weight:700;">View</span>
                    </div>
                    @endif
                    <span style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,.55);color:#fff;font-size:10px;padding:2px 8px;border-radius:8px;">
                        Sponsored
                    </span>
                </a>
            </div>
        </div>
        @endif
        {{-- END BANNER AD --}}


        {{-- Properties Grid --}}
        <div class="row g-3 g-md-4">
            @forelse($houses as $house)
            @php $img = $house->images->where('is_primary',true)->first() ?? $house->images->first(); @endphp
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="property-card" style="border:1px solid #eee;border-radius:12px;overflow:hidden;height:100%;transition: transform 0.3s, box-shadow 0.3s;">
                    <div style="position:relative;">
                        <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}"
                             style="width:100%;height:200px;object-fit:cover;" alt="{{ $house->title }}">
                        <span style="position:absolute;top:10px;left:10px;background:#2ECC71;color:#fff;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:700;">
                            {{ ucfirst($house->listing_type) }}
                        </span>
                        <span style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,.6);color:#fff;padding:4px 10px;border-radius:12px;font-size:11px;">
                            {{ ucfirst($house->property_type) }}
                        </span>
                        {{-- ADDED: verified badge for property seller --}}
                        @if($house->seller->is_verified_business)
                        <span style="position:absolute;bottom:10px;left:10px;background:#2ECC71;color:#fff;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;display:flex;align-items:center;gap:3px;">
                            <i class="fa fa-check-circle" style="font-size:10px;"></i> Verified
                        </span>
                        @endif
                    </div>
                    <div style="padding:15px;">
                        <h6 style="font-weight:700;margin-bottom:6px;font-size:16px;">{{ Str::limit($house->title, 45) }}</h6>
                        <p style="color:#888;font-size:12px;margin-bottom:8px;">
                            <i class="fa fa-map-marker mr-1" style="color:#2ECC71;"></i>
                            {{ $house->city }}, {{ $house->state }}
                        </p>
                        <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:12px;font-size:12px;color:#666;">
                            @if($house->bedrooms !== null)
                            <span><i class="fa fa-bed mr-1"></i>{{ $house->bedrooms }} Bed</span>
                            @endif
                            @if($house->bathrooms !== null)
                            <span><i class="fa fa-bath mr-1"></i>{{ $house->bathrooms }} Bath</span>
                            @endif
                            @if($house->size_sqm)
                            <span><i class="fa fa-arrows-alt mr-1"></i>{{ $house->size_sqm }} m²</span>
                            @endif
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                            <div>
                                <span style="font-size:18px;font-weight:800;color:#2ECC71;">
                                    ${{ number_format($house->price, 0) }}
                                </span>
                                @if($house->listing_type == 'rent')
                                    <small class="text-muted">/month</small>
                                @endif
                            </div>
                            <a href="{{ route('houses.show', $house->slug) }}" class="btn essence-btn btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa fa-home" style="font-size:48px;color:#ddd;margin-bottom:16px;display:block;"></i>
                <p>No properties found matching your criteria.</p>
                <a href="{{ route('houses.index') }}" class="btn essence-btn mt-3">Clear Filters</a>
            </div>
            @endforelse
        </div>

        {{-- Pagination (unchanged) --}}
        <div class="mt-4">
            {{ $houses->appends(request()->query())->links() }}
        </div>

    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>

<style>
.filter-select,.filter-input{border-radius:8px;border:1px solid #e0e0e0;padding:8px 10px;font-size:13px;transition:all .3s;background-color:#fff;width:100%;}
.filter-select:hover,.filter-input:hover{border-color:#2ECC71;}
.filter-select:focus,.filter-input:focus{border-color:#2ECC71;outline:none;box-shadow:0 0 0 2px rgba(46,204,113,.1);}
.filter-select{appearance:none;background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 10px center;background-size:14px;padding-right:30px;}
.filter-input[type="number"]{-moz-appearance:textfield;}
.filter-input[type="number"]::-webkit-inner-spin-button,.filter-input[type="number"]::-webkit-outer-spin-button{-webkit-appearance:none;margin:0;}
.property-card{transition:transform .3s,box-shadow .3s;}
.property-card:hover{transform:translateY(-5px);box-shadow:0 10px 30px rgba(0,0,0,.1);}
@media(max-width:768px){.section-padding-80{padding-top:40px;padding-bottom:40px;}.filter-select,.filter-input{font-size:12px;padding:7px 8px;}.property-card img{height:180px;}.property-card h6{font-size:14px;}.breadcumb_area{padding:30px 0;}}
@media(max-width:576px){.property-card img{height:160px;}.filter-select,.filter-input{font-size:12px;padding:6px 8px;}.card-body{padding:15px;}}
</style>

<script>
function applyFilters() {
    let params = new URLSearchParams(window.location.search);
    const listing_type  = document.getElementById('listing_type').value;
    const property_type = document.getElementById('property_type').value;
    const min_price     = document.getElementById('min_price').value;
    const max_price     = document.getElementById('max_price').value;
    const bedrooms      = document.getElementById('bedrooms').value;
    const bathrooms     = document.getElementById('bathrooms').value;
    const city          = document.getElementById('city').value;
    const state         = document.getElementById('state').value;
    const sort          = document.getElementById('sort').value;
    const search        = params.get('search');
    if(search)        params.set('search', search);
    if(listing_type)  params.set('listing_type', listing_type);
    if(property_type) params.set('property_type', property_type);
    if(min_price)     params.set('min_price', min_price);
    if(max_price)     params.set('max_price', max_price);
    if(bedrooms)      params.set('bedrooms', bedrooms);
    if(bathrooms)     params.set('bathrooms', bathrooms);
    if(city)          params.set('city', city);
    if(state)         params.set('state', state);
    if(sort)          params.set('sort', sort);
    params.forEach((value, key) => { if(!value) params.delete(key); });
    window.location.href = window.location.pathname + '?' + params.toString();
}
function changePerPage(perPage) {
    let params = new URLSearchParams(window.location.search);
    params.set('per_page', perPage);
    params.delete('page');
    window.location.href = window.location.pathname + '?' + params.toString();
}
</script>
</body>
</html>