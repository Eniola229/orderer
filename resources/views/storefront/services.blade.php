@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Services</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">
        
        {{-- Search Bar --}}
        <div class="row mb-4">
            <div class="col-12">
                <form action="{{ route('services.index') }}" method="GET" class="search-form">
                    <div class="input-group" style="box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-radius: 50px;">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search services by title, description, or category..." 
                               value="{{ request('search') }}"
                               style="border-radius: 50px 0 0 50px; border-color: green; padding: 12px 20px;">
                        <button class="btn essence-btn" type="submit" >
                            <i class="fa fa-search mr-2"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="card mb-4" style="border: 1px solid #eee; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div class="card-body" style="padding: 20px;">
                <div class="row g-2 g-md-3">
                    {{-- Category Filter --}}
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Category</label>
                        <select name="category_id" id="category_id" class="form-select filter-select" onchange="applyFilters()" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Pricing Type Filter --}}
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Pricing</label>
                        <select name="pricing_type" id="pricing_type" class="form-select filter-select" onchange="applyFilters()" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                            <option value="">All Types</option>
                            <option value="fixed" {{ request('pricing_type') == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                            <option value="hourly" {{ request('pricing_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="negotiable" {{ request('pricing_type') == 'negotiable' ? 'selected' : '' }}>Negotiable</option>
                        </select>
                    </div>
                    
                    {{-- Price Range Filter --}}
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
                    
                    {{-- Delivery Time Filter --}}
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Delivery Time</label>
                        <select name="delivery_time" id="delivery_time" class="form-select filter-select" onchange="applyFilters()" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                            <option value="">Any Time</option>
                            <option value="24h" {{ request('delivery_time') == '24h' ? 'selected' : '' }}>Within 24 Hours</option>
                            <option value="3days" {{ request('delivery_time') == '3days' ? 'selected' : '' }}>1-3 Days</option>
                            <option value="week" {{ request('delivery_time') == 'week' ? 'selected' : '' }}>1 Week</option>
                            <option value="2weeks" {{ request('delivery_time') == '2weeks' ? 'selected' : '' }}>2+ Weeks</option>
                        </select>
                    </div>
                    
                    {{-- Rating Filter --}}
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Rating</label>
                        <select name="rating" id="rating" class="form-select filter-select" onchange="applyFilters()" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                            <option value="">Any Rating</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4★ & Above</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3★ & Above</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2★ & Above</option>
                        </select>
                    </div>
                    
                    {{-- Sort By --}}
                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold" style="font-size: 12px; margin-bottom: 5px; color: #666;">Sort By</label>
                        <select name="sort" id="sort" class="form-select filter-select" onchange="applyFilters()" style="border-radius: 8px; border: 1px solid #e0e0e0; padding: 8px 10px; font-size: 13px;">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                    </div>
                    
                    {{-- Reset Filters Button --}}
                    <div class="col-6 col-md-4 col-lg-3 d-flex align-items-end">
                        <a href="{{ route('services.index') }}" class="btn w-100" style="background: #f5f5f5; border-radius: 8px; padding: 8px 10px; font-size: 13px; font-weight: 500; color: #666; text-align: center;">
                            <i class="fa fa-refresh mr-1"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Results Count & View Options --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <p class="mb-0 text-muted" style="font-size: 14px;">Found <strong>{{ $services->total() }}</strong> services</p>
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

        {{-- Services Grid --}}
        <div class="row g-3 g-md-4">
            @forelse($services as $service)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="service-card" style="border:1px solid #eee;border-radius:12px;overflow:hidden;height:100%;transition: transform 0.3s, box-shadow 0.3s;">
                    <div style="position:relative;">
                        @if($service->portfolio_images && count($service->portfolio_images))
                            <img src="{{ $service->portfolio_images[0]['url'] }}"
                                 style="width:100%;height:200px;object-fit:cover;" alt="{{ $service->title }}">
                        @else
                            <div style="width:100%;height:200px;background:#f0faf5;display:flex;align-items:center;justify-content:center;">
                                <i class="fa fa-cogs" style="font-size:48px;color:#2ECC71;opacity:.4;"></i>
                            </div>
                        @endif
                        @if($service->average_rating)
                            <span style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,.7);color:#FFA500;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                                <i class="fa fa-star"></i> {{ number_format($service->average_rating, 1) }}
                            </span>
                        @endif
                        {{-- ADDED: verified badge for service seller --}}
                        @if($service->seller->is_verified_business)
                            <span style="position:absolute;top:10px;left:10px;background:#2ECC71;color:#fff;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;display:flex;align-items:center;gap:3px;">
                                <i class="fa fa-check-circle" style="font-size:10px;"></i> Verified
                            </span>
                        @endif
                    </div>
                    <div style="padding:15px;">
                        <span style="font-size:11px;color:#2ECC71;font-weight:600;">{{ $service->category->name ?? 'Uncategorized' }}</span>
                        <h6 style="font-weight:700;margin:8px 0;font-size:16px;">{{ Str::limit($service->title, 50) }}</h6>
                        <p style="color:#888;font-size:12px;margin-bottom:12px;line-height:1.5;">{{ Str::limit($service->description, 80) }}</p>
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                            <div>
                                @if($service->pricing_type === 'negotiable')
                                    <span style="font-weight:700;color:#1a1a1a;font-size:16px;">Negotiable</span>
                                @else
                                    <span style="font-weight:800;color:#2ECC71;font-size:18px;">
                                        ₦{{ number_format($service->price, 0) }}
                                    </span>
                                    @if($service->pricing_type === 'hourly')
                                        <small style="color:#888;">/hr</small>
                                    @endif
                                @endif
                            </div>
                            @if($service->delivery_time)
                                <small style="color:#888;font-size:11px;"><i class="fa fa-clock-o mr-1"></i>{{ $service->delivery_time }}</small>
                            @endif
                        </div>
                        <div style="padding-top:12px;border-top:1px solid #f5f5f5;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($service->seller->avatar)
                                    <img src="{{ $service->seller->avatar }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;" alt="">
                                @else
                                    <div style="width:28px;height:28px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;">
                                        {{ strtoupper(substr($service->seller->business_name ?? 'S', 0, 1)) }}
                                    </div>
                                @endif
                                <small style="color:#666;font-weight:500;">{{ Str::limit($service->seller->business_name ?? 'Seller', 20) }}</small>
                            </div>
                            <a href="{{ route('services.show', $service->slug) }}" class="btn essence-btn btn-sm" style="padding: 5px 12px; font-size: 11px;">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa fa-cogs" style="font-size:48px;color:#ddd;margin-bottom:16px;display:block;"></i>
                <p>No services found matching your criteria.</p>
                <a href="{{ route('services.index') }}" class="btn essence-btn mt-3">Clear Filters</a>
            </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        <div class="mt-4">
            {{ $services->appends(request()->query())->links() }}
        </div>
    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>

<style>
/* Improved filter styles */
.filter-select,
.filter-input {
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    padding: 8px 10px;
    font-size: 13px;
    transition: all 0.3s ease;
    background-color: #fff;
    width: 100%;
}

.filter-select:hover,
.filter-input:hover {
    border-color: #2ECC71;
}

.filter-select:focus,
.filter-input:focus {
    border-color: #2ECC71;
    outline: none;
    box-shadow: 0 0 0 2px rgba(46, 204, 113, 0.1);
}

/* Style for select dropdown arrow */
.filter-select {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 14px;
    padding-right: 30px;
}

/* Remove number input arrows */
.filter-input[type="number"] {
    -moz-appearance: textfield;
}

.filter-input[type="number"]::-webkit-inner-spin-button,
.filter-input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Service card hover effect */
.service-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .section-padding-80 {
        padding-top: 40px;
        padding-bottom: 40px;
    }
    
    .filter-select,
    .filter-input {
        font-size: 12px;
        padding: 7px 8px;
    }
    
    .service-card img,
    .service-card div[style*="height:200px"] {
        height: 160px;
    }
    
    .service-card h6 {
        font-size: 14px;
    }
    
    .breadcumb_area {
        padding: 30px 0;
    }
}

@media (max-width: 576px) {
    .service-card img,
    .service-card div[style*="height:200px"] {
        height: 140px;
    }
    
    .filter-select,
    .filter-input {
        font-size: 11px;
        padding: 6px 8px;
    }
    
    .card-body {
        padding: 15px;
    }
}

/* Better grid spacing */
.row.g-3 {
    --bs-gutter-y: 1rem;
    --bs-gutter-x: 1rem;
}

@media (min-width: 768px) {
    .row.g-md-4 {
        --bs-gutter-y: 1.5rem;
        --bs-gutter-x: 1.5rem;
    }
}
</style>

<script>
function applyFilters() {
    let params = new URLSearchParams(window.location.search);
    
    // Get all filter values
    const category_id = document.getElementById('category_id').value;
    const pricing_type = document.getElementById('pricing_type').value;
    const min_price = document.getElementById('min_price').value;
    const max_price = document.getElementById('max_price').value;
    const delivery_time = document.getElementById('delivery_time').value;
    const rating = document.getElementById('rating').value;
    const sort = document.getElementById('sort').value;
    const search = params.get('search');
    
    // Build new URL
    if(search) params.set('search', search);
    if(category_id) params.set('category_id', category_id);
    if(pricing_type) params.set('pricing_type', pricing_type);
    if(min_price) params.set('min_price', min_price);
    if(max_price) params.set('max_price', max_price);
    if(delivery_time) params.set('delivery_time', delivery_time);
    if(rating) params.set('rating', rating);
    if(sort) params.set('sort', sort);
    
    // Remove empty parameters
    params.forEach((value, key) => {
        if(!value) params.delete(key);
    });
    
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