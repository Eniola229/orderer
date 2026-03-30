@extends('layouts.seller')
@section('title', 'Property Details')
@section('page_title', 'Property Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.houses.index') }}">Properties</a></li>
    <li class="breadcrumb-item active">{{ $house->title }}</li>
@endsection
@section('page_actions')
    <a href="{{ route('seller.houses.index') }}" class="btn btn-secondary btn-sm">
        <i class="feather-arrow-left me-1"></i> Back to Properties
    </a>
 
@endsection

@section('content')
<style>
    .property-gallery {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
    }
    .main-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .main-image:hover {
        transform: scale(1.02);
    }
    .thumbnail-list {
        display: flex;
        gap: 12px;
        margin-top: 15px;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .thumbnail {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s;
    }
    .thumbnail.active {
        border-color: #0d6efd;
        transform: scale(1.05);
    }
    .thumbnail:hover {
        border-color: #0d6efd;
    }
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .info-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .info-value {
        font-size: 16px;
        font-weight: 500;
        color: #212529;
    }
    .feature-badge {
        background: #f8f9fa;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        margin: 0 5px 5px 0;
        display: inline-block;
    }
    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    .modal-image {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }
</style>

<div class="row">
    <div class="col-lg-8">
        {{-- Image Gallery --}}
        <div class="property-gallery mb-4">
            @if($house->images->count() > 0)
                <img id="mainImage" src="{{ $house->images->where('is_primary', true)->first()?->image_url ?? $house->images->first()->image_url }}"
                     class="main-image" alt="{{ $house->title }}" onclick="openImageModal(this.src)">
                <div class="thumbnail-list">
                    @foreach($house->images as $image)
                        <img src="{{ $image->image_url }}"
                             class="thumbnail {{ ($house->images->where('is_primary', true)->first()?->id == $image->id || (!$house->images->where('is_primary', true)->first() && $loop->first)) ? 'active' : '' }}"
                             onclick="changeMainImage(this)"
                             data-image="{{ $image->image_url }}">
                    @endforeach
                </div>
            @else
                <div class="main-image" style="background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                    <i class="feather-image" style="font-size: 80px; color: #dee2e6;"></i>
                </div>
            @endif
        </div>

        {{-- Property Details --}}
        <div class="info-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h3 class="mb-2">{{ $house->title }}</h3>
                    <div class="d-flex align-items-center gap-3 text-muted">
                        <span><i class="feather-map-pin me-1"></i> {{ $house->address }}, {{ $house->city }}, {{ $house->state }}</span>
                        <span><i class="feather-calendar me-1"></i> Listed {{ $house->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
                <div>
                    <span class="badge orderer-badge badge-{{ $house->status }} status-badge">
                        {{ ucfirst($house->status) }}
                    </span>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="info-label">Price</div>
                    <div class="info-value fs-4 fw-bold text-primary">₦{{ number_format($house->price, 2) }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="info-label">Listing Type</div>
                    <div class="info-value">{{ ucfirst($house->listing_type) }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="info-label">Property Type</div>
                    <div class="info-value">{{ ucfirst($house->property_type) }}</div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="info-label">Bedrooms</div>
                    <div class="info-value">
                        <i class="feather-home me-1"></i> {{ $house->bedrooms ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="info-label">Bathrooms</div>
                    <div class="info-value">
                        <i class="feather-droplet me-1"></i> {{ $house->bathrooms ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="info-label">Toilets</div>
                    <div class="info-value">
                        <i class="feather-grid me-1"></i> {{ $house->toilets ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="info-label">Size (sqm)</div>
                    <div class="info-value">
                        <i class="feather-maximize me-1"></i> {{ $house->size_sqm ? number_format($house->size_sqm) . ' m²' : 'N/A' }}
                    </div>
                </div>
            </div>

            @if($house->description)
                <hr>
                <div class="mb-3">
                    <div class="info-label">Description</div>
                    <div class="info-value" style="line-height: 1.6;">{{ $house->description }}</div>
                </div>
            @endif

            @if($house->features && is_array($house->features) && count($house->features) > 0)
                <hr>
                <div class="mb-3">
                    <div class="info-label">Features & Amenities</div>
                    <div>
                        @foreach($house->features as $feature)
                            <span class="feature-badge">
                                <i class="feather-check-circle me-1 text-success"></i> {{ $feature }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($house->video_tour_url)
                <hr>
                <div class="mb-3">
                    <div class="info-label">Video Tour</div>
                    <a href="{{ $house->video_tour_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="feather-video me-1"></i> Watch Video Tour
                    </a>
                </div>
            @endif

            @if($house->status === 'rejected' && $house->rejection_reason)
                <div class="alert alert-danger mt-3">
                    <i class="feather-alert-triangle me-2"></i>
                    <strong>Rejection Reason:</strong> {{ $house->rejection_reason }}
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Seller Information --}}
        <div class="info-card">
            <h5 class="mb-3">Seller Information</h5>
            <div class="d-flex align-items-center mb-3">
                <div style="width: 60px; height: 60px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                    <i class="feather-user" style="font-size: 30px; color: #6c757d;"></i>
                </div>
                <div>
                    <h6 class="mb-1">{{ $house->seller->business_name ?? $house->seller->name ?? 'Seller' }}</h6>
                    <small class="text-muted">Member since {{ $house->seller->created_at->format('M Y') }}</small>
                </div>
            </div>
            <hr>
            <div class="mb-2">
                <i class="feather-mail me-2 text-muted"></i> {{ $house->seller->email ?? 'Not provided' }}
            </div>
            @if($house->seller->phone)
                <div class="mb-2">
                    <i class="feather-phone me-2 text-muted"></i> {{ $house->seller->phone }}
                </div>
            @endif
            @if($house->seller->business_address)
                <div>
                    <i class="feather-map-pin me-2 text-muted"></i> {{ $house->seller->business_address }}
                </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="info-card">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="d-grid gap-2">
               
                <form action="{{ route('seller.houses.destroy', $house->id) }}" method="POST"
                      onsubmit="return confirm('Delete this property listing permanently?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="feather-trash-2 me-2"></i> Delete Property
                    </button>
                </form>
                @if($house->status == 'approved')
                    <a href="{{ route('houses.show', $house->slug) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="feather-eye me-2"></i> View on Frontend
                    </a>
                @endif
            </div>
        </div>

        {{-- Property Stats --}}
        <div class="info-card">
            <h5 class="mb-3">Property Statistics</h5>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Listing ID:</span>
                <span class="fw-semibold">#{{ $house->id }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Date Added:</span>
                <span>{{ $house->created_at->format('M d, Y') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Last Updated:</span>
                <span>{{ $house->updated_at->format('M d, Y') }}</span>
            </div>
            @if($house->status == 'approved')
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Status:</span>
                    <span class="text-success">Active Listing</span>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Image Modal --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-end">
                <button type="button" class="btn btn-light mb-2" data-bs-dismiss="modal" style="border-radius: 50%;">
                    <i class="feather-x"></i>
                </button>
                <img id="modalImage" src="" class="modal-image w-100" alt="">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function changeMainImage(element) {
        // Update main image
        const mainImage = document.getElementById('mainImage');
        mainImage.src = element.dataset.image;
        
        // Update active state
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        element.classList.add('active');
    }
    
    function openImageModal(imageUrl) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageUrl;
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }
    
    // Keyboard navigation for gallery
    let currentIndex = 0;
    const images = @json($house->images->pluck('image_url'));
    
    document.addEventListener('keydown', function(e) {
        if (document.getElementById('imageModal').classList.contains('show')) {
            if (e.key === 'ArrowLeft') {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                document.getElementById('modalImage').src = images[currentIndex];
            } else if (e.key === 'ArrowRight') {
                currentIndex = (currentIndex + 1) % images.length;
                document.getElementById('modalImage').src = images[currentIndex];
            }
        }
    });
</script>
@endpush