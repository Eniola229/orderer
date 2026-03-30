@extends('layouts.admin')
@section('title', 'Banner Slots')
@section('page_title', 'Banner Slots & Pricing')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.ads.index') }}">Ads</a></li>
    <li class="breadcrumb-item active">Banner Slots</li>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Banner Slots</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Slot</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Location</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Price/Day</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Max Ads</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Dimensions</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($slots as $slot)
                            <tr>
                                <td class="fw-semibold">{{ $slot->name }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ ucfirst(str_replace('_', ' ', $slot->location)) }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.ads.slots.price', $slot->id) }}"
                                          method="POST"
                                          class="d-flex align-items-center gap-2">
                                        @csrf @method('PUT')
                                        <div class="input-group input-group-sm" style="width:120px;">
                                            <span class="input-group-text">₦</span>
                                            <input type="number"
                                                   name="price_per_day"
                                                   class="form-control"
                                                   value="{{ $slot->price_per_day }}"
                                                   step="0.01"
                                                   min="0.01">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="feather-save"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $slot->max_ads }}</td>
                                <td class="text-muted fs-12">{{ $slot->dimensions ?? '—' }}</td>
                                <td>
                                    <span class="badge orderer-badge {{ $slot->is_active ? 'badge-approved' : 'badge-rejected' }}">
                                        {{ $slot->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.ads.slots.toggle', $slot->id) }}"
                                          method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit"
                                                class="btn btn-sm {{ $slot->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $slot->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add Banner Slot</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ads.slots.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               placeholder="e.g. Homepage Hero Banner" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Location <span class="text-danger">*</span></label>
                        <select name="location" class="form-select" required>
                            <option value="homepage_hero">Homepage Hero</option>
                            <option value="category_page">Category Page</option>
                            <option value="product_page_sidebar">Product Page Sidebar</option>
                            <option value="search_results">Search Results</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Price per Day (NGN) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₦</span>
                            <input type="number" name="price_per_day" class="form-control"
                                   step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Max Concurrent Ads</label>
                        <input type="number" name="max_ads" class="form-control"
                               value="5" min="1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Dimensions (optional)</label>
                        <input type="text" name="dimensions" class="form-control"
                               placeholder="e.g. 1200x400">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="feather-plus me-2"></i> Add Slot
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection