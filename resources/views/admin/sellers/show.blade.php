@extends('layouts.admin')
@section('title', 'Seller: ' . $seller->business_name)
@section('page_title', $seller->business_name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.sellers.index') }}">Sellers</a></li>
    <li class="breadcrumb-item active">{{ $seller->business_name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">

        {{-- Profile --}}
        <div class="card mb-3">
            <div class="card-body text-center">
                @if($seller->avatar)
                <img src="{{ $seller->avatar }}"
                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #2ECC71;margin-bottom:12px;" alt="">
                @else
                <div style="width:80px;height:80px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;margin:0 auto 12px;">
                    {{ strtoupper(substr($seller->first_name,0,1)) }}
                </div>
                @endif
                <h5 class="fw-bold">{{ $seller->business_name }}</h5>
                <p class="text-muted fs-13">{{ $seller->full_name }}</p>
                <p class="text-muted fs-13"><i class="feather-mail me-1"></i> {{ $seller->email }}</p>
                <p class="text-muted fs-13"><i class="feather-phone me-1"></i> {{ $seller->phone ?? 'Not provided' }}</p>
                <p class="text-muted fs-13"><i class="feather-map-pin me-1"></i> {{ $seller->business_address ?? 'Not provided' }}</p>
                <p class="text-muted fs-13"><i class="feather-file-text me-1"></i> {{ $seller->business_description ?? 'No description' }}</p>

                <div class="d-flex gap-2 justify-content-center flex-wrap mb-3">
                    @if($seller->verification_status === 'rejected')
                        <span class="badge orderer-badge badge-rejected">
                            <i class="feather-x-circle me-1"></i> Account Rejected
                        </span>
                    @else
                        <span class="badge orderer-badge {{ $seller->is_approved ? 'badge-approved' : 'badge-pending' }}">
                            {{ $seller->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    @endif

                    @if($seller->is_verified_business)
                        <span class="badge orderer-badge badge-verified">
                            <i class="feather-check-circle me-1"></i> Verified Business
                        </span>
                    @endif

                    @if($seller->document && $seller->document->status === 'rejected')
                        <span class="badge orderer-badge badge-document-rejected">
                            <i class="feather-file-text me-1"></i> Document Rejected
                        </span>
                    @endif

                    @if($seller->is_active == 0)
                        <span class="badge orderer-badge badge-suspended">
                            <i class="feather-alert-circle me-1"></i> Suspended
                        </span>
                    @endif
                </div>

                @if($seller->verification_status === 'rejected' && $seller->rejection_reason)
                    <div class="alert alert-danger mt-3">
                        <i class="feather-alert-triangle me-2"></i>
                        <strong>Account Rejection Reason:</strong> {{ $seller->rejection_reason }}
                    </div>
                @endif

                @if($seller->document && $seller->document->status === 'rejected' && $seller->document->rejection_reason)
                    <div class="alert alert-warning mt-2">
                        <i class="feather-file-text me-2"></i>
                        <strong>Document Rejection Reason:</strong> {{ $seller->document->rejection_reason }}
                    </div>
                @endif

                <div class="border rounded p-3 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Total Orders</small>
                        <strong>{{ $orderCount }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Total Earnings</small>
                        <strong class="text-success">₦{{ number_format($totalEarnings, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Wallet Balance</small>
                        <strong class="text-primary">₦{{ number_format($wallet->balance ?? 0, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Ads Balance</small>
                        <strong class="text-info">₦{{ number_format($wallet->ads_balance ?? 0, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Joined</small>
                        <strong>{{ $seller->created_at->format('M d, Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Seller Stats --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Seller Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Products</small>
                            <strong class="h5 mb-0">{{ $seller->products->count() }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Services</small>
                            <strong class="h5 mb-0">{{ $seller->services->count() }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Properties</small>
                            <strong class="h5 mb-0">{{ $seller->properties->count() }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Total Sales</small>
                            <strong class="h5 mb-0">₦{{ number_format($totalEarnings ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Avg Rating</small>
                            <strong class="h5 mb-0">
                                @if($seller->brand)
                                    {{ number_format($seller->brand->average_rating ?? 0, 1) }} ★
                                @else
                                    0 ★
                                @endif
                            </strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Reviews</small>
                            <strong class="h5 mb-0">{{ $seller->brand->total_reviews ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wallet adjustment -- finance only --}}
        @if(auth('admin')->user()->canManageFinance())
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Wallet Adjustment</h5>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Balance</small>
                            <strong class="text-success">₦{{ number_format($wallet->balance ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Ads Balance</small>
                            <strong class="text-primary">₦{{ number_format($wallet->ads_balance ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>
                <form action="{{ route('admin.sellers.wallet', $seller->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-2">
                        <label class="form-label fw-bold fs-13">Wallet</label>
                        <select name="wallet_type" class="form-select form-select-sm">
                            <option value="balance">Main Wallet</option>
                            <option value="ads">Ads Balance</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold fs-13">Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="credit">Credit</option>
                            <option value="debit">Debit</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₦</span>
                            <input type="number" name="amount" class="form-control"
                                   min="0.01" step="0.01" placeholder="Amount" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="reason" class="form-control form-control-sm"
                               placeholder="Reason (required)" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100"
                            onclick="return confirm('Apply wallet adjustment?')">
                        Apply
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Approval actions --}}
        @if(auth('admin')->user()->canModerateSellers())
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Actions</h5></div>
            <div class="card-body d-grid gap-2">
                @if(!$seller->is_approved)
                <form action="{{ route('admin.sellers.approve', $seller->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-success w-100">
                        <i class="feather-check me-2"></i> Approve Seller
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger"
                        onclick="openRejectModal('{{ $seller->id }}', '{{ addslashes($seller->business_name) }}')">
                    <i class="feather-x me-2"></i> Reject Application
                </button>
                @elseif($seller->is_active == 0)
                <form action="{{ route('admin.sellers.unsuspend', $seller->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-success w-100">
                        <i class="feather-user-check me-2"></i> Reinstate
                    </button>
                </form>
                @else
                <button type="button" class="btn btn-outline-danger"
                        onclick="openSuspendModal('{{ $seller->id }}', '{{ addslashes($seller->business_name) }}')">
                    <i class="feather-user-x me-2"></i> Suspend
                </button>
                @endif
            </div>
        </div>
        @endif

    </div>{{-- /col-lg-4 --}}

    <div class="col-lg-8">

        {{-- Documents --}}
        @if($seller->documents->count())
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Submitted Documents</h5>
            </div>
            <div class="card-body">
                @foreach($seller->documents as $doc)
                <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded">
                    <i class="feather-file-text text-primary" style="font-size:24px;"></i>
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-semibold fs-13">
                            {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                        </p>
                        @if($doc->status)
                        <small class="text-muted">Status:
                            <span class="badge {{ $doc->status === 'approved' ? 'bg-success' : ($doc->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </small>
                        @endif
                    </div>
                    <a href="{{ $doc->document_url }}" target="_blank"
                       class="btn btn-sm btn-outline-primary">
                        <i class="feather-external-link me-1"></i> View
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Listings Tabs: Products / Services / Properties ─────────────── --}}
        <div class="card mb-3">
            <div class="card-header p-0">
                <ul class="nav nav-tabs border-0 px-3 pt-2" id="listingsTabs">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-products">
                            <i class="feather-box me-1"></i>
                            Products
                            <span class="badge bg-secondary ms-1">{{ $seller->products->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-services">
                            <i class="feather-briefcase me-1"></i>
                            Services
                            <span class="badge bg-secondary ms-1">{{ $seller->services->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-properties">
                            <i class="feather-home me-1"></i>
                            Properties
                            <span class="badge bg-secondary ms-1">{{ $seller->properties->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content">

                {{-- Products tab --}}
                <div class="tab-pane fade show active" id="tab-products">
                    <div class="card-header d-flex align-items-center justify-content-between border-top-0 rounded-0" style="border-top: 1px solid #dee2e6;">
                        <span class="fs-13 text-muted">{{ $seller->products->count() }} product(s) total</span>
                        <a href="{{ route('admin.products.index', ['search' => $seller->business_name]) }}"
                           class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    @if($seller->products->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Product</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Stock</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seller->products->take(8) as $product)
                                <tr>
                                    <td class="fw-semibold fs-13">
                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                           class="text-dark listing-title-link">
                                            {{ Str::limit($product->name, 45) }}
                                        </a>
                                    </td>
                                    <td class="fw-bold">₦{{ number_format($product->price, 2) }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <span class="badge orderer-badge badge-{{ $product->status }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="feather-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($seller->products->count() > 8)
                    <div class="p-3 text-center text-muted fs-12 border-top">
                        Showing 8 of {{ $seller->products->count() }} —
                        <a href="{{ route('admin.products.index', ['search' => $seller->business_name]) }}">view all</a>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="feather-box d-block mb-2" style="font-size:28px;"></i>
                        <p class="mb-0 fs-13">No products listed yet.</p>
                    </div>
                    @endif
                </div>

                {{-- Services tab --}}
                <div class="tab-pane fade" id="tab-services">
                    <div class="card-header d-flex align-items-center justify-content-between" style="border-top: 1px solid #dee2e6;">
                        <span class="fs-13 text-muted">{{ $seller->services->count() }} service(s) total</span>
                    </div>
                    @if($seller->services->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Service</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Pricing</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Delivery</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Rating</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seller->services->take(8) as $service)
                                <tr>
                                    <td class="fw-semibold fs-13">
                                        <a href="{{ route('admin.services.show', $service->id) }}"
                                           class="text-dark listing-title-link">
                                            {{ Str::limit($service->title, 45) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fs-11">
                                            {{ ucfirst($service->pricing_type) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">
                                        @if($service->price)
                                            ₦{{ number_format($service->price, 2) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted fs-12">{{ $service->delivery_time ?? '—' }}</td>
                                    <td>
                                        <span class="text-warning">★</span>
                                        {{ number_format($service->average_rating ?? 0, 1) }}
                                        <small class="text-muted">({{ $service->total_reviews ?? 0 }})</small>
                                    </td>
                                    <td>
                                        <span class="badge orderer-badge badge-{{ $service->status }}">
                                            {{ ucfirst($service->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.services.show', $service->id) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="feather-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($seller->services->count() > 8)
                    <div class="p-3 text-center text-muted fs-12 border-top">
                        Showing 8 of {{ $seller->services->count() }}
                    </div>
                    @endif
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="feather-briefcase d-block mb-2" style="font-size:28px;"></i>
                        <p class="mb-0 fs-13">No services listed yet.</p>
                    </div>
                    @endif
                </div>

                {{-- Properties tab --}}
                <div class="tab-pane fade" id="tab-properties">
                    <div class="card-header d-flex align-items-center justify-content-between" style="border-top: 1px solid #dee2e6;">
                        <span class="fs-13 text-muted">{{ $seller->properties->count() }} propert{{ $seller->properties->count() === 1 ? 'y' : 'ies' }} total</span>
                    </div>
                    @if($seller->properties->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Title</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Listing</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Location</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Beds / Baths</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seller->properties->take(8) as $property)
                                <tr>
                                    <td class="fw-semibold fs-13">
                                        <a href="{{ route('admin.houses.show', $property->id) }}"
                                           class="text-dark listing-title-link">
                                            {{ Str::limit($property->title, 40) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fs-11">
                                            {{ ucfirst(str_replace('_', ' ', $property->property_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge fs-11 {{ $property->listing_type === 'sale' ? 'bg-primary' : 'bg-info text-dark' }}">
                                            For {{ ucfirst($property->listing_type) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">₦{{ number_format($property->price, 2) }}</td>
                                    <td class="text-muted fs-12">{{ Str::limit($property->city . ', ' . $property->state, 25) }}</td>
                                    <td class="text-muted fs-12">
                                        @if($property->bedrooms || $property->bathrooms)
                                            {{ $property->bedrooms ?? '—' }} bed /
                                            {{ $property->bathrooms ?? '—' }} bath
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge orderer-badge badge-{{ $property->status }}">
                                            {{ ucfirst($property->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.houses.show', $property->id) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="feather-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($seller->properties->count() > 8)
                    <div class="p-3 text-center text-muted fs-12 border-top">
                        Showing 8 of {{ $seller->properties->count() }}
                    </div>
                    @endif
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="feather-home d-block mb-2" style="font-size:28px;"></i>
                        <p class="mb-0 fs-13">No properties listed yet.</p>
                    </div>
                    @endif
                </div>

            </div>{{-- /tab-content --}}
        </div>{{-- /card --}}

        {{-- Wallet Transactions --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="feather-activity me-2"></i> Recent Wallet Transactions
                </h5>
                @if($wallet)
                <a href="{{ route('admin.finance.transactions', ['search' => $seller->email]) }}"
                   class="btn btn-sm btn-outline-primary">View All</a>
                @endif
            </div>
            <div class="card-body p-0">
                @if($transactions && count($transactions) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Reference</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Balance After</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $txn)
                            @php
                                $isCredit = in_array($txn->type, ['credit','escrow_release','referral_credit','escrow_refund']);
                                $statusColors = [
                                    'completed' => '#28a745',
                                    'pending'   => '#ffc107',
                                    'failed'    => '#dc3545',
                                    'reversed'  => '#6c757d',
                                ];
                                $statusColor = $statusColors[$txn->status] ?? '#6c757d';
                            @endphp
                            <tr>
                                <td><code class="fs-12">{{ Str::limit($txn->reference, 20) }}</code></td>
                                <td>
                                    <span class="badge orderer-badge {{ $isCredit ? 'badge-approved' : 'badge-pending' }}">
                                        {{ str_replace('_', ' ', ucfirst($txn->type)) }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                    {{ $isCredit ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="fw-semibold">₦{{ number_format($txn->balance_after, 2) }}</td>
                                <td>
                                    <span class="badge" style="background-color:{{ $statusColor }};color:{{ $txn->status === 'pending' ? '#212529' : '#ffffff' }};padding:5px 10px;border-radius:4px;font-size:11px;font-weight:600;">
                                        {{ ucfirst($txn->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="feather-activity mb-2 d-block" style="font-size:32px;"></i>
                    <p>No wallet transactions yet.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Referrals --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="feather-users me-2"></i> Referrals
                </h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary">{{ $referralStats['total'] }} total</span>
                    <span class="badge bg-success">₦{{ number_format($referralStats['earned'], 2) }} earned</span>
                    @if($referralStats['pending'] > 0)
                        <span class="badge bg-warning text-dark">₦{{ number_format($referralStats['pending'], 2) }} pending</span>
                    @endif
                </div>
            </div>
            <div class="px-3 pt-3 pb-2">
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted fw-semibold text-uppercase" style="white-space:nowrap;">Referral Code:</small>
                    <code class="fs-13 text-primary fw-bold">{{ $seller->referral_code ?? '—' }}</code>
                    @if($seller->referral_code)
                    <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="adminCopyRef('{{ $seller->referral_code }}')"
                            title="Copy referral link">
                        <i class="feather-copy"></i>
                    </button>
                    @endif
                    @if($seller->referred_by)
                    <span class="ms-3 text-muted fs-12">
                        <i class="feather-corner-down-right me-1"></i>
                        Referred by code: <code>{{ $seller->referred_by }}</code>
                    </span>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if($sellerReferrals->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Referred Seller</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Business</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Joined</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Earnings</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sellerReferrals as $ref)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:28px;height:28px;border-radius:50%;background:#2ECC71;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px;flex-shrink:0;">
                                            {{ strtoupper(substr($ref->referred->first_name ?? 'S', 0, 1)) }}
                                        </div>
                                        <span class="fw-semibold fs-13">{{ $ref->referred->full_name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="text-muted fs-13">{{ $ref->referred->business_name ?? '—' }}</td>
                                <td class="text-muted fs-12">{{ $ref->created_at->format('M d, Y') }}</td>
                                <td>
                                    @foreach($ref->earnings as $earning)
                                        <span class="fw-bold text-success d-block">₦{{ number_format($earning->amount, 2) }}</span>
                                    @endforeach
                                    @if($ref->earnings->isEmpty()) <span class="text-muted">—</span> @endif
                                </td>
                                <td>
                                    @foreach($ref->earnings as $earning)
                                        <span class="badge orderer-badge badge-{{ $earning->status === 'credited' ? 'approved' : 'pending' }}">
                                            {{ ucfirst($earning->status) }}
                                        </span>
                                    @endforeach
                                    @if($ref->earnings->isEmpty())
                                        <span class="badge orderer-badge badge-pending">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="feather-users d-block mb-2" style="font-size:28px;"></i>
                    <p class="mb-0 fs-13">This seller hasn't referred anyone yet.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Brand --}}
        @php $brand = $seller->brand; @endphp
        @if($brand)
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Brand Information</h5></div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($brand->logo)
                    <img src="{{ $brand->logo }}"
                         style="width:80px;height:80px;object-fit:contain;border-radius:8px;background:#f5f5f5;padding:8px;" alt="">
                    @endif
                    <div>
                        <p class="mb-0 fw-bold h5">{{ $brand->name }}</p>
                        <p class="text-muted fs-13">{{ $brand->description ?? 'No description' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <small class="text-muted d-block">Rating</small>
                            <div class="text-warning">
                                @for($i=1;$i<=5;$i++)
                                    {{ $i <= round($brand->average_rating ?? 0) ? '★' : '☆' }}
                                @endfor
                                <small class="text-muted">({{ number_format($brand->average_rating ?? 0, 1) }})</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <small class="text-muted d-block">Total Reviews</small>
                            <strong class="h6 mb-0">{{ number_format($brand->total_reviews ?? 0) }}</strong>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('brands.show', $brand->slug) }}" target="_blank"
                       class="btn btn-sm btn-outline-primary">
                        <i class="feather-external-link me-1"></i> View Brand Page
                    </a>
                </div>
            </div>
        </div>
        @endif

    </div>{{-- /col-lg-8 --}}
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:12px;max-width:500px;width:90%;margin:auto;box-shadow:0 10px 40px rgba(0,0,0,0.2);animation:modalFadeIn 0.3s ease;">
        <div style="padding:20px;border-bottom:1px solid #e5e7eb;">
            <h5 style="margin:0;font-size:18px;font-weight:600;">Reject Seller Application</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf @method('PUT')
            <div style="padding:20px;">
                <p id="modalSellerInfo" style="margin-bottom:20px;color:#6b7280;font-size:14px;"></p>
                <div style="margin-bottom:20px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;font-size:14px;">Reason</label>
                    <textarea name="reason" rows="4" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" placeholder="Why are you rejecting this seller?" required></textarea>
                </div>
            </div>
            <div style="padding:20px;border-top:1px solid #e5e7eb;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding:8px 20px;background:#f3f4f6;border:none;border-radius:6px;cursor:pointer;font-size:14px;">Cancel</button>
                <button type="submit" style="padding:8px 20px;background:#dc2626;color:white;border:none;border-radius:6px;cursor:pointer;font-size:14px;">Reject</button>
            </div>
        </form>
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:12px;max-width:500px;width:90%;margin:auto;box-shadow:0 10px 40px rgba(0,0,0,0.2);animation:modalFadeIn 0.3s ease;">
        <div style="padding:20px;border-bottom:1px solid #e5e7eb;">
            <h5 style="margin:0;font-size:18px;font-weight:600;">Suspend Seller</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf @method('PUT')
            <div style="padding:20px;">
                <p id="suspendSellerInfo" style="margin-bottom:20px;color:#6b7280;font-size:14px;"></p>
                <div style="margin-bottom:20px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;font-size:14px;">Reason</label>
                    <textarea name="reason" rows="4" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" placeholder="Reason for suspension..." required></textarea>
                </div>
            </div>
            <div style="padding:20px;border-top:1px solid #e5e7eb;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding:8px 20px;background:#f3f4f6;border:none;border-radius:6px;cursor:pointer;font-size:14px;">Cancel</button>
                <button type="submit" style="padding:8px 20px;background:#dc2626;color:white;border:none;border-radius:6px;cursor:pointer;font-size:14px;">Suspend</button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity:0; transform:translateY(-20px); }
    to   { opacity:1; transform:translateY(0); }
}
/* Make tab badges smaller and subtler */
#listingsTabs .badge { font-size: 10px; font-weight: 600; }
#listingsTabs .nav-link { font-size: 13px; border-radius: 6px 6px 0 0; }
#listingsTabs .nav-link.active { background: #fff; border-bottom-color: #fff; }

/* Clickable listing title links */
.listing-title-link {
    text-decoration: none;
    transition: color 0.15s ease;
}
.listing-title-link:hover {
    color: #0d6efd !important;
    text-decoration: underline;
}
</style>

<script>
function openRejectModal(id, sellerName) {
    document.getElementById('rejectForm').action =
        "{{ route('admin.sellers.reject', ['seller' => '__ID__']) }}".replace('__ID__', id);
    document.getElementById('modalSellerInfo').innerHTML =
        `<strong>${sellerName}</strong><br>This seller application will be rejected.`;
    document.getElementById('rejectModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    document.body.style.overflow = '';
}
function openSuspendModal(id, sellerName) {
    document.getElementById('suspendForm').action =
        "{{ route('admin.sellers.suspend', ['seller' => '__ID__']) }}".replace('__ID__', id);
    document.getElementById('suspendSellerInfo').innerHTML =
        `<strong>${sellerName}</strong><br>This seller will be suspended and unable to list products.`;
    document.getElementById('suspendModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeSuspendModal() {
    document.getElementById('suspendModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
document.getElementById('suspendModal').addEventListener('click', function(e) {
    if (e.target === this) closeSuspendModal();
});
function adminCopyRef(code) {
    const link = 'https://ordererweb.com/seller/register?ref=' + code;
    navigator.clipboard.writeText(link).then(() => {
        alert('Referral link copied: ' + link);
    }).catch(() => {
        prompt('Copy this referral link:', link);
    });
}
</script>

@endsection