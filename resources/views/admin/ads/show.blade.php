@extends('layouts.admin')
@section('title', 'Ad Details')
@section('page_title', $ad->title)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.ads.index') }}">Ads</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($ad->title, 40) }}</li>
@endsection

@section('content')

<style>
    .media-preview {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
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
    .modal-image img, .modal-image video {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }
</style>

<div class="row">
    <div class="col-lg-8">

        {{-- Ad Info Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Ad Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Ad Title</label>
                        <p class="fw-semibold mb-0">{{ $ad->title }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Status</label>
                        <p class="mb-0">
                            <span class="badge orderer-badge" style="
                                @if($ad->status === 'pending')
                                    background-color: #ffc107; color: #212529;
                                @elseif($ad->status === 'active')
                                    background-color: #28a745; color: #ffffff;
                                @elseif($ad->status === 'paused')
                                    background-color: #17a2b8; color: #ffffff;
                                @elseif($ad->status === 'rejected')
                                    background-color: #dc3545; color: #ffffff;
                                @elseif($ad->status === 'expired')
                                    background-color: #6c757d; color: #ffffff;
                                @endif
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($ad->status) }}
                            </span>
                            @if($ad->status === 'rejected' && $ad->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">{{ $ad->rejection_reason }}</p>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ad Media Card --}}
        @if($ad->media_url)
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Ad Media</h5>
            </div>
            <div class="card-body text-center">
                @if($ad->media_type === 'image')
                    <img src="{{ $ad->media_url }}" 
                         class="media-preview" 
                         style="max-width: 100%; max-height: 300px; cursor: pointer;"
                         onclick="openImageModal('{{ $ad->media_url }}')"
                         alt="Ad image">
                @elseif($ad->media_type === 'video')
                    <video controls class="media-preview" style="max-width: 100%; max-height: 300px;">
                        <source src="{{ $ad->media_url }}" type="video/mp4">
                    </video>
                @endif
            </div>
        </div>
        @endif

        {{-- Ad Details Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Ad Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Ad Category</label>
                        <p class="fw-semibold mb-0">{{ $ad->adCategory->name ?? '—' }}</p>
                        <small class="text-muted">Type: {{ $ad->adCategory->type ?? '—' }}</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Banner Slot</label>
                        <p class="fw-semibold mb-0">{{ $ad->bannerSlot->name ?? '—' }}</p>
                        @if($ad->bannerSlot)
                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $ad->bannerSlot->location ?? '')) }}</small>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Click URL</label>
                        @if($ad->click_url)
                            <a href="{{ $ad->click_url }}" target="_blank" class="text-primary">
                                {{ Str::limit($ad->click_url, 50) }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Promoting</label>
                        @php
                            $promotable = $ad->promotable;
                        @endphp
                        @if($promotable)
                            <p class="fw-semibold mb-0">
                                @if($ad->promotable_type === 'App\Models\Product')
                                    Product: {{ $promotable->name ?? '—' }}
                                @elseif($ad->promotable_type === 'App\Models\ServiceListing')
                                    Service: {{ $promotable->title ?? '—' }}
                                @elseif($ad->promotable_type === 'App\Models\HouseListing')
                                    Property: {{ $promotable->title ?? '—' }}
                                @elseif($ad->promotable_type === 'App\Models\Seller')
                                    Brand/Store
                                @endif
                            </p>
                        @else
                            <p class="text-muted">—</p>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Seller</label>
                        <a href="{{ route('admin.sellers.show', $ad->seller_id) }}" class="text-primary">
                            {{ $ad->seller->business_name ?? '—' }}
                        </a>
                        <small class="text-muted d-block">{{ $ad->seller->email ?? '' }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Budget & Schedule Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Budget & Schedule</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Budget</label>
                        <p class="fw-bold text-success fs-4 mb-0">${{ number_format($ad->budget, 2) }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Amount Spent</label>
                        <p class="fw-semibold mb-0">${{ number_format($ad->amount_spent, 2) }}</p>
                        <small class="text-muted">{{ $ad->budget > 0 ? round(($ad->amount_spent / $ad->budget) * 100, 1) : 0 }}% used</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Cost Per Day</label>
                        <p class="fw-semibold mb-0">${{ number_format($ad->cost_per_day, 2) }}/day</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Duration</label>
                        <p class="fw-semibold mb-0">
                            {{ \Carbon\Carbon::parse($ad->start_date)->format('M d, Y') }} - 
                            {{ \Carbon\Carbon::parse($ad->end_date)->format('M d, Y') }}
                        </p>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($ad->start_date)->diffInDays($ad->end_date) + 1 }} days</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Performance Stats Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Performance Stats</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Impressions</label>
                        <p class="fw-bold fs-3 mb-0 text-primary">{{ number_format($ad->total_impressions ?? 0) }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Clicks</label>
                        <p class="fw-bold fs-3 mb-0 text-success">{{ number_format($ad->total_clicks ?? 0) }}</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">CTR</label>
                        <p class="fw-bold fs-3 mb-0">
                            @if(($ad->total_impressions ?? 0) > 0)
                                {{ round(($ad->total_clicks ?? 0) / ($ad->total_impressions ?? 0) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Conversions</label>
                        <p class="fw-bold fs-3 mb-0 text-warning">{{ number_format($ad->total_conversions ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-4">

        {{-- Seller Info Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Seller Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($ad->seller->avatar)
                    <img src="{{ $ad->seller->avatar }}" 
                         style="width:50px;height:50px;border-radius:50%;object-fit:cover;" alt="">
                    @else
                    <div style="width:50px;height:50px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;">
                        {{ strtoupper(substr($ad->seller->first_name, 0, 1)) }}
                    </div>
                    @endif
                    <div>
                        <p class="mb-0 fw-semibold">{{ $ad->seller->business_name }}</p>
                        <small class="text-muted">{{ $ad->seller->email }}</small>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Seller Status</span>
                    <span class="badge {{ $ad->seller->is_approved ? 'badge-approved' : 'badge-pending' }}">
                        {{ $ad->seller->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Ads Balance</span>
                    <span class="fw-bold text-success">${{ number_format($ad->seller->ads_balance ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Dates Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Dates</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Created</span>
                    <span>{{ $ad->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Last Updated</span>
                    <span>{{ $ad->updated_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Actions Card --}}
        @if(auth('admin')->user()->canManageAds())
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($ad->status === 'pending')
                    <div class="d-grid gap-2">
                        <button type="button" 
                                class="btn btn-success"
                                onclick="openApproveModal('{{ $ad->id }}', '{{ addslashes($ad->title) }}')">
                            <i class="feather-check me-2"></i> Approve Ad
                        </button>
                        <button type="button" 
                                class="btn btn-danger"
                                onclick="openRejectModal('{{ $ad->id }}', '{{ addslashes($ad->title) }}')">
                            <i class="feather-x me-2"></i> Reject Ad
                        </button>
                    </div>
                @elseif($ad->status === 'active')
                    <div class="d-grid">
                        <button type="button" 
                                class="btn btn-warning"
                                onclick="openSuspendModal('{{ $ad->id }}', '{{ addslashes($ad->title) }}')">
                            <i class="feather-pause me-2"></i> Suspend Ad
                        </button>
                    </div>
                @elseif($ad->status === 'paused')
                    <div class="d-grid">
                        <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="feather-play me-2"></i> Reinstate Ad
                            </button>
                        </form>
                    </div>
                @endif
                
                <hr class="my-3">
                
                <a href="{{ route('admin.ads.index') }}" 
                   class="btn btn-outline-secondary w-100">
                    <i class="feather-arrow-left me-2"></i> Back to Ads
                </a>
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
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Approve Ad</h5>
        </div>
        <form id="approveForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="approveAdInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Approve Ad</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Reject Ad</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="rejectAdInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Rejection Reason</label>
                    <textarea name="rejection_reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Why is this ad being rejected?" required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Reject Ad</button>
            </div>
        </form>
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Suspend Ad</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="suspendAdInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #ffc107; color: #212529; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Suspend Ad</button>
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
    
    function openApproveModal(id, adTitle) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        const adInfo = document.getElementById('approveAdInfo');
        
        form.action = `/admin/ads/${id}/approve`;
        adInfo.innerHTML = `<strong>${adTitle}</strong><br>This ad will be approved and activated.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function openRejectModal(id, adTitle) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const adInfo = document.getElementById('rejectAdInfo');
        
        form.action = `/admin/ads/${id}/reject`;
        adInfo.innerHTML = `<strong>${adTitle}</strong><br>Please provide a reason for rejection.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function openSuspendModal(id, adTitle) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const adInfo = document.getElementById('suspendAdInfo');
        
        form.action = `/admin/ads/${id}/suspend`;
        adInfo.innerHTML = `<strong>${adTitle}</strong><br>This ad will be paused.`;
        
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