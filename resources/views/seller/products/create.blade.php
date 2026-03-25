@extends('layouts.seller')
@section('title', 'Add Product')
@section('page_title', 'Add New Product')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')

<form action="{{ route('seller.products.store') }}"
      method="POST"
      enctype="multipart/form-data"
      id="productForm">
    @csrf

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
                               value="{{ old('name') }}"
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
                                  required>{{ old('description') }}</textarea>
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
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}
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
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">
                                Price (USD) — <small> This will be automatically converted based on the buyer’s current exchange rate in their region at the time of viewing, adding to cart, wishlist, or checkout.</small><span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       name="price"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price') }}"
                                       step="0.01"
                                       min="0.01"
                                       placeholder="0.00"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Sale Price (USD) - <small> This will be automatically converted based on the buyer’s current exchange rate in their region at the time of viewing, adding to cart, wishlist, or checkout.</small></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       name="sale_price"
                                       class="form-control"
                                       value="{{ old('sale_price') }}"
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
                                   value="{{ old('stock', 1) }}"
                                   min="0"
                                   required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Condition</label>
                            <select name="condition" class="form-select">
                                <option value="new"         {{ old('condition','new') === 'new'         ? 'selected' : '' }}>New</option>
                                <option value="used"        {{ old('condition') === 'used'        ? 'selected' : '' }}>Used</option>
                                <option value="refurbished" {{ old('condition') === 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Weight (kg)</label>
                            <input type="number"
                                   name="weight_kg"
                                   class="form-control"
                                   value="{{ old('weight_kg') }}"
                                   step="0.01"
                                   placeholder="For shipping">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">SKU</label>
                            <input type="text"
                                   name="sku"
                                   class="form-control"
                                   value="{{ old('sku') }}"
                                   placeholder="Optional">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Location</label>
                        <input type="text"
                               name="location"
                               class="form-control"
                               value="{{ old('location') }}"
                               placeholder="City, State (e.g. Lagos, Nigeria)">
                    </div>

                </div>
            </div>

            {{-- Images --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        Product Images <span class="text-danger">*</span>
                    </h5>
                    <small class="text-muted">Min 1, Max 8. First image = main image.</small>
                </div>
                <div class="card-body">
                    <div class="border border-dashed rounded p-4 text-center position-relative"
                         id="imageUploadBox"
                         style="cursor:pointer;">
                        <input type="file"
                               name="images[]"
                               id="imageInput"
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               multiple
                               required
                               style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                        <i class="feather-upload-cloud mb-2 d-block text-muted" style="font-size:32px;"></i>
                        <p class="text-muted mb-1">Click or drag images here</p>
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
                    <div class="border border-dashed rounded p-4 text-center position-relative"
                         style="cursor:pointer;">
                        <input type="file"
                               name="video"
                               id="videoInput"
                               accept="video/mp4,video/mov,video/avi"
                               style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                        <i class="feather-video mb-2 d-block text-muted" style="font-size:32px;"></i>
                        <p class="text-muted mb-1">Click to upload a product video</p>
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
                            <i class="feather-send me-2"></i> Submit for Review
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
                            Set competitive USD pricing
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</form>

@push('scripts')
<script>
document.getElementById('categorySelect').addEventListener('change', function() {
    var subs = JSON.parse(this.options[this.selectedIndex].dataset.subs || '[]');
    var subSelect = document.getElementById('subcategorySelect');
    subSelect.innerHTML = '<option value="">Select subcategory</option>';
    subs.forEach(function(sub) {
        subSelect.innerHTML += '<option value="' + sub.id + '">' + sub.name + '</option>';
    });
});

document.getElementById('imageInput').addEventListener('change', function() {
    var grid = document.getElementById('imagePreviewGrid');
    grid.innerHTML = '';
    if (this.files.length > 8) { alert('Maximum 8 images allowed.'); this.value = ''; return; }
    Array.from(this.files).forEach(function(file, index) {
        var reader = new FileReader();
        reader.onload = function(e) {
            grid.innerHTML += '<div class="position-relative" style="width:80px;height:80px;">' +
                '<img src="' + e.target.result + '" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #eee;" alt="">' +
                (index === 0 ? '<span style="position:absolute;bottom:0;left:0;right:0;background:rgba(46,204,113,.85);color:#fff;font-size:9px;text-align:center;padding:2px;border-radius:0 0 8px 8px;">Main</span>' : '') +
                '</div>';
        };
        reader.readAsDataURL(file);
    });
});

document.getElementById('videoInput').addEventListener('change', function() {
    var label = document.getElementById('videoFileName');
    if (this.files[0]) {
        label.textContent = '✓ ' + this.files[0].name;
        label.style.display = 'block';
    }
});
</script>
@endpush

@endsection