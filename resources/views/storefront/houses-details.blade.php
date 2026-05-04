@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center">
            <h2>{{ $house->title }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ route('houses.index') }}">Properties</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($house->title, 30) }}</li>
                </ol>
            </nav>
            <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-top:10px;flex-wrap:wrap;">
                <button onclick="sharePropertyNative()" style="background:none;border:1px solid #2ECC71;border-radius:50px;padding:6px 20px;font-size:13px;color:#2ECC71;cursor:pointer;">
                    <i class="fa fa-share-alt mr-1"></i> Share
                </button>
                <button onclick="document.getElementById('supportModal').style.display='flex'" style="background:none;border:1px solid #E74C3C;border-radius:50px;padding:6px 20px;font-size:13px;color:#E74C3C;cursor:pointer;">
                    <i class="fa fa-flag mr-1"></i> Report Problem
                </button>
            </div>
        </div>
    </div></div></div>
</div>

<style>
    .property-gallery {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
    }
    .main-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .main-image:hover {
        transform: scale(1.02);
    }
    .thumbnail-list {
        display: flex;
        gap: 12px;
        margin-top: 15px;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .thumbnail {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s;
    }
    .thumbnail.active {
        border-color: #2ECC71;
        transform: scale(1.05);
    }
    .thumbnail:hover {
        border-color: #2ECC71;
    }
    .info-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #eee;
    }
    .info-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .info-value {
        font-size: 16px;
        font-weight: 500;
        color: #212529;
    }
    .feature-badge {
        background: #f8f9fa;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        margin: 0 5px 5px 0;
        display: inline-block;
    }
    .share-btn {
        cursor: pointer;
        transition: all 0.3s;
    }
    .share-btn:hover {
        transform: translateY(-2px);
    }
    .modal-image {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }
    .contact-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .whatsapp-btn {
        background: #25D366;
        color: white;
        padding: 12px;
        border-radius: 50px;
        display: inline-block;
        width: 100%;
        transition: all 0.3s;
        text-decoration: none;
    }
    .whatsapp-btn:hover {
        background: #128C7E;
        color: white;
        transform: translateY(-2px);
    }
    .similar-card {
        transition: all 0.3s;
    }
    .similar-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .rating-stars {
        color: #FFA500;
        font-size: 14px;
    }
    .seller-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }
    .seller-avatar-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #2ECC71;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
    }
    .seller-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }
    
    @media (max-width: 768px) {
        .main-image {
            height: 350px;
        }
        .thumbnail {
            width: 70px;
            height: 60px;
        }
        .info-value.fs-3 {
            font-size: 1.5rem !important;
        }
        .contact-card, .seller-card {
            margin-top: 15px;
        }
    }
    
    @media (max-width: 576px) {
        .main-image {
            height: 250px;
        }
        .thumbnail {
            width: 60px;
            height: 50px;
        }
        .info-section {
            padding: 15px;
        }
        .info-value {
            font-size: 14px;
        }
        .share-btn {
            font-size: 12px;
        }
        .share-btn i {
            margin-right: 4px;
        }
    }
</style>

<section class="section-padding-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                {{-- Image Gallery --}}
                <div class="property-gallery mb-4">
                    @if($house->images->count() > 0)
                        <img id="mainImage" src="{{ $house->images->where('is_primary', true)->first()?->image_url ?? $house->images->first()->image_url }}"
                             class="main-image" alt="{{ $house->title }}" onclick="openImageModal(this.src)">
                        <div class="thumbnail-list">
                            @foreach($house->images as $image)
                                <img src="{{ $image->image_url }}"
                                     class="thumbnail {{ ($house->images->where('is_primary', true)->first()?->id == $image->id || (!$house->images->where('is_primary', true)->first() && $loop->first)) ? 'active' : '' }}"
                                     onclick="changeMainImage(this)"
                                     data-image="{{ $image->image_url }}">
                            @endforeach
                        </div>
                    @else
                        <div class="main-image" style="background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-image" style="font-size: 80px; color: #dee2e6;"></i>
                        </div>
                    @endif
                </div>

                {{-- Property Details --}}
                <div class="info-section">
                    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                        <div>
                            <h3 class="mb-2">{{ $house->title }}</h3>
                            <div class="d-flex align-items-center gap-3 text-muted flex-wrap">
                                <span><i class="fa fa-map-marker mr-1" style="color:#2ECC71;"></i> {{ $house->address }}, {{ $house->city }}, {{ $house->state }}</span>
                                <span><i class="fa fa-calendar mr-1"></i> Listed {{ $house->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="badge" style="background: #2ECC71; color: white; padding: 8px 16px; border-radius: 20px;">
                                {{ ucfirst($house->status) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="info-label">Price</div>
                            <div class="info-value fs-3 fw-bold" style="color:#2ECC71;">
                                ₦{{ number_format($house->price, 2) }}
                                @if($house->listing_type == 'rent')
                                    <small class="fs-6">/year</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-label">Listing Type</div>
                            <div class="info-value">{{ ucfirst($house->listing_type) }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="info-label">Property Type</div>
                            <div class="info-value">{{ ucfirst($house->property_type) }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Bedrooms</div>
                            <div class="info-value">
                                <i class="fa fa-bed mr-1"></i> {{ $house->bedrooms ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Bathrooms</div>
                            <div class="info-value">
                                <i class="fa fa-bath mr-1"></i> {{ $house->bathrooms ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Toilets</div>
                            <div class="info-value">
                                <i class="fa fa-toilet mr-1"></i> {{ $house->toilets ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-label">Size (sqm)</div>
                            <div class="info-value">
                                <i class="fa fa-arrows-alt mr-1"></i> {{ $house->size_sqm ? number_format($house->size_sqm) . ' m²' : 'N/A' }}
                            </div>
                        </div>
                    </div>

                    @if($house->description)
                        <hr>
                        <div class="mb-3">
                            <div class="info-label">Description</div>
                            <div class="info-value" style="line-height: 1.6;">{{ $house->description }}</div>
                        </div>
                    @endif

                    @if($house->features && is_array($house->features) && count($house->features) > 0)
                        <hr>
                        <div class="mb-3">
                            <div class="info-label">Features & Amenities</div>
                            <div>
                                @foreach($house->features as $feature)
                                    <span class="feature-badge">
                                        <i class="fa fa-check-circle mr-1" style="color:#2ECC71;"></i> {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($house->video_tour_url)
                        <hr>
                        <div class="mb-3">
                            <div class="info-label">Video Tour</div>
                            <a href="{{ $house->video_tour_url }}" target="_blank" class="btn essence-btn btn-sm">
                                <i class="fa fa-video-camera mr-1"></i> Watch Video Tour
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Share Section --}}
                <div class="info-section">
                    <h5 class="mb-3">Share This Property</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary flex-fill share-btn" onclick="shareProperty('facebook')" style="min-width: 80px;">
                            <i class="fa fa-facebook"></i> <span class="d-none d-sm-inline">Facebook</span>
                        </button>
                        <button class="btn btn-outline-info flex-fill share-btn" onclick="shareProperty('twitter')" style="min-width: 80px;">
                            <i class="fa fa-twitter"></i> <span class="d-none d-sm-inline">Twitter</span>
                        </button>
                        <button class="btn btn-outline-success flex-fill share-btn" onclick="shareProperty('whatsapp')" style="min-width: 80px;">
                            <i class="fa fa-whatsapp"></i> <span class="d-none d-sm-inline">WhatsApp</span>
                        </button>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-outline-secondary w-100 share-btn" onclick="copyToClipboard()">
                            <i class="fa fa-link mr-1"></i> Copy Link
                        </button>
                    </div>
                </div>

                {{-- SELLER INFO CARD (Like product page - shows brand and brand reviews) --}}
                <div class="seller-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if($house->seller->avatar)
                            <img src="{{ $house->seller->avatar }}" class="seller-avatar" alt="">
                        @else
                            <div class="seller-avatar-placeholder">
                                {{ strtoupper(substr($house->seller->business_name ?? $house->seller->name ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-bold">{{ $house->seller->business_name ?? $house->seller->name ?? 'Property Agent' }}</p>
                            @if($house->seller->is_verified_business)
                                <small style="color:#2ECC71;">
                                    <i class="fa fa-check-circle"></i> Verified Seller
                                </small>
                            @else
                                <small class="text-muted">Individual Seller</small>
                            @endif
                        </div>
                    </div>

                    {{-- SHOW SELLER'S BRAND (if they have one) - Like product page --}}
                    @if($house->seller->brand)
                        @php
                            $brand = $house->seller->brand;
                            $brandAvgRating = $brand->average_rating ?? 0;
                            $brandTotalReviews = $brand->total_reviews ?? 0;
                        @endphp
                        <div class="mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <small class="text-muted d-block mb-1">Brand</small>
                                    <strong style="color:#2ECC71;">
                                        <i class="fa fa-building-o mr-1"></i> {{ $brand->name }}
                                    </strong>
                                </div>
                                <a href="{{ route('brands.show', $brand->slug) }}" 
                                   class="btn btn-sm" 
                                   style="border:1px solid #2ECC71; color:#2ECC71; border-radius: 20px; padding: 4px 12px; font-size: 11px; text-decoration: none;">
                                    Visit Brand <i class="fa fa-external-link ml-1"></i>
                                </a>
                            </div>
                            
                            {{-- Brand Rating & Reviews --}}
                            <div class="mt-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($brandAvgRating))
                                                <i class="fa fa-star"></i>
                                            @elseif($i - 0.5 <= $brandAvgRating)
                                                <i class="fa fa-star-half-o"></i>
                                            @else
                                                <i class="fa fa-star-o"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <small class="text-muted">({{ $brandTotalReviews }} reviews)</small>
                                </div>
                                @if($brandTotalReviews > 0)
                                    <a href="{{ route('brands.show', $brand->slug) }}#reviews" 
                                       class="small d-block mt-1" 
                                       style="color: #2ECC71; text-decoration: none;">
                                        <i class="fa fa-comments-o mr-1"></i> See all brand reviews →
                                    </a>
                                @else
                                    <small class="text-muted d-block mt-1">No brand reviews yet</small>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- No brand - just show seller info without brand rating --}}
                        <div class="mb-3 pb-2 border-bottom">
                            <small class="text-muted d-block mb-1">Member since</small>
                            <small>{{ $house->seller->created_at->format('M Y') }}</small>
                        </div>
                    @endif

                    <hr class="my-3">
                    
                    @if($house->seller->email)
                        <div class="mb-2 small">
                            <i class="fa fa-envelope mr-2" style="color:#2ECC71;"></i> {{ $house->seller->email }}
                        </div>
                    @endif
                    @if($house->seller->phone)
                        <div class="mb-3 small">
                            <i class="fa fa-phone mr-2" style="color:#2ECC71;"></i> {{ $house->seller->phone }}
                        </div>
                    @endif
                    
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $house->seller->phone ?? '234800000000') }}?text=Hi,%20I'm%20interested%20in%20{{ urlencode($house->title) }}"
                       target="_blank"
                       class="whatsapp-btn mb-2 d-block">
                        <i class="fa fa-contact mr-2"></i> Contact
                    </a>
                </div>

                {{-- Property Stats --}}
                <div class="info-section">
                    <h5 class="mb-3">Property Information</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Listing ID:</span>
                        <span class="fw-semibold">#{{ $house->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Date Added:</span>
                        <span>{{ $house->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Last Updated:</span>
                        <span>{{ $house->updated_at->format('M d, Y') }}</span>
                    </div>
<!--                     <div class="d-flex justify-content-between">
                        <span class="text-muted">Views:</span>
                        <span>{{ $house->views ?? 0 }} views</span>
                    </div> -->
                </div>
            </div>
        </div>

        {{-- Similar Properties --}}
        @if($similarHouses && $similarHouses->count() > 0)
        <div class="mt-5">
            <h4 class="mb-4">Similar Properties You Might Like</h4>
            <div class="row">
                @foreach($similarHouses->take(3) as $similar)
                @php $similarImg = $similar->images->where('is_primary',true)->first() ?? $similar->images->first(); @endphp
                <div class="col-md-4 mb-4">
                    <a href="{{ route('houses.show', $similar->slug) }}" style="text-decoration: none;">
                        <div class="similar-card" style="border:1px solid #eee;border-radius:12px;overflow:hidden;height:100%;">
                            <img src="{{ $similarImg->image_url ?? asset('img/product-img/product-1.jpg') }}"
                                 style="width:100%;height:180px;object-fit:cover;" alt="">
                            <div style="padding:15px;">
                                <h6 style="font-weight:700;margin-bottom:6px;">{{ Str::limit($similar->title, 40) }}</h6>
                                <p style="color:#888;font-size:12px;margin-bottom:8px;">
                                    <i class="fa fa-map-marker"></i> {{ $similar->city }}, {{ $similar->state }}
                                </p>
                                <span style="font-size:18px;font-weight:800;color:#2ECC71;">
                                    ₦{{ number_format($similar->price, 0) }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

{{-- Image Modal --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-end">
                <button type="button" class="btn btn-light mb-2" data-bs-dismiss="modal" style="border-radius: 50%;">
                    <i class="fa fa-times"></i>
                </button>
                <img id="modalImage" src="" class="modal-image w-100" alt="">
            </div>
        </div>
    </div>
</div>

{{-- Support / Report Modal --}}
<div id="supportModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:999999;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:420px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.2);animation:slideUp .25s ease;">

        <div style="background:#1a1a2e;padding:18px 24px;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <i class="fa fa-flag" style="color:#E74C3C;font-size:16px;"></i>
                <h5 style="margin:0;color:#fff;font-size:16px;font-weight:700;">Report a Problem</h5>
            </div>
            <button onclick="document.getElementById('supportModal').style.display='none'"
                    style="background:rgba(255,255,255,0.1);border:none;color:#fff;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">
                &times;
            </button>
        </div>

        <div style="background:#f8f8f8;padding:12px 24px;border-bottom:1px solid #eee;display:flex;align-items:center;gap:10px;">
            <div style="width:40px;height:40px;border-radius:8px;background:#2ECC71;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa fa-home" style="color:#fff;font-size:18px;"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="margin:0;font-size:13px;font-weight:700;color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ Str::limit($house->title, 45) }}
                </p>
                <p style="margin:0;font-size:11px;color:#888;">Property ID: #{{ substr($house->id, 0, 8) }}</p>
            </div>
        </div>

        <div style="padding:24px;">
            <p style="font-size:13px;color:#666;margin-bottom:20px;">
                Found something wrong with this listing? A misleading description, suspicious seller, or technical issue? Let us know and we'll act on it quickly.
            </p>

            <div style="display:flex;flex-direction:column;gap:12px;">
                <a href="https://wa.me/2348152880128?text={{ urlencode('Hi! I want to report a problem with this property: ' . $house->title . ' (ID: #' . substr($house->id, 0, 8) . ') — URL: ' . request()->url()) }}"
                   target="_blank"
                   style="display:flex;align-items:center;gap:14px;padding:14px 18px;border-radius:10px;background:#F0FFF7;border:1.5px solid #2ECC71;text-decoration:none;transition:transform .15s;"
                   onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'">
                    <div style="width:40px;height:40px;border-radius:10px;background:#25D366;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-whatsapp" style="color:#fff;font-size:20px;"></i>
                    </div>
                    <div>
                        <p style="margin:0;font-weight:700;font-size:14px;color:#1a1a1a;">Chat on WhatsApp</p>
                        <p style="margin:0;font-size:12px;color:#666;">Fastest response · Usually replies in minutes</p>
                    </div>
                    <i class="fa fa-chevron-right" style="color:#2ECC71;margin-left:auto;"></i>
                </a>

                <a href="{{ route('buyer.support') }}?house_id={{ $house->id }}&subject={{ urlencode('Problem with property: ' . $house->title) }}"
                   style="display:flex;align-items:center;gap:14px;padding:14px 18px;border-radius:10px;background:#F5F5FF;border:1.5px solid #7B68EE;text-decoration:none;transition:transform .15s;"
                   onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'">
                    <div style="width:40px;height:40px;border-radius:10px;background:#7B68EE;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-ticket" style="color:#fff;font-size:18px;"></i>
                    </div>
                    <div>
                        <p style="margin:0;font-weight:700;font-size:14px;color:#1a1a1a;">Open a Support Ticket</p>
                        <p style="margin:0;font-size:12px;color:#666;">We'll follow up via email · Usually within 24 hrs</p>
                    </div>
                    <i class="fa fa-chevron-right" style="color:#7B68EE;margin-left:auto;"></i>
                </a>
            </div>

            <p style="text-align:center;font-size:11px;color:#aaa;margin-top:20px;margin-bottom:0;">
                Our support team is available Mon – Fri: 8am – 8pm WAT | Sat – Sun: 10am – 6pm WAT
            </p>
        </div>
    </div>
</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>

<style>
@keyframes slideUp {
    from { opacity:0; transform:translateY(30px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script>
// ── Support Modal ──────────────────────────────────────────
document.getElementById('supportModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

// ── Keyboard: Escape closes support modal, arrows navigate gallery ──
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const sm = document.getElementById('supportModal');
        if (sm && sm.style.display === 'flex') sm.style.display = 'none';
    }
});

// ── Native share (breadcrumb button) ──────────────────────
function sharePropertyNative() {
    const url   = window.location.href;
    const title = "{{ $house->title }}";
    if (navigator.share) {
        navigator.share({ title, url }).catch(function(err) {
            if (err.name !== 'AbortError') openShareFallback(url, title);
        });
    } else {
        openShareFallback(url, title);
    }
}

function openShareFallback(url, title) {
    const modal = document.createElement('div');
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999999;display:flex;align-items:center;justify-content:center;padding:16px;';
    modal.innerHTML = `
        <div style="background:#fff;border-radius:12px;max-width:400px;width:100%;padding:24px;animation:slideUp .25s ease;">
            <h3 style="margin:0 0 16px;font-size:18px;">Share this property</h3>
            <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;">
                <button onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}','_blank','width=600,height=400')" style="background:#1877F2;border:none;border-radius:50%;width:44px;height:44px;color:#fff;cursor:pointer;"><i class="fa fa-facebook"></i></button>
                <button onclick="window.open('https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}','_blank','width=600,height=400')" style="background:#1DA1F2;border:none;border-radius:50%;width:44px;height:44px;color:#fff;cursor:pointer;"><i class="fa fa-twitter"></i></button>
                <button onclick="window.open('https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}','_blank')" style="background:#25D366;border:none;border-radius:50%;width:44px;height:44px;color:#fff;cursor:pointer;"><i class="fa fa-whatsapp"></i></button>
                <button onclick="window.open('https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}','_blank')" style="background:#0088cc;border:none;border-radius:50%;width:44px;height:44px;color:#fff;cursor:pointer;"><i class="fa fa-telegram"></i></button>
                <button onclick="navigator.clipboard.writeText('${url}').then(()=>alert('Link copied!'))" style="background:#6c757d;border:none;border-radius:50%;width:44px;height:44px;color:#fff;cursor:pointer;"><i class="fa fa-link"></i></button>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" style="margin-top:16px;width:100%;padding:10px;background:#f5f5f5;border:none;border-radius:8px;cursor:pointer;">Close</button>
        </div>`;
    document.body.appendChild(modal);
    modal.addEventListener('click', function(e) { if (e.target === modal) modal.remove(); });
}

// ── Existing functions (unchanged) ────────────────────────
function changeMainImage(element) {
    const mainImage = document.getElementById('mainImage');
    mainImage.src = element.dataset.image;
    document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
    element.classList.add('active');
}

function openImageModal(imageUrl) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageUrl;
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

function shareProperty(platform) {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent("{{ $house->title }}");
    let shareUrl;
    switch(platform) {
        case 'facebook': shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`; break;
        case 'twitter':  shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`; break;
        case 'whatsapp': shareUrl = `https://wa.me/?text=${title}%20${url}`; break;
    }
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => alert('Link copied to clipboard!'));
}

// Keyboard navigation for gallery
let currentIndex = 0;
const images = @json($house->images->pluck('image_url'));

document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('imageModal');
    if (modal && modal.classList.contains('show')) {
        if (e.key === 'ArrowLeft') {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            document.getElementById('modalImage').src = images[currentIndex];
        } else if (e.key === 'ArrowRight') {
            currentIndex = (currentIndex + 1) % images.length;
            document.getElementById('modalImage').src = images[currentIndex];
        }
    }
});
</script>
</body>
</html>