@auth('web')
    @include('layouts.storefront.header-auth')
@else
    @include('layouts.storefront.header-guest')
@endauth

@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image: url({{ asset('img/bg-img/breadcumb.jpg') }});">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12">
                <div class="page-title text-center">
                    <h2>{{ Str::limit($product->name, 40) }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="single_product_area section-padding-80">
    <div class="container">
        <div class="row">

            {{-- Images --}}
            <div class="col-12 col-lg-6">
                <div class="single_product_img">
                    {{-- Main image --}}
                    @php $primaryImg = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
                    <div id="mainImgWrap" style="border:1px solid #eee;border-radius:10px;overflow:hidden;margin-bottom:16px;">
                        <img id="mainProductImg"
                             src="{{ $primaryImg->image_url ?? asset('img/product-img/product-1.jpg') }}"
                             style="width:100%;height:400px;object-fit:contain;background:#fafafa;"
                             alt="{{ $product->name }}">
                    </div>
                    {{-- Thumbnails --}}
                    <div class="d-flex gap-2 flex-wrap">
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
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span style="font-size:13px;color:#2ECC71;font-weight:600;">
                            {{ $product->category->name ?? '' }}
                        </span>
                        @if($product->subcategory)
                        <span class="text-muted" style="font-size:12px;">/ {{ $product->subcategory->name }}</span>
                        @endif
                    </div>

                    <h2 style="font-size:24px;font-weight:800;margin-bottom:12px;color:#1a1a1a;">
                        {{ $product->name }}
                    </h2>

                    {{-- Rating --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
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

                    {{-- Price --}}
                    <div class="mb-3">
                        @if($product->sale_price)
                        <span style="font-size:32px;font-weight:800;color:#2ECC71;">
                            ${{ number_format($product->sale_price, 2) }}
                        </span>
                        <span style="font-size:18px;color:#aaa;text-decoration:line-through;margin-left:10px;">
                            ${{ number_format($product->price, 2) }}
                        </span>
                        <span style="background:#FADBD8;color:#E74C3C;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;margin-left:8px;">
                            -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                        </span>
                        @else
                        <span style="font-size:32px;font-weight:800;color:#1a1a1a;">
                            ${{ number_format($product->price, 2) }}
                        </span>
                        @endif
                    </div>

                    {{-- Stock / condition --}}
                    <div class="d-flex gap-3 mb-3">
                        <span style="font-size:13px;color:#888;">
                            Condition: <strong>{{ ucfirst($product->condition) }}</strong>
                        </span>
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

                    {{-- Add to cart --}}
                    @if($product->stock > 0)
                    <form id="addToCartForm" class="mb-3">
                        @csrf
                        <div class="d-flex align-items-center gap-3 mb-3">
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
                        <div class="d-flex gap-3">
                            <button type="button" class="btn essence-btn flex-grow-1"
                                    onclick="addToCart('{{ $product->id }}')">
                                <i class="fa fa-shopping-bag mr-2"></i> Add to Cart
                            </button>
                            <button type="button" class="btn"
                                    style="border:2px solid #2ECC71;color:#2ECC71;padding:0 16px;border-radius:4px;"
                                    onclick="toggleWishlist('{{ $product->id }}', this)">
                                <i class="fa fa-heart{{ $inWishlist ? '' : '-o' }}"></i>
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-danger">
                        <i class="fa fa-times-circle mr-2"></i> Out of stock
                    </div>
                    @endif

                    {{-- Seller info --}}
                    <div style="background:#f8f8f8;border-radius:8px;padding:16px;margin-top:16px;">
                        <div class="d-flex align-items-center gap-3">
                            @if($product->seller->avatar)
                                <img src="{{ $product->seller->avatar }}"
                                     style="width:44px;height:44px;border-radius:50%;object-fit:cover;" alt="">
                            @else
                                <div style="width:44px;height:44px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;flex-shrink:0;">
                                    {{ strtoupper(substr($product->seller->business_name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
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
                               class="btn btn-sm ml-auto"
                               style="border:1px solid #2ECC71;color:#2ECC71;border-radius:4px;font-size:12px;">
                                Visit Store
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Trust badges --}}
                    <div class="d-flex gap-3 flex-wrap mt-3">
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
                        <div style="line-height:1.9;color:#444;font-size:15px;">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                        @if($product->weight_kg)
                        <div class="mt-4">
                            <table class="table table-bordered" style="max-width:400px;">
                                <tr><td class="fw-bold">Weight</td><td>{{ $product->weight_kg }} kg</td></tr>
                                <tr><td class="fw-bold">Condition</td><td>{{ ucfirst($product->condition) }}</td></tr>
                                @if($product->sku)
                                <tr><td class="fw-bold">SKU</td><td>{{ $product->sku }}</td></tr>
                                @endif
                            </table>
                        </div>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="reviews">
                        @if($product->reviews->count())
                        @foreach($product->reviews->where('is_visible',true)->take(10) as $review)
                        <div class="d-flex gap-3 mb-4 pb-4 border-bottom">
                            <div style="width:44px;height:44px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($review->user->first_name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
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
                                <p style="color:#555;font-size:14px;margin:0;">{{ $review->review ?? 'No comment.' }}</p>
                                <small style="color:#aaa;">{{ $review->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                        @endforeach
                        @else
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
            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <div class="single-product-wrapper">
                    <div class="product-img">
                        <a href="{{ route('product.show', $related->slug) }}">
                            <img src="{{ $rImg->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
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
                        <p class="product-price">${{ number_format($related->price, 2) }}</p>
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

@include('layouts.storefront.footer')

<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    const max   = parseInt(input.max);
    let val     = parseInt(input.value) + delta;
    if (val < 1)   val = 1;
    if (val > max) val = max;
    input.value = val;
}

function addToCart(productId) {
    const qty = document.getElementById('qty').value;
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ product_id: productId, quantity: parseInt(qty) })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            document.querySelectorAll('#cart-count, #cart-count-sidebar').forEach(el => el.textContent = data.count);
            alert('Added to cart!');
        }
    });
}

function toggleWishlist(productId, btn) {
    @auth('web')
    fetch('{{ route("buyer.wishlist.toggle") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ product_id: productId })
    }).then(r => r.json()).then(data => {
        const icon = btn.querySelector('i');
        if (data.added) {
            icon.classList.remove('fa-heart-o');
            icon.classList.add('fa-heart');
        } else {
            icon.classList.remove('fa-heart');
            icon.classList.add('fa-heart-o');
        }
    });
    @else
    window.location.href = '{{ route("login") }}';
    @endauth
}

document.querySelectorAll('.add-to-cart').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        addToCart(this.dataset.product);
    });
});
</script>
</body>
</html>
