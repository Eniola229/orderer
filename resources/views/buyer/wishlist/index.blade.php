@extends('layouts.buyer')
@section('title', 'My Wishlist')
@section('page_title', 'My Wishlist')
@section('breadcrumb')
    <li class="breadcrumb-item active">Wishlist</li>
@endsection

@section('content')

@if($wishlists->count())
<div class="row">
    @foreach($wishlists as $item)
    @php
        $product = $item->wishlistable;
        if (!$product) continue;
        $img = $product->images->where('is_primary', true)->first() ?? $product->images->first();
        $priceNow = $product->sale_price ?? $product->price;
        $priceDrop = $item->price_at_save && $priceNow < $item->price_at_save;
    @endphp
    <div class="col-xxl-3 col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="position-relative">
                <a href="{{ route('product.show', $product->slug) }}">
                    <img src="{{ $img->image_url ?? asset('dashboard/assets/images/no-image.png') }}"
                         style="width:100%;height:200px;object-fit:cover;border-radius:8px 8px 0 0;"
                         alt="{{ $product->name }}">
                </a>
                @if($priceDrop)
                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                    Price Drop!
                </span>
                @endif
                <form action="{{ route('buyer.wishlist.remove', $item->id) }}"
                      method="POST"
                      class="position-absolute top-0 start-0 m-2">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light"
                            style="border-radius:50%;width:32px;height:32px;padding:0;"
                            title="Remove from wishlist">
                        <i class="feather-x" style="font-size:14px;"></i>
                    </button>
                </form>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="text-muted fs-12 mb-1">{{ $product->category->name ?? '' }}</p>
                <h6 class="fw-semibold mb-2">
                    <a href="{{ route('product.show', $product->slug) }}"
                       class="text-dark text-decoration-none">
                        {{ Str::limit($product->name, 50) }}
                    </a>
                </h6>
                <div class="mt-auto">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            @if($product->sale_price)
                            <span class="fw-bold text-success">${{ number_format($product->sale_price, 2) }}</span>
                            <small class="text-muted text-decoration-line-through ms-1">
                                ${{ number_format($product->price, 2) }}
                            </small>
                            @else
                            <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        @if($item->price_at_save)
                        <small class="text-muted">Saved at ${{ number_format($item->price_at_save, 2) }}</small>
                        @endif
                    </div>
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary w-100 btn-sm">
                            <i class="feather-shopping-cart me-1"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="d-flex justify-content-center">{{ $wishlists->links() }}</div>
@else
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="feather-heart mb-3 d-block" style="font-size:48px;color:#ddd;"></i>
        <h5 class="fw-semibold mb-2">Your wishlist is empty</h5>
        <p class="mb-3">Save products you love to buy them later.</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary">Browse Shop</a>
    </div>
</div>
@endif

@endsection
