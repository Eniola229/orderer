@extends('layouts.seller')
@section('title', 'Add Service')
@section('page_title', 'Add New Service')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.services.index') }}">Services</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')

<form action="{{ route('seller.services.store') }}"
      method="POST"
      enctype="multipart/form-data"
      id="serviceForm">
    @csrf

    <div class="row">

        {{-- Left --}}
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
                               value="{{ old('title') }}"
                               placeholder="e.g. Professional Logo Design"
                               required>
                        @error('title')
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
                                  placeholder="Describe your service in detail — what you offer, what's included, requirements..."
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
                                    class="form-select @error('category_id') is-invalid @enderror"
                                    required>
                                <option value="">Select category</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Pricing Type <span class="text-danger">*</span>
                            </label>
                            <select name="pricing_type"
                                    id="pricingTypeSelect"
                                    class="form-select"
                                    required>
                                <option value="fixed"      {{ old('pricing_type','fixed') === 'fixed'      ? 'selected' : '' }}>Fixed Price</option>
                                <option value="hourly"     {{ old('pricing_type') === 'hourly'     ? 'selected' : '' }}>Hourly Rate</option>
                                <option value="negotiable" {{ old('pricing_type') === 'negotiable' ? 'selected' : '' }}>Negotiable</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4" id="priceField">
                            <label class="form-label fw-bold">Price (USD) - <small> This will be automatically converted based on the buyer’s current exchange rate in their region at the time of viewing, adding to cart, wishlist, or checkout.</small></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       name="price"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price') }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="Leave blank if negotiable">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Delivery Time</label>
                            <input type="text"
                                   name="delivery_time"
                                   class="form-control"
                                   value="{{ old('delivery_time') }}"
                                   placeholder="e.g. 2-3 business days">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Location</label>
                        <input type="text"
                               name="location"
                               class="form-control"
                               value="{{ old('location') }}"
                               placeholder="City, Country">
                    </div>

                </div>
            </div>

            {{-- Portfolio Images --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Portfolio Images</h5>
                    <small class="text-muted">Optional — Max 5 images</small>
                </div>
                <div class="card-body">
                    <div class="border border-dashed rounded p-4 text-center position-relative"
                         id="portfolioUploadBox"
                         style="cursor:pointer;">
                        <input type="file"
                               name="portfolio[]"
                               id="portfolioInput"
                               accept="image/jpg,image/jpeg,image/png"
                               multiple
                               style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                        <i class="feather-upload-cloud mb-2 d-block text-muted" style="font-size:32px;"></i>
                        <p class="text-muted mb-1">Upload samples of your work</p>
                        <small class="text-muted">JPG, PNG — max 10MB each</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3" id="portfolioPreviewGrid"></div>
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
                        Your service will be reviewed before going live. Usually under 24 hours.
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-send me-2"></i> Submit for Review
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
                    <h5 class="card-title mb-0">Listing Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled fs-13 text-muted">
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Write a clear, specific service title
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Describe exactly what's included
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Add portfolio images to build trust
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Set a realistic delivery timeframe
                        </li>
                        <li class="mb-0">
                            <i class="feather-check-circle text-success me-2"></i>
                            Use competitive USD pricing
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</form>

@push('scripts')
<script>
document.getElementById('pricingTypeSelect').addEventListener('change', function() {
    var priceField = document.getElementById('priceField');
    priceField.style.opacity = this.value === 'negotiable' ? '0.4' : '1';
    priceField.querySelector('input').disabled = this.value === 'negotiable';
});

document.getElementById('portfolioInput').addEventListener('change', function() {
    var grid = document.getElementById('portfolioPreviewGrid');
    grid.innerHTML = '';
    if (this.files.length > 5) { alert('Maximum 5 images allowed.'); this.value = ''; return; }
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
</script>
@endpush

@endsection