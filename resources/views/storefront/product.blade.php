@auth('web')
    @include('layouts.storefront.header-auth')
@else
    @include('layouts.storefront.header-guest')
@endauth

@include('layouts.storefront.cart-sidebar') 
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image: url({{ asset('img/bg-img/breadcumb.jpeg') }}); background-size: cover; background-position: center;">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12">
                <div class="page-title text-center">
                    <h2 class="fs-2 fs-md-1">{{ Str::limit($product->name, 40) }}</h2>
                    <button onclick="shareProduct()" style="background: none; border: 1px solid #2ECC71; border-radius: 50px; padding: 6px 20px; margin-top: 10px; font-size: 13px; color: #2ECC71; cursor: pointer;">
                        <i class="fa fa-share-alt mr-1"></i> Share
                    </button>
                </div>
            </div>
        </div>
    </div>
</div> 
 
<style>  
    @media (max-width: 768px) {
        .single_product_area {
            padding: 40px 0;
        }
        .single_product_desc h2 {
            font-size: 20px !important;
        }
        .single_product_desc [style*="font-size:32px"] {
            font-size: 24px !important;
        }
        .product-img img {
            width: 100%;
            height: auto;
        }
        .nav-tabs .nav-link {
            padding: 8px 12px;
            font-size: 14px;
        }
        .trust-badges {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px !important;
        }
        .seller-info {
            flex-direction: column;
            text-align: center;
        }
        .seller-info .ml-auto {
            margin-left: 0 !important;
            margin-top: 10px;
        }
    }
    
    @media (max-width: 576px) {
        .single_product_desc h2 {
            font-size: 18px !important;
        }
        .single_product_desc [style*="font-size:32px"] {
            font-size: 20px !important;
        }
        .d-flex.gap-3 {
            gap: 12px !important;
        }
        .thumbnail-list {
            justify-content: center;
        }
        .table-responsive {
            overflow-x: auto;
        }
    }
    
    @media (max-width: 480px) {
        .breadcumb_area {
            padding: 40px 0;
        }
        .breadcumb_area h2 {
            font-size: 1.2rem;
        }
        .nav-tabs .nav-link {
            padding: 6px 10px;
            font-size: 12px;
        }
        .trust-badges div {
            font-size: 10px;
        }
    }
    .rating-star i {
        transition: color 0.2s ease;
    }
    .rating-star:hover i {
        color: #F39C12 !important;
    }
    .review-img {
        transition: transform 0.2s ease;
    }
    .review-img:hover {
        transform: scale(1.05);
    }
</style>

<section class="single_product_area section-padding-80">
    <div class="container">
        <div class="row g-4">
            {{-- Images --}}
            <div class="col-12 col-lg-6">
                <div class="single_product_img">
                    {{-- Main image --}}
                    @php $primaryImg = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
                    <div id="mainImgWrap" style="border:1px solid #eee;border-radius:10px;overflow:hidden;margin-bottom:16px;">
                        <img id="mainProductImg"
                             src="{{ $primaryImg->image_url ?? asset('img/product-img/product-1.jpg') }}"
                             style="width:100%;height:auto;aspect-ratio:1/1;object-fit:contain;background:#fafafa;"
                             alt="{{ $product->name }}">
                    </div>
                    {{-- Thumbnails --}}
                    <div class="d-flex gap-2 flex-wrap justify-content-start">
                        @foreach($product->images as $img)
                        <img src="{{ $img->image_url }}"
                             style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:2px solid {{ $img->is_primary ? '#2ECC71' : '#eee' }};cursor:pointer;"
                             onclick="document.getElementById('mainProductImg').src='{{ $img->image_url }}';
                                      document.querySelectorAll('.thumb-img').forEach(t=>t.style.borderColor='#eee');
                                      this.style.borderColor='#2ECC71';"
                             class="thumb-img" alt="">
                        @endforeach
                        {{-- Video thumbnail --}}
                        @if($product->videos->first())
                        <div style="width:70px;height:70px;border-radius:6px;border:2px solid #eee;background:#1a1a2e;display:flex;align-items:center;justify-content:center;cursor:pointer;"
                             onclick="document.getElementById('productVideo').style.display='block';document.getElementById('mainImgWrap').style.display='none';">
                            <i class="fa fa-play" style="color:#fff;font-size:20px;"></i>
                        </div>
                        @endif
                    </div>
                    {{-- Video player (hidden) --}}
                    @if($product->videos->first())
                    <div id="productVideo" style="display:none;margin-top:16px;">
                        <video controls style="width:100%;border-radius:10px;">
                            <source src="{{ $product->videos->first()->video_url }}">
                        </video>
                        <button onclick="document.getElementById('productVideo').style.display='none';document.getElementById('mainImgWrap').style.display='block';"
                                style="background:none;border:none;color:#2ECC71;cursor:pointer;font-size:13px;margin-top:8px;">
                            <i class="fa fa-image mr-1"></i> Back to images
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Product info --}}
            <div class="col-12 col-lg-6">
                <div class="single_product_desc">
                    {{-- Seller / category --}}
                    <div class="d-flex align-items-center gap-3 mb-2 flex-wrap">
                        <span style="font-size:13px;color:#2ECC71;font-weight:600;">
                            {{ $product->category->name ?? '' }}
                        </span>
                        @if($product->subcategory)
                        <span class="text-muted" style="font-size:12px;">/ {{ $product->subcategory->name }}</span>
                        @endif
                    </div>

                    <h2 style="font-size:24px;font-weight:800;margin-bottom:12px;color:#1a1a1a;word-break:break-word;">
                        {{ $product->name }}
                    </h2>

                    {{-- Rating --}}
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        <div style="color:#F39C12;">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= round($product->average_rating) ? '★' : '☆' }}
                            @endfor
                        </div>
                        <span style="font-size:13px;color:#888;">
                            ({{ $product->total_reviews }} reviews)
                        </span>
                        <span style="font-size:13px;color:#888;">·</span>
                        <span style="font-size:13px;color:#888;">{{ $product->total_sold }} sold</span>
                    </div>

                    {{-- ⚡ Flash Sale Banner --}}
                    @if(isset($flashSale) && $flashSale?->isActive())
                    <div style="background:#FEF9EC;border:1.5px solid #F39C12;border-radius:10px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span style="font-size:20px;">⚡</span>
                            <div>
                                <p style="margin:0;font-weight:800;font-size:14px;color:#B7770D;">Flash Sale — Limited Time!</p>
                                <p style="margin:0;font-size:12px;color:#888;">
                                    @if($flashSale->quantity_limit)
                                        {{ $flashSale->quantity_limit - $flashSale->quantity_sold }} units left at this price ·
                                    @endif
                                    Ends <strong style="color:#E74C3C;" id="flashCountdown" data-ends="{{ $flashSale->ends_at->toISOString() }}">
                                        {{ $flashSale->ends_at->diffForHumans() }}
                                    </strong>
                                </p>
                            </div>
                        </div>
                        <span style="background:#F39C12;color:#fff;font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;white-space:nowrap;">
                            -{{ round((($flashSale->original_price - $flashSale->sale_price) / $flashSale->original_price) * 100) }}% OFF
                        </span>
                    </div>
                    @endif

                    {{-- Price --}}
                    <div class="mb-3">
                        @if(isset($flashSale) && $flashSale?->isActive())
                        <span style="font-size:32px;font-weight:800;color:#E74C3C;" class="price-large">
                            ₦{{ number_format($flashSale->sale_price, 2) }}
                        </span>
                        <span style="font-size:18px;color:#aaa;text-decoration:line-through;margin-left:10px;">
                            ₦{{ number_format($flashSale->original_price, 2) }}
                        </span>
                        <span style="background:#FADBD8;color:#E74C3C;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;margin-left:8px;display:inline-block;">
                            ⚡ Flash Price
                        </span>
                        @elseif($product->sale_price)
                        <span style="font-size:32px;font-weight:800;color:#2ECC71;" class="price-large">
                            ₦{{ number_format($product->sale_price, 2) }}
                        </span>
                        <span style="font-size:18px;color:#aaa;text-decoration:line-through;margin-left:10px;">
                            ₦{{ number_format($product->price, 2) }}
                        </span>
                        <span style="background:#FADBD8;color:#E74C3C;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;margin-left:8px;display:inline-block;">
                            -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                        </span>
                        @else
                        <span style="font-size:32px;font-weight:800;color:#1a1a1a;" class="price-large">
                            ₦{{ number_format($product->price, 2) }}
                        </span>
                        @endif
                    </div>

                    {{-- Stock / condition --}}
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        <span style="font-size:13px;color:#888;">
                            Condition: <strong>{{ ucfirst($product->condition) }}</strong>
                        </span>
                        <span style="font-size:13px;color:#888;">·</span>
                        <span style="font-size:13px;color:{{ $product->stock > 0 ? '#2ECC71' : '#E74C3C' }};font-weight:600;">
                            {{ $product->stock > 0 ? $product->stock . ' in stock' : 'Out of stock' }}
                        </span>
                    </div>

                    @if($product->location)
                    <div class="mb-3">
                        <span style="font-size:13px;color:#888;">
                            <i class="fa fa-map-marker mr-1"></i> {{ $product->location }}
                        </span>
                    </div>
                    @endif

                    {{-- Product Options (if any) --}}
                    @if($product->options->count())
                    <div class="product-options mb-3" id="productOptions">
                        @foreach($product->options as $option)
                        <div class="mb-3" data-option-id="{{ $option->id }}">
                            <p class="mb-2" style="font-size:14px;font-weight:700;color:#1a1a1a;">
                                {{ $option->name }}:
                                <span class="selected-label text-muted fw-normal" id="selected-{{ $option->id }}">—</span>
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($option->values as $val)
                                <button type="button"
                                        class="option-swatch"
                                        data-option-id="{{ $option->id }}"
                                        data-option-name="{{ $option->name }}"
                                        data-value-id="{{ $val->id }}"
                                        data-value="{{ $val->value }}"
                                        data-image="{{ $val->image_url ?? '' }}"
                                        title="{{ $val->value }}"
                                        style="
                                            border: 2px solid #ddd;
                                            border-radius: 8px;
                                            background: #fff;
                                            cursor: pointer;
                                            padding: 0;
                                            overflow: hidden;
                                            transition: border-color .15s, transform .15s;
                                            {{ $val->image_url ? 'width:52px;height:52px;' : 'padding:6px 14px;height:36px;font-size:13px;font-weight:600;' }}
                                        ">
                                    @if($val->image_url)
                                        <img src="{{ $val->image_url }}"
                                             style="width:100%;height:100%;object-fit:cover;display:block;"
                                             alt="{{ $val->value }}">
                                    @else
                                        {{ $val->value }}
                                    @endif
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Add to cart - MOVED OUTSIDE the options condition --}}
                    @if($product->stock > 0)
                    <form id="addToCartForm" class="mb-3">
                        @csrf
                        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                            <label style="font-weight:600;font-size:14px;">Quantity:</label>
                            <div class="d-flex align-items-center border rounded" style="overflow:hidden;">
                                <button type="button" onclick="changeQty(-1)"
                                        style="width:36px;height:36px;border:none;background:#f5f5f5;font-size:18px;cursor:pointer;">−</button>
                                <input type="number" id="qty" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                       style="width:50px;height:36px;border:none;text-align:center;font-weight:700;">
                                <button type="button" onclick="changeQty(1)"
                                        style="width:36px;height:36px;border:none;background:#f5f5f5;font-size:18px;cursor:pointer;">+</button>
                            </div>
                        </div>
                            <div class="d-flex gap-3 flex-wrap mb-2">
                                <button type="button" class="btn essence-btn flex-grow-1"
                                        onclick="addToCart('{{ $product->id }}', {{ isset($flashSale) && $flashSale?->isActive() ? 'true' : 'false' }})">
                                    <i class="fa fa-shopping-bag mr-2"></i> Add to Cart
                                </button>
                                <button type="button" class="btn"
                                        style="border:2px solid #2ECC71;color:#2ECC71;padding:0 16px;border-radius:4px;"
                                        onclick="toggleWishlist('{{ $product->id }}', this)">
                                    <i class="fa fa-heart{{ $inWishlist ? '' : '-o' }}"></i>
                                </button>
                            </div>
                    </form>

                {{-- ── Buy Now ──────────────────────────────────────────────────── --}}
                <form action="{{ route('buy-now.initiate') }}" method="POST" class="mt-2">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity"   id="buy-now-qty" value="1">

                    {{-- Pass selected options as JSON --}}
                    <input type="hidden" name="selected_options_json" id="buy-now-options" value="[]">

                    <button type="submit" class="btn w-100"
                            style="background:#F39C12;color:#fff;font-weight:700;border:none;
                                   border-radius:4px;padding:11px 0;font-size:15px;letter-spacing:.3px;"
                            onclick="syncBuyNowFields()">
                        <i class="fa fa-bolt mr-2"></i> Buy Now
                    </button>
                </form>
                    @else
                    <div class="alert alert-danger">
                        <i class="fa fa-times-circle mr-2"></i> Out of stock
                    </div>
                    @endif

                    {{-- Seller info --}}
                    <div style="background:#f8f8f8;border-radius:8px;padding:16px;margin-top:16px;">
                        <div class="d-flex align-items-center gap-3 flex-wrap seller-info">
                            @if($product->seller->avatar)
                                <img src="{{ $product->seller->avatar }}"
                                     style="width:44px;height:44px;border-radius:50%;object-fit:cover;" alt="">
                            @else
                                <div style="width:44px;height:44px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;flex-shrink:0;">
                                    {{ strtoupper(substr($product->seller->business_name, 0, 1)) }}
                                </div>
                            @endif
                            <div style="flex:1;">
                                <p style="margin:0;font-weight:700;font-size:14px;">
                                    {{ $product->seller->business_name }}
                                </p>
                                <small style="color:#888;">
                                    @if($product->seller->is_verified_business)
                                        <i class="fa fa-check-circle" style="color:#2ECC71;"></i>
                                        Verified Seller
                                    @else
                                        Individual Seller
                                    @endif
                                </small>
                            </div>
                            @if($product->seller->brand)
                            <a href="{{ route('brands.show', $product->seller->brand->slug) }}"
                               class="btn btn-sm"
                               style="border:1px solid #2ECC71;color:#2ECC71;border-radius:4px;font-size:12px;">
                                Visit Store
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Trust badges --}}
                    <div class="d-flex gap-3 flex-wrap mt-3 trust-badges">
                        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#666;">
                            <i class="fa fa-lock" style="color:#2ECC71;"></i> Escrow Protection
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#666;">
                            <i class="fa fa-truck" style="color:#2ECC71;"></i> Worldwide Delivery
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#666;">
                            <i class="fa fa-refresh" style="color:#2ECC71;"></i> Return Policy
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Sidebar Ads - full width banner --}}
        @if(isset($sidebarAds) && $sidebarAds->count())
        <div class="row mt-4">
            @foreach($sidebarAds as $ad)
            <div class="col-12 col-md-6 mb-3">
                <div style="position:relative;border-radius:12px;overflow:hidden;background:#1a1a2e;height:140px;">
                    @if($ad->media_type === 'video' && $ad->media_url)
                        <video autoplay muted loop playsinline
                               style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;">
                            <source src="{{ $ad->media_url }}">
                        </video>
                    @elseif($ad->media_url)
                        <div style="position:absolute;inset:0;background-image:url('{{ $ad->media_url }}');background-size:cover;background-position:center;z-index:0;"></div>
                    @endif
                    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.50);z-index:1;"></div>
                    <div style="position:relative;z-index:2;padding:16px 20px;height:100%;display:flex;flex-direction:column;justify-content:center;">
                        <span style="display:inline-block;background:#2ECC71;color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;width:fit-content;">
                            Sponsored
                        </span>
                        <p style="color:#fff;font-size:13px;font-weight:700;margin-bottom:10px;line-height:1.4;">
                            {{ Str::limit($ad->title, 70) }}
                        </p>
                        <a href="{{ $ad->clickTrackingUrl() }}"
                           style="display:inline-block;background:#2ECC71;color:#fff;padding:5px 14px;border-radius:6px;font-size:12px;font-weight:700;text-decoration:none;width:fit-content;">
                            Shop Now
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Description + Reviews --}}
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#description">Description</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#reviews">
                            Reviews ({{ $product->total_reviews }})
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-4">
                    <div class="tab-pane fade show active" id="description">
                        <div style="line-height:1.9;color:#444;font-size:15px;word-break:break-word;">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                        @if($product->weight_kg)
                        <div class="mt-4">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="max-width:400px;">
                                    <tr><td class="fw-bold">Weight</td><td>{{ $product->weight_kg }} kg</td></tr>
                                    <tr><td class="fw-bold">Condition</td><td>{{ ucfirst($product->condition) }}</td></tr>
                                    @if($product->sku)
                                    <tr><td class="fw-bold">SKU</td><td>{{ $product->sku }}</td></tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Reviews Tab --}}
                    <div class="tab-pane fade" id="reviews">
                        @if($product->reviews->count())
                            @foreach($product->reviews->where('is_visible',true)->take(10) as $review)
                            <div class="d-flex gap-3 mb-4 pb-4 border-bottom flex-wrap">
                                <div style="width:44px;height:44px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($review->user->first_name ?? 'U', 0, 1)) }}
                                </div>
                                <div style="flex:1;">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <strong>{{ $review->user->first_name ?? 'Buyer' }}</strong>
                                        <span style="color:#F39C12;">
                                            @for($i=1;$i<=5;$i++) {{ $i<=$review->rating?'★':'☆' }} @endfor
                                        </span>
                                        @if($review->is_verified_purchase)
                                        <span style="background:#D5F5E3;color:#1E8449;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;">
                                            Verified Purchase
                                        </span>
                                        @endif
                                    </div>
                                    <p style="color:#555;font-size:14px;margin:0 0 8px 0;word-break:break-word;">
                                        {{ $review->review ?? 'No comment.' }}
                                    </p>
                                    
                                    {{-- Display review images if any --}}
                                    @if($review->images && is_array($review->images) && count($review->images) > 0)
                                    <div class="d-flex gap-2 mt-2 flex-wrap">
                                        @foreach($review->images as $img)
                                        <img src="{{ $img }}" 
                                             style="width:60px;height:60px;object-fit:cover;border-radius:6px;cursor:pointer;"
                                             onclick="openReviewImageModal('{{ $img }}')"
                                             class="review-img"
                                             alt="Review image">
                                        @endforeach
                                    </div>
                                    @endif
                                    
                                    <small style="color:#aaa;">{{ $review->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                            @endforeach
                        @endif

                        {{-- Review Form Logic --}}
                        @php
                            $canReview = false;
                            $hasPurchased = false;
                            $hasReviewed = false;
                            
                            if(auth('web')->check()) {
                                $userId = auth('web')->id();
                                
                                // Check if user has purchased this product before
                                $hasPurchased = \App\Models\OrderItem::whereHas('order', function($q) use ($userId) {
                                    $q->where('user_id', $userId)
                                      ->whereIn('payment_status', ['paid', 'completed'])
                                      ->where('status', '!=', 'cancelled');
                                })->where('orderable_id', $product->id)
                                  ->where('orderable_type', 'App\Models\Product')
                                  ->exists();
                                
                                // Check if user has already reviewed this product
                                $hasReviewed = \App\Models\ProductReview::where('product_id', $product->id)
                                    ->where('user_id', $userId)
                                    ->exists();
                                
                                $canReview = $hasPurchased && !$hasReviewed;
                            }
                        @endphp
                        
                        @if(!auth('web')->check())
                            <div class="text-center py-4">
                                <div class="alert alert-info">
                                    <i class="fa fa-lock me-2"></i>
                                    Please <a href="{{ route('login') }}" class="alert-link">login</a> to write a review.
                                </div>
                            </div>
                        @elseif($hasReviewed)
                            <div class="text-center py-4">
                                <div class="alert alert-success">
                                    <i class="fa fa-check-circle me-2"></i>
                                    Thank you for your review! You have already reviewed this product.
                                </div>
                            </div>
                        @elseif(!$hasPurchased)
                            <div class="text-center py-4">
                                <div class="alert alert-warning">
                                    <i class="fa fa-shopping-cart me-2"></i>
                                    You can only review products you have purchased. 
                                    <a href="{{ route('product.show', $product->slug) }}" class="alert-link">Buy this product</a> to leave a review.
                                </div>
                            </div>
                        @else
                            <div class="review-form mt-4">
                                <h5 class="mb-3">Write a Review</h5>
                                <form action="{{ route('product.review', $product->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    
                                    {{-- Rating --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Your Rating *</label>
                                        <div class="rating-input">
                                            <div class="d-flex gap-2">
                                                @for($i = 5; $i >= 1; $i--)
                                                <label class="rating-star" style="cursor:pointer; font-size: 30px; color: #ddd;">
                                                    <input type="radio" name="rating" value="{{ $i }}" style="display: none;" required>
                                                    <i class="fa fa-star" data-value="{{ $i }}"></i>
                                                </label>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Review Text --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Your Review *</label>
                                        <textarea name="review" rows="5" class="form-control" placeholder="Share your experience with this product..." required></textarea>
                                    </div>
                                    
                                    {{-- Images --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Add Photos (Optional)</label>
                                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                        <small class="text-muted">You can upload up to 5 images (JPG, PNG, WEBP, max 2MB each)</small>
                                    </div>
                                    
                                    <button type="submit" class="btn essence-btn">
                                        <i class="fa fa-paper-plane me-2"></i> Submit Review
                                    </button>
                                </form>
                            </div>
                        @endif
                        
                        @if(!$product->reviews->count() && !$canReview && !$hasReviewed && auth('web')->check())
                            <div class="text-center py-4 text-muted">
                                <i class="fa fa-star-o" style="font-size:36px;color:#ddd;"></i>
                                <p class="mt-2">No reviews yet. Be the first to review this product.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Related products --}}
        @if($relatedProducts->count())
        <div class="row mt-5">
            <div class="col-12">
                <h3 style="font-weight:800;margin-bottom:24px;">Related Products</h3>
            </div>
            @foreach($relatedProducts as $related)
            @php $rImg = $related->images->where('is_primary',true)->first() ?? $related->images->first(); @endphp
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="single-product-wrapper">
                    <div class="product-img">
                        <a href="{{ route('product.show', $related->slug) }}">
                            <img src="{{ $rImg->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="" style="width:100%;aspect-ratio:1/1;object-fit:cover;">
                        </a>
                        <div class="product-favourite">
                            <a href="#" class="favme fa fa-heart" data-product="{{ $related->id }}"></a>
                        </div>
                    </div>
                    <div class="product-description">
                        <span>{{ $related->seller->business_name ?? '' }}</span>
                        <a href="{{ route('product.show', $related->slug) }}">
                            <h6>{{ Str::limit($related->name, 35) }}</h6>
                        </a>
                        <p class="product-price">₦{{ number_format($related->price, 2) }}</p>
                        <div class="hover-content">
                            <div class="add-to-cart-btn">
                                <a href="#" class="btn essence-btn add-to-cart"
                                   data-product="{{ $related->id }}">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</section>

{{-- Review Image Modal --}}
<div id="reviewImageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 999999; align-items: center; justify-content: center; cursor: pointer;">
    <img id="modalReviewImage" src="" style="max-width: 90%; max-height: 90%; object-fit: contain;">
    <button onclick="closeReviewImageModal()" style="position: absolute; top: 20px; right: 20px; background: white; border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer;">
        <i class="fa fa-times"></i>
    </button>
</div>

@include('layouts.storefront.footer')

<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>

<style>
.option-swatch:hover {
    border-color: #2ECC71 !important;
    transform: translateY(-1px);
}
.option-swatch.active {
    border-color: #2ECC71 !important;
    box-shadow: 0 0 0 2px rgba(46,204,113,.35);
    transform: translateY(-1px);
}
</style>

<script>
// Auto-select first option for each option group if none selected
function autoSelectFirstOptions() {
    var optionsContainer = document.getElementById('productOptions');
    if (!optionsContainer) return; // No options, exit quietly
    
    document.querySelectorAll('#productOptions [data-option-id]').forEach(function (group) {
        var optionId = group.dataset.optionId;
        
        // If no option is selected for this group
        if (!window._selectedOptions[optionId]) {
            var firstSwatch = group.querySelector('.option-swatch');
            if (firstSwatch) {
                // Trigger click on the first swatch
                firstSwatch.click();
            }
        }
    });
}

// Options swatch logic
window._selectedOptions = {};
 
document.querySelectorAll('.option-swatch').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var optionId   = this.dataset.optionId;
        var optionName = this.dataset.optionName;
        var valueId    = this.dataset.valueId;
        var value      = this.dataset.value;
        var imgUrl     = this.dataset.image;
 
        // Deselect all swatches in this option group, then mark this one active
        document.querySelectorAll('.option-swatch[data-option-id="' + optionId + '"]')
                .forEach(function(b) { b.classList.remove('active'); });
        this.classList.add('active');
 
        // Update the "selected: X" label next to the option name
        var label = document.getElementById('selected-' + optionId);
        if (label) label.textContent = value;
 
        // Store the full selection object
        window._selectedOptions[optionId] = {
            option_id:   optionId,
            option_name: optionName,
            value_id:    valueId,
            value:       value,
            image_url:   imgUrl || null,
        };
 
        // If the swatch has its own image, swap the main product photo
        if (imgUrl) {
            var mainImg = document.getElementById('mainProductImg');
            if (mainImg) {
                mainImg.src = imgUrl;
                document.querySelectorAll('.thumb-img').forEach(function (t) {
                    t.style.borderColor = (t.src === imgUrl) ? '#2ECC71' : '#eee';
                });
            }
        }
    });
});
 
// Validate options - auto-selects first option if none selected
window.validateOptions = function () {
    // Only run if product has options
    if (document.getElementById('productOptions')) {
        autoSelectFirstOptions();
    }
    return true; // Always return true
};
 
// Returns the selections as an array for the cart payload
window.getSelectedOptions = function () {
    return Object.values(window._selectedOptions);
};

// Flash sale countdown timer
(function() {
    const el = document.getElementById('flashCountdown');
    if (!el) return;
    const endsAt = new Date(el.dataset.ends);

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        const diff = endsAt - Date.now();
        if (diff <= 0) {
            el.textContent = 'Expired';
            setTimeout(() => location.reload(), 1500);
            return;
        }
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        el.textContent = h > 0
            ? pad(h) + ':' + pad(m) + ':' + pad(s)
            : pad(m) + ':' + pad(s);
    }

    tick();
    setInterval(tick, 1000);
})();

function changeQty(delta) {
    const input = document.getElementById('qty');
    const max   = parseInt(input.max);
    let val     = parseInt(input.value) + delta;
    if (val < 1)   val = 1;
    if (val > max) val = max;
    input.value = val;
}

function addToCart(productId, isFlashSale = false) {
    // Auto-select any unselected options (if product has options)
    if (typeof window.validateOptions === 'function') {
        window.validateOptions();
    }
 
    const qty = document.getElementById('qty').value;
 
    const payload = {
        product_id:       productId,
        quantity:         parseInt(qty),
        selected_options: (typeof window.getSelectedOptions === 'function')
                            ? window.getSelectedOptions()
                            : [],
    };
 
    if (isFlashSale) payload.flash_sale = true;
 
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (window.loadCart) window.loadCart();
            if (window.cartToast) window.cartToast('Item added to cart!');
        } else {
            if (window.cartToast) window.cartToast(data.message ?? 'Could not add item.', 'error');
        }
    })
    .catch(() => {
        if (window.cartToast) window.cartToast('Something went wrong.', 'error');
    });
}

function toggleWishlist(productId, btn) {
    @auth('web')
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
        const icon = btn.querySelector('i');
        if (data.added) {
            icon.classList.remove('fa-heart-o');
            icon.classList.add('fa-heart');
        } else {
            icon.classList.remove('fa-heart');
            icon.classList.add('fa-heart-o');
        }
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
}

function openReviewImageModal(imgUrl) {
    document.getElementById('modalReviewImage').src = imgUrl;
    document.getElementById('reviewImageModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeReviewImageModal() {
    document.getElementById('reviewImageModal').style.display = 'none';
    document.body.style.overflow = '';
}

function shareProduct() {
    const url = window.location.href;
    const title = "{{ $product->name }}";
    const text = `Check out this product: {{ $product->name }}`;
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: text,
            url: url,
        }).catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Share failed:', err);
                fallbackShare(url, title);
            }
        });
    } else {
        fallbackShare(url, title);
    }
}

function fallbackShare(url, title) {
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0,0,0,0.6)';
    modal.style.zIndex = '999999';
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    
    modal.innerHTML = `
        <div style="background: white; border-radius: 12px; max-width: 400px; width: 90%; padding: 20px; animation: fadeIn 0.3s ease;">
            <h3 style="margin: 0 0 15px 0; font-size: 18px;">Share this product</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center;">
                <button onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}', '_blank', 'width=600,height=400')" style="background: #1877F2; border: none; border-radius: 50%; width: 44px; height: 44px; color: white; cursor: pointer;"><i class="fa fa-facebook"></i></button>
                <button onclick="window.open('https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}', '_blank', 'width=600,height=400')" style="background: #1DA1F2; border: none; border-radius: 50%; width: 44px; height: 44px; color: white; cursor: pointer;"><i class="fa fa-twitter"></i></button>
                <button onclick="window.open('https://wa.me/?text=${encodeURIComponent(title)}%20${encodeURIComponent(url)}', '_blank', 'width=600,height=400')" style="background: #25D366; border: none; border-radius: 50%; width: 44px; height: 44px; color: white; cursor: pointer;"><i class="fa fa-whatsapp"></i></button>
                <button onclick="window.open('https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}', '_blank', 'width=600,height=400')" style="background: #0088cc; border: none; border-radius: 50%; width: 44px; height: 44px; color: white; cursor: pointer;"><i class="fa fa-telegram"></i></button>
                <button onclick="window.open('https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}', '_blank', 'width=600,height=400')" style="background: #0077B5; border: none; border-radius: 50%; width: 44px; height: 44px; color: white; cursor: pointer;"><i class="fa fa-linkedin"></i></button>
                <button onclick="window.open('https://www.reddit.com/submit?url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}', '_blank', 'width=600,height=400')" style="background: #FF4500; border: none; border-radius: 50%; width: 44px; height: 44px; color: white; cursor: pointer;"><i class="fa fa-reddit"></i></button>
                <button onclick="navigator.clipboard.writeText('${url}').then(() => alert('Link copied!'))" style="background: #6c757d; border: none; border-radius: 50%; width: 44px; height: 44px; color: white; cursor: pointer;"><i class="fa fa-link"></i></button>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" style="margin-top: 20px; width: 100%; padding: 10px; background: #f5f5f5; border: none; border-radius: 8px; cursor: pointer;">Close</button>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const style = document.createElement('style');
    style.textContent = `@keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }`;
    document.head.appendChild(style);
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Rating star functionality
document.querySelectorAll('.rating-star').forEach(star => {
    const icon = star.querySelector('i');
    const radio = star.querySelector('input');
    
    star.addEventListener('click', function() {
        const value = parseInt(radio.value);
        
        document.querySelectorAll('.rating-star i').forEach(s => {
            const starValue = parseInt(s.dataset.value);
            if (starValue <= value) {
                s.style.color = '#F39C12';
            } else {
                s.style.color = '#ddd';
            }
        });
        
        radio.checked = true;
    });
});

// Close modal when clicking outside
document.getElementById('reviewImageModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeReviewImageModal();
    }
});

document.querySelectorAll('.add-to-cart').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        addToCart(this.dataset.product);
    });
});

// Initialize: Auto-select first options when page loads (only if product has options)
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('productOptions')) {
        autoSelectFirstOptions();
    }
});

function syncBuyNowFields() {
    // Sync quantity
    var qty = document.getElementById('qty');
    if (qty) document.getElementById('buy-now-qty').value = qty.value;

    // Sync selected options
    if (typeof window.getSelectedOptions === 'function') {
        document.getElementById('buy-now-options').value =
            JSON.stringify(window.getSelectedOptions());
    }
}

</script>

</body>
</html>