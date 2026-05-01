@extends('layouts.admin')
@section('title', 'Product Details')
@section('page_title', $product->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
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
                        <p class="fw-bold text-success fs-4 mb-0">₦{{ number_format($product->price, 2) }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Sale Price</label>
                        <p class="fw-bold mb-0">
                            @if($product->sale_price)
                                ₦{{ number_format($product->sale_price, 2) }}
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
                    <img src="{{ $image->image_url }}" 
                         alt="Product image"
                         onclick="openImageModal('{{ $image->image_url }}')"
                         style="cursor: pointer;">
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Options Card --}}
        @include('partials._product_options_display')

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

        {{-- Seller Info Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Seller Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($product->seller->avatar)
                    <img src="{{ $product->seller->avatar }}" 
                         style="width:50px;height:50px;border-radius:50%;object-fit:cover;" alt="">
                    @else
                    <div style="width:50px;height:50px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;">
                        {{ strtoupper(substr($product->seller->first_name, 0, 1)) }}
                    </div>
                    @endif
                    <div>
                        <p class="mb-0 fw-semibold">{{ $product->seller->business_name }}</p>
                        <small class="text-muted">{{ $product->seller->email }}</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Seller Status</span>
                    <span class="badge {{ $product->seller->is_approved ? 'badge-approved' : 'badge-pending' }}">
                        {{ $product->seller->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Joined</span>
                    <span>{{ $product->seller->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

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
        @if(auth('admin')->user()->canModerateSellers())
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($product->status === 'pending')
                    <div class="d-grid gap-2">
                        <button type="button" 
                                class="btn btn-success"
                                onclick="openApproveModal('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                            <i class="feather-check me-2"></i> Approve Product
                        </button>
                        <button type="button" 
                                class="btn btn-danger"
                                onclick="openRejectModal('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                            <i class="feather-x me-2"></i> Reject Product
                        </button>
                    </div>
                @elseif($product->status === 'approved')
                    <div class="d-grid">
                        <button type="button" 
                                class="btn btn-warning"
                                onclick="openSuspendModal('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                            <i class="feather-pause me-2"></i> Suspend Product
                        </button>
                    </div>
                @elseif($product->status === 'suspended')
                    <div class="d-grid">
                        <form action="{{ route('admin.products.approve', $product->id) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="feather-play me-2"></i> Reinstate Product
                            </button>
                        </form>
                    </div>
                @endif
                @if(auth('admin')->user()->canModerateSellers())
                <div class="d-grid">
                <form action="{{ route('admin.products.feature', $product) }}" method="POST" onclick="event.stopPropagation()">
                    @csrf @method('PUT')
                    <button type="submit"
                            class="btn btn-sm {{ $product->is_featured ? 'btn-warning' : 'btn-outline-secondary' }}"
                            title="{{ $product->is_featured ? 'Remove from featured' : 'Mark as featured' }}">
                        <i class="feather-star"></i>
                    </button>
                </form>
            </div>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Image Modal --}}
<div id="imageModal" class="modal-image" onclick="closeImageModal()">
    <img id="modalImage" src="" alt="">
</div>

{{-- Approve Modal --}}
<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Approve Product</h5>
        </div>
        <form id="approveForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="approveProductInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Notes (Optional)</label>
                    <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Add any notes about this approval..."></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Approve Product</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Reject Product</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="rejectProductInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Rejection Reason</label>
                    <textarea name="reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Why is this product being rejected?" required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Reject Product</button>
            </div>
        </form>
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Suspend Product</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="suspendProductInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Reason (Optional)</label>
                    <textarea name="reason" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Reason for suspension..."></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #ffc107; color: #212529; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Suspend Product</button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    // Image Modal
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
    
    // Approve Modal Functions
    function openApproveModal(id, productName) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        const productInfo = document.getElementById('approveProductInfo');
        
        form.action = `/admin/products/${id}/approve`;
        productInfo.innerHTML = `<strong>${productName}</strong><br>This product will be approved and become visible to buyers.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Reject Modal Functions
    function openRejectModal(id, productName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const productInfo = document.getElementById('rejectProductInfo');
        
        form.action = `/admin/products/${id}/reject`;
        productInfo.innerHTML = `<strong>${productName}</strong><br>Please provide a reason for rejection.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Suspend Modal Functions
    function openSuspendModal(id, productName) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const productInfo = document.getElementById('suspendProductInfo');
        
        form.action = `/admin/products/${id}/suspend`;
        productInfo.innerHTML = `<strong>${productName}</strong><br>This product will be suspended and hidden from buyers.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeSuspendModal() {
        const modal = document.getElementById('suspendModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close modals when clicking outside
    document.getElementById('approveModal').addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
    document.getElementById('suspendModal').addEventListener('click', function(e) {
        if (e.target === this) closeSuspendModal();
    });
</script>

@endsection