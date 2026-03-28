@extends('layouts.seller')
@section('title', 'Create Ad')
@section('page_title', 'Create Promotion')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.ads.index') }}">Promotions</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')

<style>
    .media-preview {
        position: relative;
        display: inline-block;
        margin-top: 15px;
    }
    .media-preview img, .media-preview video {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    .remove-media {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
    }
    .remove-media:hover {
        background: #c82333;
    }
</style>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('seller.ads.store') }}"
              method="POST"
              enctype="multipart/form-data"
              id="adForm">
            @csrf

            {{-- What are you promoting --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">What are you promoting?</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Promotable Type <span class="text-danger">*</span>
                        </label>
                        <select name="promotable_type"
                                id="promotableType"
                                class="form-select"
                                required>
                            <option value="">Select what to promote</option>
                            <option value="product"  {{ old('promotable_type') === 'product'  ? 'selected' : '' }}>A Product</option>
                            <option value="service"  {{ old('promotable_type') === 'service'  ? 'selected' : '' }}>A Service</option>
                            <option value="house"    {{ old('promotable_type') === 'house'    ? 'selected' : '' }}>A Property</option>
                            <option value="brand"    {{ old('promotable_type') === 'brand'    ? 'selected' : '' }}>My Brand/Store</option>
                        </select>
                    </div>

                    {{-- Dynamic listing selector --}}
                    <div id="productSection" style="display:none;" class="mb-4">
                        <label class="form-label fw-bold">Select Product</label>
                        <select name="promotable_id" id="productSelect" class="form-select">
                            <option value="">Select a product</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('promotable_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} — ${{ number_format($p->price, 2) }}
                            </option>
                            @endforeach
                        </select>
                        @if($products->isEmpty())
                            <small class="text-danger">No approved products found. Get products approved first.</small>
                        @endif
                    </div>

                    <div id="serviceSection" style="display:none;" class="mb-4">
                        <label class="form-label fw-bold">Select Service</label>
                        <select name="promotable_id" id="serviceSelect" class="form-select">
                            <option value="">Select a service</option>
                            @foreach($services as $s)
                            <option value="{{ $s->id }}">{{ $s->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="houseSection" style="display:none;" class="mb-4">
                        <label class="form-label fw-bold">Select Property</label>
                        <select name="promotable_id" id="houseSelect" class="form-select">
                            <option value="">Select a property</option>
                            @foreach($houses as $h)
                            <option value="{{ $h->id }}">{{ $h->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="brandSection" style="display:none;" class="mb-4">
                        <input type="hidden" name="promotable_id" value="{{ auth('seller')->id() }}">
                        <div class="alert alert-info">
                            <i class="feather-info me-2"></i>
                            Promoting your brand will show your store banner to buyers.
                        </div>
                    </div>

                </div>
            </div>

            {{-- Ad type and slot --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ad Type & Placement</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Ad Category <span class="text-danger">*</span>
                        </label>
                        <select name="ad_category_id"
                                id="adCategorySelect"
                                class="form-select"
                                required>
                            <option value="">Select ad type</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                    data-type="{{ $cat->type }}"
                                    {{ old('ad_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} —
                                @if($cat->type === 'banner_image') Image Banner
                                @elseif($cat->type === 'banner_video') Video Banner (Premium)
                                @elseif($cat->type === 'top_listing') Top Listing
                                @elseif($cat->type === 'cpc') Pay Per Order (CPC)
                                @endif
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            CPC ads only charge when a buyer orders. Other types charge daily.
                        </small>
                    </div>

                    {{-- Banner slot (only for banner types) --}}
                    <div id="slotSection" style="display:none;" class="mb-4">
                        <label class="form-label fw-bold">Banner Slot</label>
                        <div class="row g-3" id="slotCards">
                            @foreach($slots as $slot)
                            <div class="col-md-6">
                                <label class="d-block border rounded p-3 cursor-pointer slot-card"
                                       for="slot_{{ $slot->id }}"
                                       style="cursor:pointer;transition:border-color .2s;">
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="radio"
                                               name="ad_banner_slot_id"
                                               id="slot_{{ $slot->id }}"
                                               value="{{ $slot->id }}"
                                               class="form-check-input mt-0 slot-radio"
                                               data-price="{{ $slot->price_per_day }}">
                                        <div>
                                            <p class="mb-0 fw-semibold fs-14">{{ $slot->name }}</p>
                                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $slot->location)) }}</small>
                                            <p class="mb-0 text-primary fw-bold fs-13 mt-1">
                                                ${{ number_format($slot->price_per_day, 2) }}/day
                                            </p>
                                            @if($slot->dimensions)
                                                <small class="text-muted">{{ $slot->dimensions }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

            {{-- Ad content --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ad Content</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Ad Title <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               value="{{ old('title') }}"
                               placeholder="e.g. Summer Sale — Up to 50% off!"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Ad Media</label>
                        <div class="border border-dashed rounded p-4 text-center position-relative"
                             style="cursor:pointer;">
                            <input type="file"
                                   name="media"
                                   id="adMedia"
                                   accept="image/jpg,image/jpeg,image/png,video/mp4,video/mov"
                                   style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;">
                            <i class="feather-image mb-2 d-block text-muted" style="font-size:28px;"></i>
                            <p class="text-muted mb-1">Upload image or video</p>
                            <small class="text-muted">Image: JPG, PNG | Video: MP4, MOV — max 50MB</small>
                        </div>
                        <p id="adMediaName" class="text-success fs-13 mt-2 mb-0" style="display:none;"></p>

                        
                        {{-- Media Preview --}}
                        <canvas id="thumbnailCanvas" style="display:none;"></canvas>

                    {{-- Media Preview --}}
                    <div id="mediaPreview" class="media-preview" style="display:none;">
                            <button type="button" class="remove-media" onclick="removeMedia()">×</button>
                            <img id="imagePreview" src="" alt="Preview" style="display:none;">
                            <video id="videoPreview" controls preload="metadata" style="display:none;"></video>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Click URL (optional)</label>
                        <input type="url"
                               name="click_url"
                               class="form-control"
                               value="{{ old('click_url') }}"
                               placeholder="https://...">
                        <small class="text-muted">
                            Where to send buyers when they click your ad. Leave blank to use your product page.
                        </small>
                    </div>

                </div>
            </div>

            {{-- Budget & Schedule --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Budget & Schedule</h5>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Start Date <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="start_date"
                                   id="startDate"
                                   class="form-control"
                                   value="{{ old('start_date', now()->format('Y-m-d')) }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                End Date <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="end_date"
                                   id="endDate"
                                   class="form-control"
                                   value="{{ old('end_date', now()->addDays(7)->format('Y-m-d')) }}"
                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                   required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Total Budget (USD) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number"
                                   name="budget"
                                   id="budgetInput"
                                   class="form-control"
                                   value="{{ old('budget') }}"
                                   step="0.01"
                                   min="1"
                                   placeholder="0.00"
                                   required>
                        </div>
                        <div id="budgetEstimate"
                             class="mt-2 p-3 bg-light rounded fs-13 text-muted"
                             style="display:none;">
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>
                                <i class="feather-info me-2"></i>
                                Your ads balance:
                                <strong>${{ number_format($seller->ads_balance, 2) }}</strong>
                            </span>
                            <a href="{{ route('seller.wallet.index') }}"
                               class="btn btn-sm btn-outline-primary">
                                Top Up
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="feather-send me-2"></i> Submit Ad for Review
                </button>
                <a href="{{ route('seller.ads.index') }}"
                   class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>

        </form>
    </div>

    {{-- Info sidebar --}}
    <div class="col-lg-4">

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Ad Types Explained</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <div class="avatar-text avatar-sm rounded"
                             style="background:#D5F5E3;color:#2ECC71;flex-shrink:0;">
                            <i class="feather-image" style="font-size:14px;"></i>
                        </div>
                        <div>
                            <p class="mb-1 fw-semibold fs-13">Banner Image Ad</p>
                            <p class="text-muted fs-12 mb-0">
                                Your image appears in the slideshow banners. Charged daily.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <div class="avatar-text avatar-sm rounded"
                             style="background:#EBF5FB;color:#2980B9;flex-shrink:0;">
                            <i class="feather-video" style="font-size:14px;"></i>
                        </div>
                        <div>
                            <p class="mb-1 fw-semibold fs-13">Banner Video Ad</p>
                            <p class="text-muted fs-12 mb-0">
                                Video plays in banner slideshows. Premium pricing. Highest visibility.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <div class="avatar-text avatar-sm rounded"
                             style="background:#FEF9E7;color:#F39C12;flex-shrink:0;">
                            <i class="feather-trending-up" style="font-size:14px;"></i>
                        </div>
                        <div>
                            <p class="mb-1 fw-semibold fs-13">Top Listing</p>
                            <p class="text-muted fs-12 mb-0">
                                Your product appears at the top of category and search results. Charged daily.
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-start gap-2">
                        <div class="avatar-text avatar-sm rounded"
                             style="background:#FADBD8;color:#E74C3C;flex-shrink:0;">
                            <i class="feather-shopping-cart" style="font-size:14px;"></i>
                        </div>
                        <div>
                            <p class="mb-1 fw-semibold fs-13">Pay Per Order (CPC)</p>
                            <p class="text-muted fs-12 mb-0">
                                You are only charged when a buyer actually places an order via your ad. Higher cost per conversion but zero waste.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Banner Slots</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled fs-13 text-muted">
                    <li class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="feather-home me-2 text-primary"></i> Homepage Hero</span>
                            <strong class="text-primary">Highest Traffic</strong>
                        </div>
                        <small>Main banner on the home page. Maximum visibility.</small>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="feather-grid me-2 text-success"></i> Category Page</span>
                            <strong class="text-success">Targeted</strong>
                        </div>
                        <small>Shows on category browse pages. Reaches buyers already interested.</small>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="feather-package me-2 text-warning"></i> Product Sidebar</span>
                            <strong class="text-warning">High Intent</strong>
                        </div>
                        <small>Appears next to product pages. Buyers are in buying mode.</small>
                    </li>
                    <li>
                        <div class="d-flex justify-content-between">
                            <span><i class="feather-search me-2 text-danger"></i> Search Results</span>
                            <strong class="text-danger">Intent-Based</strong>
                        </div>
                        <small>Shows when buyers search. Highest purchase intent.</small>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// Show/hide listing selector based on type
document.getElementById('promotableType').addEventListener('change', function() {
    ['productSection','serviceSection','houseSection','brandSection'].forEach(function(id) {
        document.getElementById(id).style.display = 'none';
    });

    const map = {
        product: 'productSection',
        service: 'serviceSection',
        house:   'houseSection',
        brand:   'brandSection',
    };

    if (map[this.value]) {
        document.getElementById(map[this.value]).style.display = 'block';
    }
});

// Show/hide slot selector based on ad type
document.getElementById('adCategorySelect').addEventListener('change', function() {
    const type = this.options[this.selectedIndex].dataset.type;
    const slotSection = document.getElementById('slotSection');
    slotSection.style.display = (type === 'banner_image' || type === 'banner_video') ? 'block' : 'none';
    updateBudgetEstimate();
});

// Slot card visual selection
document.querySelectorAll('.slot-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.slot-card').forEach(c => {
            c.style.borderColor = '#dee2e6';
            c.style.background = '#fff';
        });
        this.closest('.slot-card').style.borderColor = '#2ECC71';
        this.closest('.slot-card').style.background = '#f0faf5';
        updateBudgetEstimate();
    });
});

// Media preview
const mediaInput     = document.getElementById('adMedia');
const mediaNameSpan  = document.getElementById('adMediaName');
const previewContainer = document.getElementById('mediaPreview');
const imagePreview   = document.getElementById('imagePreview');
const videoPreview   = document.getElementById('videoPreview');
const thumbCanvas    = document.getElementById('thumbnailCanvas');
const thumbCtx       = thumbCanvas.getContext('2d');

mediaInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) { removeMedia(); return; }

    mediaNameSpan.textContent = '✓ ' + file.name;
    mediaNameSpan.style.display = 'block';
    previewContainer.style.display = 'block';

    if (file.type.startsWith('image/')) {

        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
            videoPreview.style.display = 'none';
        };
        reader.readAsDataURL(file);

    } else if (file.type.startsWith('video/')) {

        // Show the real video player
        videoPreview.src = URL.createObjectURL(file);
        videoPreview.style.display = 'block';
        imagePreview.style.display = 'none';

        // Use a SEPARATE hidden video purely for thumbnail extraction
        const tmpVid = document.createElement('video');
        tmpVid.muted      = true;
        tmpVid.playsInline = true;
        tmpVid.autoplay   = false;

        const tmpUrl = URL.createObjectURL(file);
        tmpVid.src = tmpUrl;

        // Step 1 — wait for metadata so we know dimensions
        tmpVid.addEventListener('loadedmetadata', function () {
            thumbCanvas.width  = tmpVid.videoWidth;
            thumbCanvas.height = tmpVid.videoHeight;
            tmpVid.currentTime = 0; // seek to first frame
        }, { once: true });

        // Step 2 — after seek, draw frame → set as poster
        tmpVid.addEventListener('seeked', function () {
            thumbCtx.drawImage(tmpVid, 0, 0, thumbCanvas.width, thumbCanvas.height);
            videoPreview.poster = thumbCanvas.toDataURL('image/jpeg');
            URL.revokeObjectURL(tmpUrl);
        }, { once: true });

        tmpVid.addEventListener('error', function () {
            URL.revokeObjectURL(tmpUrl);
        }, { once: true });
    }
});

function removeMedia() {
    mediaInput.value = '';
    mediaNameSpan.style.display = 'none';
    previewContainer.style.display = 'none';
    imagePreview.style.display = 'none';
    videoPreview.style.display = 'none';
    videoPreview.poster = '';
    imagePreview.src = '';
    videoPreview.src = '';
}

function removeMedia() {                                               // ✅ fixed name
    mediaInput.value = '';
    mediaNameSpan.style.display = 'none';
    previewContainer.style.display = 'none';
    imagePreview.style.display = 'none';
    videoPreview.style.display = 'none';
    
    if (imagePreview.src) URL.revokeObjectURL(imagePreview.src);
    if (videoPreview.src) URL.revokeObjectURL(videoPreview.src);
    imagePreview.src = '';
    videoPreview.src = '';
}

// Budget estimate
function updateBudgetEstimate() {
    const start   = document.getElementById('startDate').value;
    const end     = document.getElementById('endDate').value;
    const slot    = document.querySelector('.slot-radio:checked');
    const pricePerDay = slot ? parseFloat(slot.dataset.price) : 5.00;

    if (start && end) {
        const days    = Math.ceil((new Date(end) - new Date(start)) / (1000 * 60 * 60 * 24)) + 1;
        const minCost = (pricePerDay * days).toFixed(2);
        const div     = document.getElementById('budgetEstimate');
        div.style.display = 'block';
        div.innerHTML = `
            <strong>${days} day(s)</strong> × $${pricePerDay.toFixed(2)}/day = 
            <strong>$${minCost} minimum budget</strong>
        `;
    }
}

document.getElementById('startDate').addEventListener('change', updateBudgetEstimate);
document.getElementById('endDate').addEventListener('change', updateBudgetEstimate);

// Pre-trigger if old values exist
@if(old('promotable_type'))
    document.getElementById('promotableType').value = '{{ old("promotable_type") }}';
    document.getElementById('promotableType').dispatchEvent(new Event('change'));
@endif

@if(old('ad_category_id'))
    document.getElementById('adCategorySelect').value = '{{ old("ad_category_id") }}';
    document.getElementById('adCategorySelect').dispatchEvent(new Event('change'));
@endif

@if(old('ad_banner_slot_id'))
    document.querySelector(`input[name="ad_banner_slot_id"][value="{{ old('ad_banner_slot_id') }}"]`).checked = true;
    document.querySelector(`input[name="ad_banner_slot_id"][value="{{ old('ad_banner_slot_id') }}"]`).dispatchEvent(new Event('change'));
@endif

// Check if there's a media preview from old data (if any)
@if(old('media'))
    // Handle old media preview if needed
@endif
</script>
@endpush

@endsection