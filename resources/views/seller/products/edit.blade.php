@extends('layouts.seller')
@section('title', 'Edit Product')
@section('page_title', 'Edit Product')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

@if($product->status === 'approved')
<div class="alert alert-warning mb-4">
    <i class="feather-alert-triangle me-2"></i>
    <strong>Note:</strong> This product is approved. Any changes you make will require re-approval by our team before going live.
</div>
@endif

<form action="{{ route('seller.products.update', $product->id) }}"
      method="POST"
      enctype="multipart/form-data"
      id="productForm">
    @csrf
    @method('PUT') 

    <div class="row">
 
        {{-- Left --}}
        <div class="col-lg-8">

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Information</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Product Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $product->name) }}"
                               placeholder="Enter a clear, descriptive product name"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Description <span class="text-danger">*</span>
                        </label>
                        <textarea name="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="6"
                                  placeholder="Describe your product in detail"
                                  required>{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select name="category_id"
                                    id="categorySelect"
                                    class="form-select"
                                    required>
                                <option value="">Select category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                        {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}
                                        data-subs='@json($cat->subcategories)'>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Subcategory</label>
                            <select name="subcategory_id"
                                    id="subcategorySelect"
                                    class="form-select">
                                <option value="">Select subcategory</option>
                                @if($product->subcategory_id)
                                    @foreach($product->category->subcategories ?? [] as $sub)
                                    <option value="{{ $sub->id }}" {{ $product->subcategory_id == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->name }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">
                                Price (NGN) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number"
                                       name="price"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price', $product->price) }}"
                                       step="0.01"
                                       min="0.01"
                                       placeholder="0.00"
                                       required>
                            </div>
                            <small class="text-muted">This will be automatically converted based on the buyer's current exchange rate</small>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Sale Price (NGN)</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number"
                                       name="sale_price"
                                       class="form-control"
                                       value="{{ old('sale_price', $product->sale_price) }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="Optional">
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">
                                Stock <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   name="stock"
                                   class="form-control"
                                   value="{{ old('stock', $product->stock) }}"
                                   min="0"
                                   required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Condition</label>
                            <select name="condition" class="form-select">
                                <option value="new"         {{ old('condition', $product->condition) == 'new'         ? 'selected' : '' }}>New</option>
                                <option value="used"        {{ old('condition', $product->condition) == 'used'        ? 'selected' : '' }}>Used</option>
                                <option value="refurbished" {{ old('condition', $product->condition) == 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Weight (kg)</label>
                            <input type="number"
                                   name="weight_kg"
                                   class="form-control"
                                   value="{{ old('weight_kg', $product->weight_kg) }}"
                                   step="0.01"
                                   placeholder="For shipping">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">SKU</label>
                            <input type="text"
                                   name="sku"
                                   class="form-control"
                                   value="{{ old('sku', $product->sku) }}"
                                   placeholder="Optional">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Location</label>
                        <input type="text"
                               name="location"
                               class="form-control"
                               value="{{ old('location', $product->location) }}"
                               placeholder="City, State (e.g. Lagos, Nigeria)">
                    </div>

                </div>
            </div>

            @include('seller.products._options', ['existingOptions' => $product->options])

            {{-- Images --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        Product Images <span class="text-danger">*</span>
                    </h5>
                    <small class="text-muted">Min 1, Max 8. First image = main image.</small>
                </div>
                <div class="card-body">
                    
                    {{-- Current Images --}}
                    @if($product->images->count())
                    <div class="mb-4">
                        <label class="form-label fw-bold">Current Images</label>
                        <div class="d-flex flex-wrap gap-2" id="currentImages">
                            @foreach($product->images as $image)
                            <div class="position-relative" style="width:100px;height:100px;" id="image-{{ $image->id }}">
                                <img src="{{ $image->image_url }}" 
                                     style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #eee;" 
                                     alt="">
                                @if($loop->first)
                                <span style="position:absolute;bottom:0;left:0;right:0;background:rgba(46,204,113,.85);color:#fff;font-size:10px;text-align:center;padding:3px;border-radius:0 0 8px 8px;">
                                    Main
                                </span>
                                @endif
                                <button type="button" 
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0"
                                        style="width:22px;height:22px;font-size:12px;margin:-8px -8px 0 0;"
                                        onclick="removeImage('{{ $image->id }}')">
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
                         id="imageUploadBox"
                         style="cursor:pointer;">
                        <input type="file"
                               name="new_images[]"
                               id="imageInput"
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               multiple
                               style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                        <i class="feather-upload-cloud mb-2 d-block text-muted" style="font-size:32px;"></i>
                        <p class="text-muted mb-1">Add new images</p>
                        <small class="text-muted">JPG, PNG, WebP — max 4MB each</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3" id="imagePreviewGrid"></div>
                </div>
            </div>

            {{-- Video --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Product Video</h5>
                    <small class="text-muted">Optional — Max 50MB</small>
                </div>
                <div class="card-body">
                    
                    {{-- Current Video --}}
                    @if($product->videos->count())
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="feather-video me-2 text-primary"></i>
                                <strong>Current Video</strong>
                                <br>
                                <small class="text-muted">{{ $product->videos->first()->video_url }}</small>
                            </div>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="removeVideo()">
                                <i class="feather-trash-2"></i> Remove
                            </button>
                        </div>
                        <input type="hidden" name="remove_video" id="removeVideo" value="0">
                    </div>
                    @endif

                    {{-- Upload New Video --}}
                    <div class="border border-dashed rounded p-4 text-center position-relative"
                         style="cursor:pointer;">
                        <input type="file"
                               name="new_video"
                               id="videoInput"
                               accept="video/mp4,video/mov,video/avi"
                               style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                        <i class="feather-video mb-2 d-block text-muted" style="font-size:32px;"></i>
                        <p class="text-muted mb-1">Upload new video</p>
                        <small class="text-muted">MP4, MOV, AVI — max 50MB</small>
                    </div>
                    <p id="videoFileName" class="text-success fs-13 mt-2 mb-0" style="display:none;"></p>
                </div>
            </div>

        </div>

        {{-- Right --}}
        <div class="col-lg-4">

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Publish</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3">
                        <i class="feather-info me-2"></i>
                        Your product will be reviewed before going live. Usually under 24 hours.
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i> Save & Submit for Review
                        </button>
                        <a href="{{ route('seller.products.index') }}"
                           class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Listing Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled fs-13 text-muted">
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Use clear, white background images
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Include dimensions and weight
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Write at least 100 characters in description
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Add a video to boost conversions
                        </li>
                        <li class="mb-0">
                            <i class="feather-check-circle text-success me-2"></i>
                            Set competitive NGN pricing
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</form>

@push('scripts')
<script>
// Category change handler
document.getElementById('categorySelect').addEventListener('change', function() {
    var subs = JSON.parse(this.options[this.selectedIndex].dataset.subs || '[]');
    var subSelect = document.getElementById('subcategorySelect');
    subSelect.innerHTML = '<option value="">Select subcategory</option>';
    subs.forEach(function(sub) {
        subSelect.innerHTML += '<option value="' + sub.id + '">' + sub.name + '</option>';
    });
});

// Image preview for new images
document.getElementById('imageInput').addEventListener('change', function() {
    var grid = document.getElementById('imagePreviewGrid');
    if (this.files.length + {{ $product->images->count() }} > 8) { 
        alert('Maximum 8 images allowed. You already have {{ $product->images->count() }} images.'); 
        this.value = ''; 
        return; 
    }
    grid.innerHTML = '';
    Array.from(this.files).forEach(function(file, index) {
        var reader = new FileReader();
        reader.onload = function(e) {
            grid.innerHTML += '<div class="position-relative" style="width:80px;height:80px;">' +
                '<img src="' + e.target.result + '" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #eee;" alt="">' +
                '<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0" style="width:20px;height:20px;font-size:10px;margin:-5px -5px 0 0;" onclick="this.parentElement.remove()">' +
                '<i class="feather-x"></i></button>' +
                '</div>';
        };
        reader.readAsDataURL(file);
    });
});

// Remove existing image
function removeImage(imageId) {
    if (confirm('Remove this image?')) {
        var container = document.getElementById('image-' + imageId);
        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';
        
        var removeInput = document.getElementById('removeImages');
        var currentValue = removeInput.value;
        if (currentValue) {
            removeInput.value = currentValue + ',' + imageId;
        } else {
            removeInput.value = imageId;
        }
        
        // Optionally hide the image container
        container.style.display = 'none';
    }
}

// Remove video
function removeVideo() {
    if (confirm('Remove the current video?')) {
        document.getElementById('removeVideo').value = '1';
        document.querySelector('.bg-light').style.display = 'none';
    }
}

// Video preview
document.getElementById('videoInput').addEventListener('change', function() {
    var label = document.getElementById('videoFileName');
    if (this.files[0]) {
        label.textContent = '✓ New video: ' + this.files[0].name;
        label.style.display = 'block';
    }
});

// Initialize subcategory on page load
document.addEventListener('DOMContentLoaded', function() {
    var categorySelect = document.getElementById('categorySelect');
    if (categorySelect.value) {
        var event = new Event('change');
        categorySelect.dispatchEvent(event);
    }
});
</script>
@endpush

@endsection