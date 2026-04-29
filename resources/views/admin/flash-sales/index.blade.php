@extends('layouts.admin')
@section('title', 'Flash Sales')
@section('page_title', 'Flash Sales')
@section('breadcrumb')
    <li class="breadcrumb-item active">Flash Sales</li>
@endsection
@section('page_actions')
    <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> New Flash Sale
    </a>
@endsection

@section('content')
<!-- Stats Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fs-11 fw-semibold">Total Flash Sales</span>
                        <h2 class="mt-2 mb-0">{{ $stats['total'] }}</h2>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="feather-grid text-primary" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fs-11 fw-semibold">Active</span>
                        <h2 class="mt-2 mb-0 text-success">{{ $stats['active'] }}</h2>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="feather-zap text-success" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fs-11 fw-semibold">Paused</span>
                        <h2 class="mt-2 mb-0 text-warning">{{ $stats['paused'] }}</h2>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="feather-pause-circle text-warning" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fs-11 fw-semibold">Ended</span>
                        <h2 class="mt-2 mb-0 text-danger">{{ $stats['ended'] }}</h2>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="feather-calendar text-danger" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.flash-sales.index') }}" class="row g-3">
            <!-- Search -->
            <div class="col-md-3">
                <label class="form-label fs-11 text-muted">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Product or title..." value="{{ request('search') }}">
            </div>

            <!-- Status Filter -->
            <div class="col-md-2">
                <label class="form-label fs-11 text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Live</option>
                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                    <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>Ended</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                </select>
            </div>

            <!-- Seller Filter - Searchable using Datalist -->
            <div class="col-md-3">
                <label class="form-label fs-11 text-muted">Seller</label>
                <input type="text" list="seller-list" name="seller_id" 
                       class="form-control form-control-sm" 
                       placeholder="Type to search seller..."
                       value="{{ $sellers->firstWhere('id', request('seller_id'))->business_name ?? '' }}">
                <datalist id="seller-list">
                    <option value="">All Sellers</option>
                    @foreach($sellers as $seller)
                        <option value="{{ $seller->business_name }}" data-id="{{ $seller->id }}">
                    @endforeach
                </datalist>
                <small class="text-muted fs-11">Type seller name to search</small>
            </div>

            <!-- Hidden input to store actual seller ID -->
            <input type="hidden" name="seller_id_hidden" id="seller_id_hidden" value="{{ request('seller_id') }}">

            <!-- Date Range: Created At -->
            <div class="col-md-2">
                <label class="form-label fs-11 text-muted">From Date (Created)</label>
                <input type="date" name="date_from" class="form-control form-control-sm" 
                       value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fs-11 text-muted">To Date (Created)</label>
                <input type="date" name="date_to" class="form-control form-control-sm" 
                       value="{{ request('date_to') }}">
            </div>

            <!-- Date Range: Starts At -->
            <div class="col-md-2">
                <label class="form-label fs-11 text-muted">Starts From</label>
                <input type="date" name="starts_from" class="form-control form-control-sm" 
                       value="{{ request('starts_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fs-11 text-muted">Starts To</label>
                <input type="date" name="starts_to" class="form-control form-control-sm" 
                       value="{{ request('starts_to') }}">
            </div>

            <!-- Date Range: Ends At -->
            <div class="col-md-2">
                <label class="form-label fs-11 text-muted">Ends From</label>
                <input type="date" name="ends_from" class="form-control form-control-sm" 
                       value="{{ request('ends_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fs-11 text-muted">Ends To</label>
                <input type="date" name="ends_to" class="form-control form-control-sm" 
                       value="{{ request('ends_to') }}">
            </div>

            <!-- Filter Actions -->
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100" id="apply-filters">
                    <i class="feather-filter me-1"></i> Apply
                </button>
                <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-secondary btn-sm w-100">
                    <i class="feather-x me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>


<!-- Main Table Card -->
<div class="card">
    <div class="card-body p-0">
        @if($flashSales->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Product</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Original</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Sale Price</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Discount</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Period</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Sold / Limit</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Live Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($flashSales as $sale)
                    @php
                        $discount = round((($sale->original_price - $sale->sale_price) / $sale->original_price) * 100);
                        $active   = $sale->is_active && now()->between($sale->starts_at, $sale->ends_at);
                    @endphp
                    <tr>
                        <td>
                            <div>
                                <p class="mb-0 fw-semibold fs-13">
                                    <a href="{{ route('admin.flash-sales.show', $sale) }}" class="text-decoration-none">
                                        {{ Str::limit($sale->product->name ?? '—', 35) }}
                                    </a>
                                </p>
                                <small class="text-muted">{{ Str::limit($sale->title, 30) }}</small>
                            </div>
                        </td>
                        <td class="fs-13 text-muted">
                            {{ $sale->product->seller->business_name ?? '—' }}
                        </td>
                        <td class="fs-13">₦{{ number_format($sale->original_price, 2) }}</td>
                        <td class="fw-bold text-success">₦{{ number_format($sale->sale_price, 2) }}</td>
                        <td>
                            <span class="badge" style="background:#FADBD8;color:#E74C3C;">
                                -{{ $discount }}%
                            </span>
                        </td>
                        <td class="fs-12 text-muted">
                            {{ $sale->starts_at->format('M d, g:i A') }}<br>
                            <small>to</small><br>
                            {{ $sale->ends_at->format('M d, g:i A') }}
                        </td>
                        <td class="fs-13">
                            {{ $sale->quantity_sold }}
                            /
                            {{ $sale->quantity_limit ?? '∞' }}
                        </td>
                        <td>
                            @if($active)
                                <span class="badge badge-success" style="background:#28a745;color:#fff;">Live</span>
                            @elseif($sale->ends_at < now())
                                <span class="badge badge-secondary" style="background:#6c757d;color:#fff;">Ended</span>
                            @elseif(!$sale->is_active)
                                <span class="badge badge-warning" style="background:#ffc107;color:#212529;">Paused</span>
                            @else
                                <span class="badge badge-info" style="background:#17a2b8;color:#fff;">Scheduled</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.flash-sales.show', $sale) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="feather-eye"></i>
                                </a>
                                
                                @if($active || $sale->is_active)
                                <form action="{{ route('admin.flash-sales.toggle', $sale->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button type="submit"
                                            class="btn btn-sm {{ $sale->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                        {{ $sale->is_active ? 'Pause' : 'Activate' }}
                                    </button>
                                </form>
                                @endif
                                
                                <form action="{{ route('admin.flash-sales.destroy', $sale->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this flash sale permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $flashSales->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-zap mb-2 d-block" style="font-size:40px;"></i>
            <p>No flash sales found matching your filters.</p>
            <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-secondary">
                Clear Filters
            </a>
            <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-primary">
                Create Flash Sale
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

<!-- JavaScript to handle seller search mapping -->
@push('scripts')
<script>
    // Map seller names to IDs
    const sellerMap = {
        @foreach($sellers as $seller)
            "{{ $seller->business_name }}": "{{ $seller->id }}",
        @endforeach
    };

    // Get the seller input and hidden field
    const sellerInput = document.querySelector('input[name="seller_id"]');
    const sellerHidden = document.querySelector('#seller_id_hidden');
    
    // When user selects from datalist, update hidden field with ID
    if (sellerInput) {
        sellerInput.addEventListener('input', function() {
            const selectedName = this.value;
            if (sellerMap[selectedName]) {
                sellerHidden.value = sellerMap[selectedName];
            } else if (selectedName === '') {
                sellerHidden.value = '';
            }
        });
    }
    
    // On form submit, replace seller_id field with hidden value
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Remove the original seller_id input and replace with hidden
            const originalSellerInput = document.querySelector('input[name="seller_id"]');
            if (originalSellerInput) {
                originalSellerInput.removeAttribute('name');
            }
            if (sellerHidden) {
                sellerHidden.setAttribute('name', 'seller_id');
            }
        });
    }
</script>
@endpush
