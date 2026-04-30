@extends('layouts.seller')
@section('title', 'Edit Service')
@section('page_title', 'Edit Service')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.services.index') }}">Services</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

@if(session('warning'))
<div class="alert alert-warning mb-4">
    <i class="feather-alert-triangle me-2"></i>
    {{ session('warning') }}
</div>
@endif

<form action="{{ route('seller.services.update', $service->id) }}" 
      method="POST"  
      enctype="multipart/form-data"
      id="serviceForm">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Service Information</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Service Title <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $service->title) }}"
                               placeholder="e.g. Professional Logo Design"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" 
                                    {{ old('category_id', $service->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Location</label>
                            <input type="text"
                                   name="location"
                                   class="form-control"
                                   value="{{ old('location', $service->location) }}"
                                   placeholder="e.g. Lagos, Nigeria or Remote">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Description <span class="text-danger">*</span>
                        </label>
                        <textarea name="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="6"
                                  placeholder="Describe your service in detail"
                                  required>{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Pricing Type</label>
                            <select name="pricing_type" id="pricingType" class="form-select">
                                <option value="fixed" {{ old('pricing_type', $service->pricing_type) == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                                <option value="hourly" {{ old('pricing_type', $service->pricing_type) == 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                                <option value="negotiable" {{ old('pricing_type', $service->pricing_type) == 'negotiable' ? 'selected' : '' }}>Negotiable</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-4" id="priceField">
                            <label class="form-label fw-bold">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number"
                                       name="price"
                                       class="form-control"
                                       value="{{ old('price', $service->price) }}"
                                       step="0.01"
                                       min="0.01"
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Delivery Time</label>
                            <input type="text"
                                   name="delivery_time"
                                   class="form-control"
                                   value="{{ old('delivery_time', $service->delivery_time) }}"
                                   placeholder="e.g. 3-5 days">
                        </div>
                    </div>

                </div>
            </div>

            {{-- Portfolio Images --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Portfolio Images</h5>
                </div>
                <div class="card-body">
                    
                    {{-- Current Images --}}
                    @if($service->portfolio_images && count($service->portfolio_images) > 0)
                    <div class="mb-4">
                        <label class="form-label fw-bold">Current Images</label>
                        <div class="d-flex flex-wrap gap-2" id="currentImages">
                            @foreach($service->portfolio_images as $index => $image)
                            <div class="position-relative" style="width:100px;height:100px;" id="image-{{ $index }}">
                                <img src="{{ $image['url'] }}" 
                                     style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #eee;" 
                                     alt="">
                                <button type="button" 
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0"
                                        style="width:22px;height:22px;font-size:12px;margin:-8px -8px 0 0;"
                                        onclick="markForRemoval('{{ $image['public_id'] }}', this)">
                                    <i class="feather-x"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="remove_images" id="removeImages" value="">
                    </div>
                    @endif

                    {{-- Upload New Images --}}
                    <div class="border border-dashed rounded p-4 text-center position-relative"
                         style="cursor:pointer;">
                        <input type="file"
                               name="portfolio[]"
                               id="imageInput"
                               accept="image/jpg,image/jpeg,image/png"
                               multiple
                               style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                        <i class="feather-upload-cloud mb-2 d-block text-muted" style="font-size:32px;"></i>
                        <p class="text-muted mb-1">Add new portfolio images</p>
                        <small class="text-muted">JPG, PNG — max 10MB each</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3" id="imagePreviewGrid"></div>
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label fw-bold">Portfolio / Work URL</label>
                <input type="url"
                       name="portfolio_url"
                       class="form-control @error('portfolio_url') is-invalid @enderror"
                       value="{{ old('portfolio_url', $service->portfolio_url) }}"
                       placeholder="https://behance.net/yourwork">
                @error('portfolio_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Link to your Behance, Dribbble, Google Drive, or any portfolio site.</small>
            </div>

        </div>

        <div class="col-lg-4">

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Publish</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="feather-info me-2"></i>
                        Your service will be reviewed before going live.
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i> Save Changes
                        </button>
                        <a href="{{ route('seller.services.index') }}"
                           class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status Info</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Current Status</span>
                        <span class="badge orderer-badge badge-{{ $service->status }}">
                            {{ ucfirst($service->status) }}
                        </span>
                    </div>
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

        </div>
    </div>

</form>

@push('scripts')
<script>
// Pricing type toggle
const pricingType = document.getElementById('pricingType');
const priceField = document.getElementById('priceField');

function togglePriceField() {
    if (pricingType.value === 'negotiable') {
        priceField.style.display = 'none';
    } else {
        priceField.style.display = 'block';
    }
}

pricingType.addEventListener('change', togglePriceField);
togglePriceField();

// Image preview for new images
document.getElementById('imageInput').addEventListener('change', function() {
    var grid = document.getElementById('imagePreviewGrid');
    var currentCount = {{ count($service->portfolio_images ?? []) }};
    
    if (this.files.length + currentCount > 10) { 
        alert('Maximum 10 images allowed. You already have ' + currentCount + ' images.'); 
        this.value = ''; 
        return; 
    }
    
    grid.innerHTML = '';
    Array.from(this.files).forEach(function(file, index) {
        var reader = new FileReader();
        reader.onload = function(e) {
            grid.innerHTML += '<div class="position-relative" style="width:100px;height:100px;">' +
                '<img src="' + e.target.result + '" style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #eee;" alt="">' +
                '<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0" style="width:22px;height:22px;font-size:12px;margin:-8px -8px 0 0;" onclick="this.parentElement.remove()">' +
                '<i class="feather-x"></i></button>' +
                '</div>';
        };
        reader.readAsDataURL(file);
    });
});

// Mark image for removal
function markForRemoval(publicId, buttonElement) {
    if (confirm('Remove this image?')) {
        var container = buttonElement.closest('.position-relative');
        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';
        
        var removeInput = document.getElementById('removeImages');
        var currentValue = removeInput.value;
        if (currentValue) {
            removeInput.value = currentValue + ',' + publicId;
        } else {
            removeInput.value = publicId;
        }
        
        container.style.display = 'none';
    }
}
</script>
@endpush

@endsection