@extends('layouts.seller')
@section('title', 'Service Details')
@section('page_title', $service->title)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.services.index') }}">Services</a></li>
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
        transition: transform 0.2s;
    }
    .portfolio-images img:hover {
        transform: scale(1.05);
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
    @media (max-width: 768px) {
        .portfolio-images img {
            width: 70px;
            height: 70px;
        }
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
                            @if($service->status === 'rejected' && $service->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">{{ $service->rejection_reason }}</p>
                            @endif
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

        @if($service->portfolio_images && is_array($service->portfolio_images) && count($service->portfolio_images) > 0)
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
                <h5 class="card-title mb-0">Category</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Category</span>
                    <span class="fw-semibold">{{ $service->category->name ?? '—' }}</span>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Service Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Created</span>
                    <span>{{ $service->created_at->format('M d, Y') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Last Updated</span>
                    <span>{{ $service->updated_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                @if($service->status === 'pending' || $service->status === 'rejected')
                    <div class="d-grid gap-2">
                        <a href="{{ route('seller.services.edit', $service->id) }}" 
                           class="btn btn-warning">
                            <i class="feather-edit-2 me-2"></i> Edit Service
                        </a>
                        <form action="{{ route('seller.services.destroy', $service->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Delete this service permanently?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="feather-trash-2 me-2"></i> Delete Service
                            </button>
                        </form>
                    </div>
                @elseif($service->status === 'approved')
                    <div class="alert alert-info mb-3">
                        <i class="feather-info me-2"></i>
                        This service is live. To edit, it will need to be re-submitted for review.
                    </div>
                    <div class="d-grid">
                        <a href="{{ route('seller.services.edit', $service->id) }}" 
                           class="btn btn-warning">
                            <i class="feather-edit-2 me-2"></i> Request Changes (Re-submit)
                        </a>
                    </div>
                @endif
                
                <hr class="my-3">
                
                <a href="{{ route('seller.services.index') }}" 
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