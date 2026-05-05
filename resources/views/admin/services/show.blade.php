@extends('layouts.admin')
@section('title', 'Service Details')
@section('page_title', $service->title)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($service->title, 40) }}</li>
@endsection

@section('content')

<style>
    .portfolio-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .portfolio-images img {
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
                <h5 class="card-title mb-0">Service Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Service Title</label>
                        <p class="fw-semibold mb-0">{{ $service->title }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Status</label>
                        <p class="mb-0">
                            <span class="badge orderer-badge" style="
                                @if($service->status === 'pending')
                                    background-color: #ffc107; color: #212529;
                                @elseif($service->status === 'approved')
                                    background-color: #28a745; color: #ffffff;
                                @elseif($service->status === 'rejected')
                                    background-color: #dc3545; color: #ffffff;
                                @elseif($service->status === 'suspended')
                                    background-color: #6c757d; color: #ffffff;
                                @endif
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($service->status) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Description</label>
                        <p class="mb-0">{{ $service->description ?? 'No description' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Pricing & Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Pricing Type</label>
                        <p class="fw-semibold mb-0">{{ ucfirst($service->pricing_type) }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Price</label>
                        <p class="fw-bold text-success mb-0">
                            @if($service->pricing_type === 'negotiable')
                                Negotiable
                            @else
                                ₦{{ number_format($service->price, 2) }}
                                @if($service->pricing_type === 'hourly')
                                    /hour
                                @endif
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Delivery Time</label>
                        <p class="fw-semibold mb-0">{{ $service->delivery_time ?? 'Not specified' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Location</label>
                        <p class="fw-semibold mb-0">{{ $service->location ?? 'Not specified' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Revisions</label>
                        <p class="fw-semibold mb-0">{{ $service->revisions ?? 'Not specified' }}</p>
                    </div>
                </div>
                @if($service->portfolio_url)
                <div class="col-md-6 mb-3">
                    <label class="text-muted d-block fs-12 mb-1">Portfolio URL</label>
                    <a href="{{ $service->portfolio_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="feather-external-link me-1"></i> View Portfolio
                    </a>
                </div>
                @endif
            </div>
        </div>

        @if($service->portfolio_images && count($service->portfolio_images) > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Portfolio Images</h5>
            </div>
            <div class="card-body">
                <div class="portfolio-images">
                    @foreach($service->portfolio_images as $image)
                    <img src="{{ $image['url'] }}" 
                         alt="Portfolio image"
                         onclick="openImageModal('{{ $image['url'] }}')"
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
                    @if($service->seller->avatar)
                    <img src="{{ $service->seller->avatar }}" 
                         style="width:50px;height:50px;border-radius:50%;object-fit:cover;" alt="">
                    @else
                    <div style="width:50px;height:50px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;">
                        {{ strtoupper(substr($service->seller->first_name, 0, 1)) }}
                    </div>
                    @endif
                    <div>
                        <p class="mb-0 fw-semibold">{{ $service->seller->business_name }}</p>
                        <small class="text-muted">{{ $service->seller->email }}</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Seller Status</span>
                    <span class="badge {{ $service->seller->is_approved ? 'badge-approved' : 'badge-pending' }}">
                        {{ $service->seller->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Joined</span>
                    <span>{{ $service->seller->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Category</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Category</span>
                    <span class="fw-semibold">{{ $service->category->name ?? '—' }}</span>
                </div>
            </div>
        </div>

        @if($service->status === 'rejected' && $service->rejection_reason)
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">Rejection Reason</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $service->rejection_reason }}</p>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($service->status === 'pending' && auth('admin')->user()->canModerateSellers())
                    <div class="d-grid gap-2">
                        <button type="button" 
                                class="btn btn-success"
                                onclick="openApproveModal('{{ $service->id }}', '{{ addslashes($service->title) }}')">
                            <i class="feather-check me-2"></i> Approve Service
                        </button>
                        <button type="button" 
                                class="btn btn-danger"
                                onclick="openRejectModal('{{ $service->id }}', '{{ addslashes($service->title) }}')">
                            <i class="feather-x me-2"></i> Reject Service
                        </button>
                    </div>
                @elseif($service->status === 'approved' && auth('admin')->user()->canModerateSellers())
                    <div class="d-grid">
                        <button type="button" 
                                class="btn btn-warning"
                                onclick="openSuspendModal('{{ $service->id }}', '{{ addslashes($service->title) }}')">
                            <i class="feather-pause me-2"></i> Suspend Service
                        </button>
                    </div>
                @elseif($service->status === 'suspended' && auth('admin')->user()->canModerateSellers())
                    <div class="d-grid">
                        <form action="{{ route('admin.services.approve', $service->id) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="feather-play me-2"></i> Reinstate Service
                            </button>
                        </form>
                    </div>
                @endif
                
                <hr class="my-3">
                
                <a href="{{ route('admin.services.index') }}" 
                   class="btn btn-outline-secondary w-100">
                    <i class="feather-arrow-left me-2"></i> Back to Services
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
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Approve Service</h5>
        </div>
        <form id="approveForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="approveServiceInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Approve Service</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Reject Service</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="rejectServiceInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Rejection Reason</label>
                    <textarea name="reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Why is this service being rejected?" required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Reject Service</button>
            </div>
        </form>
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Suspend Service</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="suspendServiceInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #ffc107; color: #212529; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Suspend Service</button>
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
    
    function openApproveModal(id, serviceName) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        const serviceInfo = document.getElementById('approveServiceInfo');
        
        form.action = "{{ route('admin.services.approve', ['service' => '__ID__']) }}".replace('__ID__', id);
        serviceInfo.innerHTML = `<strong>${serviceName}</strong><br>This service will be approved and become visible to buyers.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function openRejectModal(id, serviceName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const serviceInfo = document.getElementById('rejectServiceInfo');
        
        form.action = "{{ route('admin.services.reject', ['service' => '__ID__']) }}".replace('__ID__', id);

        serviceInfo.innerHTML = `<strong>${serviceName}</strong><br>Please provide a reason for rejection.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function openSuspendModal(id, serviceName) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const serviceInfo = document.getElementById('suspendServiceInfo');
        
        form.action = "{{ route('admin.services.suspend', ['service' => '__ID__']) }}".replace('__ID__', id);

        serviceInfo.innerHTML = `<strong>${serviceName}</strong><br>This service will be suspended and hidden from buyers.`;
        
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