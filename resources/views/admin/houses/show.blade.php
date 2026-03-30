@extends('layouts.admin')
@section('title', 'Property Details')
@section('page_title', $house->title)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.houses.index') }}">Properties</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($house->title, 40) }}</li>
@endsection

@section('content')

<style>
    .property-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .property-images img {
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

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Property Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Property Title</label>
                        <p class="fw-semibold mb-0">{{ $house->title }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Status</label>
                        <p class="mb-0">
                            <span class="badge orderer-badge" style="
                                @if($house->status === 'pending')
                                    background-color: #ffc107; color: #212529;
                                @elseif($house->status === 'approved')
                                    background-color: #28a745; color: #ffffff;
                                @elseif($house->status === 'rejected')
                                    background-color: #dc3545; color: #ffffff;
                                @elseif($house->status === 'suspended')
                                    background-color: #6c757d; color: #ffffff;
                                @endif
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($house->status) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Description</label>
                        <p class="mb-0">{{ $house->description ?? 'No description' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Property Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Listing Type</label>
                        <p class="fw-semibold mb-0">{{ ucfirst($house->listing_type) }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Property Type</label>
                        <p class="fw-semibold mb-0">{{ ucfirst($house->property_type) }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Price</label>
                        <p class="fw-bold text-success fs-4 mb-0">
                            @if($house->listing_type === 'rent')
                                ₦{{ number_format($house->price, 2) }}/month
                            @else
                                ₦{{ number_format($house->price, 2) }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Location</label>
                        <p class="fw-semibold mb-0">{{ $house->location }}</p>
                        <small class="text-muted">{{ $house->city }}, {{ $house->state }}, {{ $house->country }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Address</label>
                        <p class="fw-semibold mb-0">{{ $house->address ?? 'Not specified' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Bedrooms</label>
                        <p class="fw-semibold mb-0">{{ $house->bedrooms ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Bathrooms</label>
                        <p class="fw-semibold mb-0">{{ $house->bathrooms ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Toilets</label>
                        <p class="fw-semibold mb-0">{{ $house->toilets ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Size (sqm)</label>
                        <p class="fw-semibold mb-0">{{ $house->size_sqm ?? 'N/A' }} sqm</p>
                    </div>
                </div>
            </div>
        </div>

        @if($house->features && count($house->features) > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Features & Amenities</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($house->features as $feature)
                    <span class="badge bg-light text-dark p-2">{{ $feature }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($house->video_tour_url)
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Video Tour</h5>
            </div>
            <div class="card-body">
                <a href="{{ $house->video_tour_url }}" target="_blank" class="text-primary">
                    <i class="feather-video me-2"></i> {{ $house->video_tour_url }}
                </a>
            </div>
        </div>
        @endif

        @if($house->images->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Property Images</h5>
            </div>
            <div class="card-body">
                <div class="property-images">
                    @foreach($house->images as $image)
                    <img src="{{ $image->image_url }}" 
                         alt="Property image"
                         onclick="openImageModal('{{ $image->image_url }}')"
                         style="cursor: pointer;">
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>

    <div class="col-lg-4">

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Seller Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($house->seller->avatar)
                    <img src="{{ $house->seller->avatar }}" 
                         style="width:50px;height:50px;border-radius:50%;object-fit:cover;" alt="">
                    @else
                    <div style="width:50px;height:50px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;">
                        {{ strtoupper(substr($house->seller->first_name, 0, 1)) }}
                    </div>
                    @endif
                    <div>
                        <p class="mb-0 fw-semibold">{{ $house->seller->business_name }}</p>
                        <small class="text-muted">{{ $house->seller->email }}</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Seller Status</span>
                    <span class="badge {{ $house->seller->is_approved ? 'badge-approved' : 'badge-pending' }}">
                        {{ $house->seller->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Joined</span>
                    <span>{{ $house->seller->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Listing Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Created</span>
                    <span>{{ $house->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Last Updated</span>
                    <span>{{ $house->updated_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>

        @if($house->status === 'rejected' && $house->rejection_reason)
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">Rejection Reason</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $house->rejection_reason }}</p>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($house->status === 'pending' && auth('admin')->user()->canModerateSellers())
                    <div class="d-grid gap-2">
                        <button type="button" 
                                class="btn btn-success"
                                onclick="openApproveModal('{{ $house->id }}', '{{ addslashes($house->title) }}')">
                            <i class="feather-check me-2"></i> Approve Property
                        </button>
                        <button type="button" 
                                class="btn btn-danger"
                                onclick="openRejectModal('{{ $house->id }}', '{{ addslashes($house->title) }}')">
                            <i class="feather-x me-2"></i> Reject Property
                        </button>
                    </div>
                @elseif($house->status === 'approved' && auth('admin')->user()->canModerateSellers())
                    <div class="d-grid">
                        <button type="button" 
                                class="btn btn-warning"
                                onclick="openSuspendModal('{{ $house->id }}', '{{ addslashes($house->title) }}')">
                            <i class="feather-pause me-2"></i> Suspend Property
                        </button>
                    </div>
                @elseif($house->status === 'suspended' && auth('admin')->user()->canModerateSellers())
                    <div class="d-grid">
                        <form action="{{ route('admin.houses.approve', $house->id) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="feather-play me-2"></i> Reinstate Property
                            </button>
                        </form>
                    </div>
                @endif
                
                <hr class="my-3">
                
                <a href="{{ route('admin.houses.index') }}" 
                   class="btn btn-outline-secondary w-100">
                    <i class="feather-arrow-left me-2"></i> Back to Properties
                </a>
            </div>
        </div>

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
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Approve Property</h5>
        </div>
        <form id="approveForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="approvePropertyInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Approve Property</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Reject Property</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="rejectPropertyInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Rejection Reason</label>
                    <textarea name="reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Why is this property being rejected?" required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Reject Property</button>
            </div>
        </form>
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Suspend Property</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="suspendPropertyInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #ffc107; color: #212529; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Suspend Property</button>
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
    
    function openApproveModal(id, propertyName) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        const propertyInfo = document.getElementById('approvePropertyInfo');
        
        form.action = `/admin/houses/${id}/approve`;
        propertyInfo.innerHTML = `<strong>${propertyName}</strong><br>This property will be approved and become visible to buyers.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function openRejectModal(id, propertyName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const propertyInfo = document.getElementById('rejectPropertyInfo');
        
        form.action = `/admin/houses/${id}/reject`;
        propertyInfo.innerHTML = `<strong>${propertyName}</strong><br>Please provide a reason for rejection.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function openSuspendModal(id, propertyName) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const propertyInfo = document.getElementById('suspendPropertyInfo');
        
        form.action = `/admin/houses/${id}/suspend`;
        propertyInfo.innerHTML = `<strong>${propertyName}</strong><br>This property will be suspended and hidden from buyers.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeSuspendModal() {
        const modal = document.getElementById('suspendModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    document.getElementById('approveModal').addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
    document.getElementById('suspendModal').addEventListener('click', function(e) {
        if (e.target === this) closeSuspendModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
            closeApproveModal();
            closeRejectModal();
            closeSuspendModal();
        }
    });
</script>

@endsection