{{-- brand-show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $brand->name }} — Orderer</title>
    <link rel="icon" href="{{ asset('img/core-img/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orderer.css') }}">
</head>
<body>
@auth('web')@include('layouts.storefront.header-auth')@else@include('layouts.storefront.header-guest')@endauth
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
</body>
</html>
