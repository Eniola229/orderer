@extends('layouts.admin')
@section('title', 'Ad Categories')
@section('page_title', 'Ad Categories')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.ads.index') }}">Ads</a></li>
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Ad Categories</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Name</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Active Ads</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $cat)
                            <tr>
                                <td class="fw-semibold">{{ $cat->name }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ str_replace('_', ' ', ucfirst($cat->type)) }}
                                    </span>
                                </td>
                                <td>{{ $cat->ads()->where('status','active')->count() }}</td>
                                <td>
                                    <span class="badge orderer-badge {{ $cat->is_active ? 'badge-approved' : 'badge-rejected' }}">
                                        {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.ads.categories.toggle', $cat->id) }}"
                                          method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit"
                                                class="btn btn-sm {{ $cat->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $cat->is_active ? 'Disable' : 'Enable' }}
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
                <h5 class="card-title mb-0">Add Ad Category</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.ads.categories.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               placeholder="e.g. Homepage Banner" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="banner_image">Banner Image</option>
                            <option value="banner_video">Banner Video</option>
                            <option value="top_listing">Top Listing</option>
                            <option value="cpc">CPC (Pay Per Order)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Brief description..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="feather-plus me-2"></i> Add Category
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection