@extends('layouts.admin')
@section('title', 'Seller Badges')
@section('page_title', 'Seller Badges')
@section('breadcrumb')
    <li class="breadcrumb-item active">Badges</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">All Badges</h5>
                <form action="{{ route('admin.badges.auto-award') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="feather-award me-1"></i> Run Auto-Award
                    </button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Badge</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Criteria</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Awarded To</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Award Manually</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($badges as $badge)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:36px;height:36px;border-radius:8px;background:{{ $badge->color }}20;display:flex;align-items:center;justify-content:center;">
                                            <i class="{{ $badge->icon }}" style="color:{{ $badge->color }};font-size:18px;"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold fs-13"
                                               style="color:{{ $badge->color }}">
                                                {{ $badge->name }}
                                            </p>
                                            <small class="text-muted">{{ $badge->description }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fs-13">
                                    @if($badge->criteria_type === 'manual')
                                        <span class="text-muted">Manual only</span>
                                    @elseif($badge->criteria_type === 'orders_count')
                                        {{ $badge->criteria_value }}+ completed orders
                                    @elseif($badge->criteria_type === 'rating')
                                        Rating {{ number_format($badge->criteria_value / 10, 1) }}+
                                    @elseif($badge->criteria_type === 'verified')
                                        Verified business
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $badge->sellers_count }} seller(s)
                                    </span>
                                </td>
                                <td>
                                    <span class="badge orderer-badge {{ $badge->is_active ? 'badge-approved' : 'badge-rejected' }}">
                                        {{ $badge->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.badges.award', $badge->id) }}"
                                          method="POST" class="d-flex gap-2">
                                        @csrf
                                        <select name="seller_id" class="form-select form-select-sm"
                                                style="width:180px;" required>
                                            <option value="">Select seller...</option>
                                            @foreach(\App\Models\Seller::where('is_approved', true)->orderBy('business_name')->get() as $seller)
                                            <option value="{{ $seller->id }}">{{ $seller->business_name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            Award
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
                <h5 class="card-title mb-0">Create Badge</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.badges.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Badge Name</label>
                        <input type="text" name="name" class="form-control"
                               placeholder="e.g. Top Seller" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Icon (Feather icon name)</label>
                        <input type="text" name="icon" class="form-control"
                               placeholder="feather-award">
                        <small class="text-muted">Use feather icon names e.g. feather-star</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Color</label>
                        <input type="color" name="color" class="form-control form-control-color"
                               value="#2ECC71">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <input type="text" name="description" class="form-control"
                               placeholder="Brief description of this badge">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Criteria Type</label>
                        <select name="criteria_type" class="form-select" required>
                            <option value="manual">Manual (admin awards only)</option>
                            <option value="orders_count">Orders Count</option>
                            <option value="rating">Product Rating</option>
                            <option value="verified">Verified Business</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Criteria Value</label>
                        <input type="number" name="criteria_value" class="form-control"
                               placeholder="e.g. 50 for 50 orders, 45 for 4.5 rating">
                        <small class="text-muted">For rating: multiply by 10 (e.g. 45 = 4.5★)</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="feather-plus me-2"></i> Create Badge
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection