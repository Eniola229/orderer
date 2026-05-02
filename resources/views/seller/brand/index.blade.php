@extends('layouts.seller')
@section('title', 'My Brand')
@section('page_title', 'My Brand')
@section('breadcrumb')
    <li class="breadcrumb-item active">Brand</li>
@endsection

@section('content')

@if(!$brand)
 
{{-- Create brand --}}
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Create Your Brand</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <i class="feather-info me-2"></i>
                    A brand page lets buyers browse all your products, services, properties in one place and leave reviews.
                </div>
                <form action="{{ route('seller.brand.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $seller->business_name) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="Tell buyers about your brand...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Website</label>
                        <input type="url" name="website" class="form-control"
                               value="{{ old('website') }}" placeholder="https://...">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Logo</label>
                            <input type="file" name="logo" class="form-control"
                                   accept="image/jpg,image/jpeg,image/png">
                            <small class="text-muted">JPG, PNG — max 2MB</small>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Banner Image</label>
                            <input type="file" name="banner" class="form-control"
                                   accept="image/jpg,image/jpeg,image/png">
                            <small class="text-muted">JPG, PNG — max 4MB</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="feather-plus me-2"></i> Create Brand
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@else

{{-- Brand exists --}}
<div class="row">
    <div class="col-lg-8">

        {{-- Banner preview --}}
        @if($brand->banner)
        <div class="card mb-3 overflow-hidden">
            <img src="{{ $brand->banner }}"
                 style="width:100%;height:200px;object-fit:cover;" alt="Brand Banner">
        </div>
        @endif

        {{-- Edit brand --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Brand</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('seller.brand.update', $brand->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $brand->name) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $brand->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Website</label>
                        <input type="url" name="website" class="form-control"
                               value="{{ old('website', $brand->website) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Logo</label>
                            @if($brand->logo)
                                <div class="mb-2">
                                    <img src="{{ $brand->logo }}"
                                         style="height:60px;border-radius:8px;object-fit:cover;" alt="">
                                </div>
                            @endif
                            <input type="file" name="logo" class="form-control"
                                   accept="image/jpg,image/jpeg,image/png">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Banner Image</label>
                            <input type="file" name="banner" class="form-control"
                                   accept="image/jpg,image/jpeg,image/png">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        {{-- Reviews --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Brand Reviews
                    <span class="badge bg-light text-dark ms-2">
                        {{ $brand->total_reviews }}
                    </span>
                </h5>
            </div>
            <div class="card-body p-0">
                @forelse($brand->reviews()->where('is_visible', true)->latest()->take(10)->get() as $review)
                <div class="d-flex gap-3 p-3 border-bottom">
                    <div class="avatar-text avatar-sm rounded bg-primary text-white flex-shrink-0">
                        {{ strtoupper(substr($review->user->first_name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <strong class="fs-13">
                                {{ $review->user->first_name ?? 'Buyer' }}
                            </strong>
                            <span class="text-warning fs-12">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $review->rating ? '★' : '☆' }}
                                @endfor
                            </span>
                        </div>
                        <p class="mb-0 text-muted fs-13">{{ $review->review ?? 'No comment.' }}</p>
                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="feather-star mb-2 d-block" style="font-size:28px;"></i>
                    No reviews yet.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                @if($brand->logo)
                    <img src="{{ $brand->logo }}"
                         style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:3px solid #2ECC71;"
                         class="mb-3" alt="">
                @else
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:100px;height:100px;border-radius:50%;background:#2ECC71;color:#fff;font-size:36px;font-weight:700;">
                        {{ strtoupper(substr($brand->name, 0, 1)) }}
                    </div>
                @endif
                <h5 class="fw-bold">{{ $brand->name }}</h5>
                <div class="text-warning fs-14 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= round($brand->average_rating) ? '★' : '☆' }}
                    @endfor
                    <small class="text-muted ms-1">({{ $brand->total_reviews }})</small>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <p class="mb-0 fw-bold fs-16">
                                {{ \App\Models\Product::where('seller_id', $seller->id)->where('status','approved')->count() }}
                            </p>
                            <small class="text-muted">Products</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <p class="mb-0 fw-bold fs-16">{{ $brand->total_reviews }}</p>
                            <small class="text-muted">Reviews</small>
                        </div>
                    </div>
                </div>
                @if($brand->website)
                <a href="{{ $brand->website }}" target="_blank"
                   class="btn btn-outline-primary btn-sm mt-3 w-100">
                    <i class="feather-external-link me-1"></i> Visit Website
                </a>
                @endif

                {{-- Share Brand --}}
                <div class="mt-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100"
                            onclick="shareBrand()">
                        <i class="feather-share-2 me-1"></i> Share Brand
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endif

@endsection

@push('scripts')
<script>
function shareBrand() {
    var url = '{{ $brand ? route('brands.show', $brand->slug) : '' }}';
    var name = '{{ $brand ? addslashes($brand->name) : '' }}';

    if (navigator.share) {
        navigator.share({
            title: name + ' — Orderer',
            text: 'Check out ' + name + ' on Orderer!',
            url: url,
        });
    } else { 
        navigator.clipboard.writeText(url).then(function () {
            alert('Brand link copied to clipboard!');
        }).catch(function () {
            prompt('Copy this link:', url);
        });
    }
}
</script>
@endpush