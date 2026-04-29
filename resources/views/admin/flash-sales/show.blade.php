@extends('layouts.admin')
@section('title', 'Flash Sale Details')
@section('page_title', 'Flash Sale Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.flash-sales.index') }}">Flash Sales</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($flashSale->title, 40) }}</li>
@endsection
@section('page_actions')
    <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="feather-arrow-left me-1"></i> Back
    </a>
@endsection

@section('content')
@php
    $discount = round((($flashSale->original_price - $flashSale->sale_price) / $flashSale->original_price) * 100);
    $active   = $flashSale->is_active && now()->between($flashSale->starts_at, $flashSale->ends_at);
    $ended    = $flashSale->ends_at < now();
    $product  = $flashSale->product;
@endphp

<style>
    .product-images img {
        width: 90px; height: 90px;
        object-fit: cover; border-radius: 8px;
        border: 1px solid #e5e7eb; cursor: pointer;
    }
    .modal-image {
        position: fixed; top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.9); z-index: 999999;
        display: none; align-items: center; justify-content: center;
    }
    .modal-image img { max-width: 90%; max-height: 90%; object-fit: contain; }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="row">

    {{-- LEFT COLUMN ──────────────────────────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Flash Sale Summary --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="feather-zap me-2 text-warning"></i>{{ $flashSale->title }}
                </h5>
                @if($active)
                    <span class="badge orderer-badge badge-approved">Live</span>
                @elseif($ended)
                    <span class="badge orderer-badge badge-completed">Ended</span>
                @elseif(!$flashSale->is_active)
                    <span class="badge orderer-badge badge-pending">Paused</span>
                @else
                    <span class="badge orderer-badge badge-draft">Scheduled</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 text-center">
                        <p class="text-muted fs-12 mb-1">Original Price</p>
                        <p class="fw-semibold fs-14 mb-0 text-decoration-line-through text-muted">
                            ₦{{ number_format($flashSale->original_price, 2) }}
                        </p>
                    </div>
                    <div class="col-md-3 text-center">
                        <p class="text-muted fs-12 mb-1">Sale Price</p>
                        <p class="fw-bold fs-18 mb-0 text-success">
                            ₦{{ number_format($flashSale->sale_price, 2) }}
                        </p>
                    </div>
                    <div class="col-md-3 text-center">
                        <p class="text-muted fs-12 mb-1">Discount</p>
                        <span class="badge fs-14" style="background:#FADBD8;color:#E74C3C;">
                            -{{ $discount }}%
                        </span>
                    </div>
                    <div class="col-md-3 text-center">
                        <p class="text-muted fs-12 mb-1">Sold / Limit</p>
                        <p class="fw-semibold fs-14 mb-0">
                            {{ $flashSale->quantity_sold }} / {{ $flashSale->quantity_limit ?? '∞' }}
                        </p>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row g-3">
                    <div class="col-md-6">
                        <p class="text-muted fs-12 mb-1">Start</p>
                        <p class="fw-semibold mb-0">{{ $flashSale->starts_at->format('D, M d Y \a\t g:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted fs-12 mb-1">End</p>
                        <p class="fw-semibold mb-0">{{ $flashSale->ends_at->format('D, M d Y \a\t g:i A') }}</p>
                    </div>
                </div>

                @if(!$ended)
                <div class="mt-3">
                    <p class="text-muted fs-12 mb-1">Time Remaining</p>
                    <div id="countdown" class="fw-bold fs-14 text-primary"></div>
                </div>
                @endif
            </div>
        </div>

        {{-- Product Info --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                @if($product)
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Product Name</label>
                        <p class="fw-semibold mb-0">{{ $product->name }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Product Status</label>
                        <span class="badge orderer-badge" style="
                            @if($product->status === 'pending') background:#ffc107;color:#212529;
                            @elseif($product->status === 'approved') background:#28a745;color:#fff;
                            @elseif($product->status === 'rejected') background:#dc3545;color:#fff;
                            @else background:#6c757d;color:#fff; @endif
                            padding:5px 10px;border-radius:4px;font-size:12px;font-weight:600;">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Description</label>
                        <p class="mb-0">{{ $product->description ?? 'No description provided.' }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Listed Price</label>
                        <p class="fw-bold text-success fs-5 mb-0">₦{{ number_format($product->price, 2) }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Stock</label>
                        <p class="fw-bold mb-0">{{ $product->stock }} units</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Condition</label>
                        <p class="mb-0">{{ ucfirst($product->condition) }}</p>
                    </div>
                </div>

                {{-- Product Images --}}
                @if($product->images->count())
                <label class="text-muted d-block fs-12 mb-2">Product Images</label>
                <div class="d-flex flex-wrap gap-2 product-images">
                    @foreach($product->images as $image)
                    <img src="{{ $image->image_url }}" alt="Product image"
                         onclick="openImageModal('{{ $image->image_url }}')">
                    @endforeach
                </div>
                @endif

                @else
                <p class="text-muted mb-0">Product no longer exists.</p>
                @endif
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN ─────────────────────────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Flash Sale Actions --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Flash Sale Actions</h5>
            </div>
            <div class="card-body">
                {{-- Pause / Activate --}}
                @if(!$ended)
                <form action="{{ route('admin.flash-sales.toggle', $flashSale->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit"
                            class="btn w-100 {{ $flashSale->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                        <i class="feather-{{ $flashSale->is_active ? 'pause' : 'play' }} me-2"></i>
                        {{ $flashSale->is_active ? 'Pause Sale' : 'Activate Sale' }}
                    </button>
                </form>
                @endif

                {{-- Delete --}}
                <form action="{{ route('admin.flash-sales.destroy', $flashSale->id) }}"
                      method="POST" class="mt-2"
                      onsubmit="return confirm('Permanently delete this flash sale?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="feather-trash-2 me-2"></i> Delete Flash Sale
                    </button>
                </form>
            </div>
        </div>

        {{-- Seller Info --}}
        @if($product && $product->seller)
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Seller</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($product->seller->avatar)
                        <img src="{{ $product->seller->avatar }}"
                             style="width:46px;height:46px;border-radius:50%;object-fit:cover;" alt="">
                    @else
                        <div style="width:46px;height:46px;border-radius:50%;background:#2ECC71;color:#fff;
                                    display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700;">
                            {{ strtoupper(substr($product->seller->first_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="mb-0 fw-semibold">{{ $product->seller->business_name }}</p>
                        <small class="text-muted">{{ $product->seller->email }}</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Status</span>
                    <span class="badge {{ $product->seller->is_approved ? 'badge-approved' : 'badge-pending' }} orderer-badge">
                        {{ $product->seller->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted fs-13">Joined</span>
                    <span class="fs-13">{{ $product->seller->created_at->format('M d, Y') }}</span>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.sellers.show', $product->seller->id) }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        View Seller Profile
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Meta --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Meta</h5>
            </div>
            <div class="card-body">
                @if($product)
                <div class="d-flex justify-content-between">
                    <span class="text-muted fs-13">Product</span>
                    <a href="{{ route('admin.products.show', $product->id) }}" class="fs-13">
                        View Product
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- Image lightbox --}}
<div id="imageModal" class="modal-image" onclick="closeImageModal()">
    <img id="modalImage" src="" alt="">
</div>

<script>
    // Image modal
    function openImageModal(url) {
        document.getElementById('modalImage').src = url;
        document.getElementById('imageModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Countdown timer
    @if(!$ended)
    (function () {
        const end = new Date("{{ $flashSale->ends_at->toIso8601String() }}").getTime();
        const el  = document.getElementById('countdown');
        if (!el) return;

        function tick() {
            const diff = end - Date.now();
            if (diff <= 0) { el.textContent = 'Ended'; return; }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000)  / 60000);
            const s = Math.floor((diff % 60000)    / 1000);
            el.textContent = `${d}d ${h}h ${m}m ${s}s`;
            setTimeout(tick, 1000);
        }
        tick();
    })();
    @endif
</script>
@endsection