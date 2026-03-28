@extends('layouts.seller')
@section('title', 'Product Details')
@section('page_title', $product->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($product->name, 40) }}</li>
@endsection

@section('content')

<style>
    .product-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .product-images img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        cursor: pointer;
    }
    .modal-image {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        z-index: 999999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .modal-image img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }
</style>

<div class="row">
    <div class="col-lg-8">

        {{-- Product Info Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Product Name</label>
                        <p class="fw-semibold mb-0">{{ $product->name }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Status</label>
                        <p class="mb-0">
                            <span class="badge orderer-badge" style="
                                @if($product->status === 'pending')
                                    background-color: #ffc107; color: #212529;
                                @elseif($product->status === 'approved')
                                    background-color: #28a745; color: #ffffff;
                                @elseif($product->status === 'rejected')
                                    background-color: #dc3545; color: #ffffff;
                                @elseif($product->status === 'suspended')
                                    background-color: #6c757d; color: #ffffff;
                                @else
                                    background-color: #6c757d; color: #ffffff;
                                @endif
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($product->status) }}
                            </span>
                            @if($product->status === 'rejected' && $product->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">
                                    {{ $product->rejection_reason }}
                                </p>
                            @endif
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Description</label>
                        <p class="mb-0">{{ $product->description ?? 'No description' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pricing Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Pricing & Stock</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Price</label>
                        <p class="fw-bold text-success fs-4 mb-0">${{ number_format($product->price, 2) }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Sale Price</label>
                        <p class="fw-bold mb-0">
                            @if($product->sale_price)
                                ${{ number_format($product->sale_price, 2) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Stock</label>
                        <p class="fw-bold mb-0">{{ $product->stock }} units</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Images Card --}}
        @if($product->images->count())
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Images</h5>
            </div>
            <div class="card-body">
                <div class="product-images">
                    @foreach($product->images as $image)
                    <div class="position-relative">
                        <img src="{{ $image->image_url }}" 
                             alt="Product image"
                             onclick="openImageModal('{{ $image->image_url }}')"
                             style="cursor: pointer;">
                        @if($loop->first)
                        <span class="badge bg-success position-absolute top-0 start-0 m-1" style="font-size: 10px;">Primary</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Videos Card --}}
        @if($product->videos->count())
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Videos</h5>
            </div>
            <div class="card-body">
                @foreach($product->videos as $video)
                <div class="mb-2">
                    <video controls style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                        <source src="{{ $video->video_url }}" type="video/mp4">
                    </video>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    <div class="col-lg-4">

        {{-- Category Info Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Category</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Category</span>
                    <span class="fw-semibold">{{ $product->category->name ?? '—' }}</span>
                </div>
                @if($product->subcategory)
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Subcategory</span>
                    <span class="fw-semibold">{{ $product->subcategory->name ?? '—' }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Additional Info Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Additional Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Condition</span>
                    <span>{{ ucfirst($product->condition) }}</span>
                </div>
                @if($product->weight_kg)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Weight</span>
                    <span>{{ $product->weight_kg }} kg</span>
                </div>
                @endif
                @if($product->location)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Location</span>
                    <span>{{ $product->location }}</span>
                </div>
                @endif
                @if($product->sku)
                <div class="d-flex justify-content-between">
                    <span class="text-muted">SKU</span>
                    <span><code>{{ $product->sku }}</code></span>
                </div>
                @endif
            </div>
        </div>

        {{-- Actions Card --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($product->status === 'pending' || $product->status === 'rejected')
                    <div class="d-grid gap-2">
                        <a href="{{ route('seller.products.edit', $product->id) }}" 
                           class="btn btn-warning">
                            <i class="feather-edit-2 me-2"></i> Edit Product
                        </a>
                        <form action="{{ route('seller.products.destroy', $product->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Delete this product permanently?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="feather-trash-2 me-2"></i> Delete Product
                            </button>
                        </form>
                    </div>
                @elseif($product->status === 'approved')
                    <div class="alert alert-info mb-3">
                        <i class="feather-info me-2"></i>
                        This product is live. To edit, it will need to be re-submitted for review.
                    </div>
                    <div class="d-grid">
                        <a href="{{ route('seller.products.edit', $product->id) }}" 
                           class="btn btn-warning">
                            <i class="feather-edit-2 me-2"></i> Request Changes (Re-submit)
                        </a>
                    </div>
                @endif
                
                <hr class="my-3">
                
                <a href="{{ route('seller.products.index') }}" 
                   class="btn btn-outline-secondary w-100">
                    <i class="feather-arrow-left me-2"></i> Back to Products
                </a>
            </div>
        </div>

    </div>
</div>

{{-- Image Modal --}}
<div id="imageModal" class="modal-image" onclick="closeImageModal()">
    <img id="modalImage" src="" alt="">
</div>

<script>
    function openImageModal(url) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('modalImage');
        img.src = url;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });
</script>

@endsection