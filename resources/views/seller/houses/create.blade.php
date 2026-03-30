@extends('layouts.seller')
@section('title', 'Add Property')
@section('page_title', 'List a Property')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.houses.index') }}">Properties</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')

<form action="{{ route('seller.houses.store') }}"
      method="POST"
      enctype="multipart/form-data"
      id="houseForm">
    @csrf

    <div class="row">

        {{-- Left --}}
        <div class="col-lg-8">

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Property Details</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Title <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}"
                               placeholder="e.g. Spacious 3-Bedroom Apartment in Lekki"
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
                                  placeholder="Describe the property — amenities, nearby landmarks, condition..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Property Type <span class="text-danger">*</span>
                            </label>
                            <select name="property_type" class="form-select" required>
                                <option value="">Select type</option>
                                @foreach(['apartment','house','land','commercial','shortlet','other'] as $type)
                                <option value="{{ $type }}" {{ old('property_type') === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Listing Type <span class="text-danger">*</span>
                            </label>
                            <select name="listing_type" class="form-select" required>
                                <option value="sale"     {{ old('listing_type','sale') === 'sale'     ? 'selected' : '' }}>For Sale</option>
                                <option value="rent"     {{ old('listing_type') === 'rent'     ? 'selected' : '' }}>For Rent</option>
                                <option value="shortlet" {{ old('listing_type') === 'shortlet' ? 'selected' : '' }}>Shortlet</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Price (NGN) - (Yearly) <small> This will be automatically converted based on the buyer’s current exchange rate in their region at the time of viewing, adding to cart, wishlist, or checkout.</small><span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">₦</span>
                            <input type="number"
                                   name="price"
                                   class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price') }}"
                                   step="0.01"
                                   min="0.01"
                                   placeholder="0.00"
                                   required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Bedrooms</label>
                            <input type="number"
                                   name="bedrooms"
                                   class="form-control"
                                   value="{{ old('bedrooms') }}"
                                   min="0"
                                   placeholder="0">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Bathrooms</label>
                            <input type="number"
                                   name="bathrooms"
                                   class="form-control"
                                   value="{{ old('bathrooms') }}"
                                   min="0"
                                   placeholder="0">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Toilets</label>
                            <input type="number"
                                   name="toilets"
                                   class="form-control"
                                   value="{{ old('toilets') }}"
                                   min="0"
                                   placeholder="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Size (sqm)</label>
                            <input type="number"
                                   name="size_sqm"
                                   class="form-control"
                                   value="{{ old('size_sqm') }}"
                                   step="0.01"
                                   min="0"
                                   placeholder="Floor area">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Video Tour URL</label>
                            <input type="url"
                                   name="video_tour_url"
                                   class="form-control"
                                   value="{{ old('video_tour_url') }}"
                                   placeholder="YouTube or Drive link">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Features</label>
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            @foreach(['Parking','Swimming Pool','Generator','24hr Light','Security','Gym','Elevator','Water Supply','Balcony','CCTV'] as $feature)
                            <label class="d-flex align-items-center gap-2 fs-13 fw-normal" style="cursor:pointer;">
                                <input type="checkbox"
                                       name="features[]"
                                       value="{{ $feature }}"
                                       {{ in_array($feature, old('features', [])) ? 'checked' : '' }}>
                                {{ $feature }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

            {{-- Location --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Location</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Full Address</label>
                        <input type="text"
                               name="address"
                               class="form-control"
                               value="{{ old('address') }}"
                               placeholder="Street address">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">City</label>
                            <input type="text"
                                   name="city"
                                   class="form-control"
                                   value="{{ old('city') }}">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">
                                State <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="location"
                                   class="form-control @error('location') is-invalid @enderror"
                                   value="{{ old('location') }}"
                                   required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-bold">Country</label>
                            <input type="text"
                                   name="country"
                                   class="form-control"
                                   value="{{ old('country', 'Nigeria') }}">
                        </div>
                    </div>

                </div>
            </div>

            {{-- Images --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        Property Images <span class="text-danger">*</span>
                    </h5>
                    <small class="text-muted">Min 1, Max 10. First image = main image.</small>
                </div>
                <div class="card-body">
                    <div class="border border-dashed rounded p-4 text-center position-relative"
                         id="imageUploadBox"
                         style="cursor:pointer;">
                        <input type="file"
                               name="images[]"
                               id="houseImageInput"
                               accept="image/jpg,image/jpeg,image/png"
                               multiple
                               required
                               style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                        <i class="feather-upload-cloud mb-2 d-block text-muted" style="font-size:32px;"></i>
                        <p class="text-muted mb-1">Click or drag images here</p>
                        <small class="text-muted">JPG, PNG — max 4MB each</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3" id="houseImagePreview"></div>
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
                        Your property listing will be reviewed before going live. Usually under 24 hours.
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-send me-2"></i> Submit for Review
                        </button>
                        <a href="{{ route('seller.houses.index') }}"
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
                            Upload bright, high-quality photos
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Include exact address and landmarks
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            List all available features
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Add a video tour to boost interest
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
document.getElementById('houseImageInput').addEventListener('change', function() {
    var grid = document.getElementById('houseImagePreview');
    grid.innerHTML = '';
    if (this.files.length > 10) { alert('Maximum 10 images allowed.'); this.value = ''; return; }
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