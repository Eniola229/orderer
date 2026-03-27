@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

{{-- Brand hero --}}
<div style="background:#1a1a2e;padding:60px 0;text-align:center;color:#fff;position:relative;overflow:hidden;">
    @if($brand->banner)
    <img src="{{ $brand->banner }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.3;" alt="">
    @endif
    <div style="position:relative;z-index:1;">
        @if($brand->logo)
            <img src="{{ $brand->logo }}" style="height:80px;object-fit:contain;border-radius:12px;background:#fff;padding:8px;margin-bottom:16px;" alt="{{ $brand->name }}">
        @else
            <div style="width:80px;height:80px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;margin:0 auto 16px;">
                {{ strtoupper(substr($brand->name,0,1)) }}
            </div>
        @endif
        <h1 style="color:#fff;font-size:32px;font-weight:800;margin-bottom:8px;">{{ $brand->name }}</h1>
        <div style="color:#F39C12;font-size:16px;margin-bottom:8px;">
            @for($i=1;$i<=5;$i++) {{ $i<=round($brand->average_rating)?'★':'☆' }} @endfor
            <span style="color:rgba(255,255,255,.7);font-size:13px;">({{ $brand->total_reviews }} reviews)</span>
        </div>
        @if($brand->description)
        <p style="color:rgba(255,255,255,.8);max-width:600px;margin:0 auto;font-size:15px;">{{ $brand->description }}</p>
        @endif
    </div>
</div>
{{-- Brand Share Bar --}}
<div style="background:#fff;border-bottom:1px solid #eee;padding:12px 0;">
    <div class="container">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <span style="font-size:13px;font-weight:600;color:#888;">Share {{ $brand->name }}:</span>

            @php
                $shareUrl   = urlencode(url()->current());
                $shareText  = urlencode("Check out {$brand->name} on Orderer!");
                $shareImage = urlencode($brand->logo ?? '');
            @endphp

            {{-- Facebook --}}
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
               target="_blank" rel="noopener"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:20px;background:#1877F2;color:#fff;font-size:12px;font-weight:600;text-decoration:none;transition:opacity .2s;"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                </svg>
                Facebook
            </a>

            {{-- Twitter / X --}}
            <a href="https://twitter.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}"
               target="_blank" rel="noopener"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:20px;background:#000;color:#fff;font-size:12px;font-weight:600;text-decoration:none;transition:opacity .2s;"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                X (Twitter)
            </a>

            {{-- WhatsApp --}}
            <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
               target="_blank" rel="noopener"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:20px;background:#25D366;color:#fff;font-size:12px;font-weight:600;text-decoration:none;transition:opacity .2s;"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp
            </a>

            {{-- Telegram --}}
            <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}"
               target="_blank" rel="noopener"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:20px;background:#229ED9;color:#fff;font-size:12px;font-weight:600;text-decoration:none;transition:opacity .2s;"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                </svg>
                Telegram
            </a>

            {{-- LinkedIn --}}
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
               target="_blank" rel="noopener"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:20px;background:#0A66C2;color:#fff;font-size:12px;font-weight:600;text-decoration:none;transition:opacity .2s;"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
                LinkedIn
            </a>

            {{-- Copy Link --}}
            <button onclick="copyBrandLink(this)"
                    style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:20px;background:#f5f5f5;color:#333;font-size:12px;font-weight:600;border:1px solid #eee;cursor:pointer;transition:all .2s;"
                    onmouseover="this.style.background='#eee'" onmouseout="this.style.background='#f5f5f5'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                Copy Link
            </button>
        </div>
    </div>
</div>

<section class="section-padding-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <h4 style="font-weight:800;margin-bottom:24px;">Products from {{ $brand->name }}</h4>
                <div class="row">
                    @forelse($products as $product)
                    @php $img = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
                    <div class="col-12 col-sm-6 col-md-4 mb-4">
                        <div class="single-product-wrapper">
                            <div class="product-img">
                                <a href="{{ route('product.show', $product->slug) }}">
                                    <img src="{{ $img->image_url ?? asset('img/product-img/product-1.jpg') }}" alt="">
                                </a>
                                <div class="product-favourite">
                                    <a href="#" class="favme fa fa-heart" data-product="{{ $product->id }}"></a>
                                </div>
                            </div>
                            <div class="product-description">
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
                    @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <p>No products from this brand yet.</p>
                    </div>
                    @endforelse
                </div>
                <div>{{ $products->links() }}</div>
            </div>

            {{-- Reviews sidebar --}}
            <div class="col-lg-3">
                <div style="border:1px solid #eee;border-radius:10px;padding:20px;">
                    <h6 style="font-weight:700;margin-bottom:16px;">Customer Reviews</h6>
                    @forelse($brand->reviews->where('is_visible',true)->take(5) as $review)
                    <div style="padding-bottom:12px;margin-bottom:12px;border-bottom:1px solid #f5f5f5;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($review->user->first_name??'U',0,1)) }}
                            </div>
                            <div>
                                <p style="margin:0;font-weight:600;font-size:12px;">{{ $review->user->first_name??'Buyer' }}</p>
                                <span style="color:#F39C12;font-size:11px;">
                                    @for($i=1;$i<=5;$i++) {{ $i<=$review->rating?'★':'☆' }} @endfor
                                </span>
                            </div>
                        </div>
                        <p style="margin:0;font-size:12px;color:#666;">{{ Str::limit($review->review??'',80) }}</p>
                    </div>
                    @empty
                    <p class="text-muted" style="font-size:13px;">No reviews yet.</p>
                    @endforelse

                    @auth('web')
                    <form action="{{ route('brands.review', $brand->id) }}" method="POST" class="mt-3">
                        @csrf
                        <label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Leave a Review</label>
                        <select name="rating" class="form-control form-control-sm mb-2">
                            @for($i=5;$i>=1;$i--)
                            <option value="{{ $i }}">{{ $i }} Star{{ $i>1?'s':'' }}</option>
                            @endfor
                        </select>
                        <textarea name="review" class="form-control form-control-sm mb-2"
                                  rows="2" placeholder="Your review..."></textarea>
                        <button type="submit" class="btn essence-btn btn-sm w-100">Submit</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="btn essence-btn btn-sm w-100 mt-2">
                        Sign in to Review
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
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
            if (data.success) document.querySelectorAll('#cart-count,#cart-count-sidebar').forEach(el => el.textContent = data.count);
        });
    });
});
</script>

<script>
function copyBrandLink(btn) {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2ECC71" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Copied!`;
        btn.style.color = '#2ECC71';
        btn.style.borderColor = '#2ECC71';
        setTimeout(() => {
            btn.innerHTML = original;
            btn.style.color = '#333';
            btn.style.borderColor = '#eee';
        }, 2000);
    });
}
</script>
</body>
</html>
