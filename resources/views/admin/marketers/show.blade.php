@extends('layouts.admin')
@section('title', $marketer->full_name)
@section('page_title', $marketer->full_name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.marketers.index') }}">Marketers</a></li>
    <li class="breadcrumb-item active">{{ $marketer->full_name }}</li>
@endsection

@section('content') 

@if(!auth('admin')->user()->canManageAdmins())
<div class="alert alert-danger">
    <i class="feather-lock me-2"></i> You do not have permission to view marketer details.
</div>
@else

<div class="row g-4">

    {{-- Left: marketer info --}}
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <div style="width:64px;height:64px;border-radius:50%;background:#1a1f2e;
                            display:flex;align-items:center;justify-content:center;
                            margin:0 auto 12px;font-size:22px;font-weight:700;color:#2ECC71;">
                    {{ strtoupper(substr($marketer->first_name,0,1)) }}{{ strtoupper(substr($marketer->last_name,0,1)) }}
                </div>
                <h5 class="mb-0 fw-bold">{{ $marketer->full_name }}</h5>
                <p class="text-muted fs-13 mb-2">{{ $marketer->email }}</p>
                @if($marketer->is_active)
                    <span class="badge bg-success-subtle text-success fw-semibold">Active</span>
                @else
                    <span class="badge bg-danger-subtle text-danger fw-semibold">Suspended</span>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">Marketing Code</h6>
            </div>
            <div class="card-body text-center py-4">
                @if($marketer->marketing_code)
                <div style="background:#1a1f2e;color:#2ECC71;font-family:monospace;
                            font-size:18px;font-weight:700;letter-spacing:2px;
                            padding:14px 20px;border-radius:8px;display:inline-block;">
                    {{ $marketer->marketing_code }}
                </div>
                <form method="POST" action="{{ route('admin.marketers.regen-code', $marketer) }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning"
                            onclick="return confirm('Regenerate this marketer\'s code?')">
                        <i class="feather-refresh-cw me-1"></i> Regenerate Code
                    </button>
                </form>
                @else
                <p class="text-muted fs-13">No code generated yet.</p>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless fs-13 mb-0">
                    <tr>
                        <td class="text-muted fw-semibold" style="width:120px;">Joined</td>
                        <td>{{ $marketer->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Last Login</td>
                        <td>{{ $marketer->last_login_at ? $marketer->last_login_at->diffForHumans() : 'Never' }}</td>
                    </tr>
                    @if($marketer->notes)
                    <tr>
                        <td class="text-muted fw-semibold">Notes</td>
                        <td>{{ $marketer->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.marketers.edit', $marketer) }}" class="btn btn-primary btn-sm flex-grow-1">
                <i class="feather-edit-2 me-1"></i> Edit
            </a>
            @if($marketer->is_active)
            <form method="POST" action="{{ route('admin.marketers.suspend', $marketer) }}">
                @csrf @method('PUT')
                <button class="btn btn-outline-danger btn-sm"
                        onclick="return confirm('Suspend this marketer? They will not be able to log in.')">
                    <i class="feather-slash me-1"></i> Suspend
                </button>
            </form>
            @else
            <form method="POST" action="{{ route('admin.marketers.activate', $marketer) }}">
                @csrf @method('PUT')
                <button class="btn btn-outline-success btn-sm">
                    <i class="feather-check-circle me-1"></i> Activate
                </button>
            </form>
            @endif
        </div>

    </div>

    {{-- Right: referral stats + table --}}
    <div class="col-lg-8">

        {{-- Referral Stats --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">Referral Overview</h6>
            </div>
            <div class="card-body">
                {{-- Seller stats --}}
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-2" style="letter-spacing:.5px;">Sellers</p>
                <div class="row g-3 mb-3">
                    <div class="col-6 col-sm-4">
                        <div class="card border-0 bg-light text-center p-3">
                            <div class="fs-24 fw-800 text-dark">{{ $stats['total'] }}</div>
                            <div class="text-muted fs-12">Total Referrals</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="card border-0 bg-light text-center p-3">
                            <div class="fs-24 fw-800 text-success">{{ $stats['approved'] }}</div>
                            <div class="text-muted fs-12">Approved</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="card border-0 bg-light text-center p-3">
                            <div class="fs-24 fw-800 text-warning">{{ $stats['pending'] }}</div>
                            <div class="text-muted fs-12">Pending</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="card border-0 bg-light text-center p-3">
                            <div class="fs-24 fw-800 text-info">{{ $stats['verified'] }}</div>
                            <div class="text-muted fs-12">Verified</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="card border-0 bg-light text-center p-3">
                            <div class="fs-24 fw-800 text-secondary">{{ $stats['unverified'] }}</div>
                            <div class="text-muted fs-12">Unverified</div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                {{-- Listing stats --}}
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-2" style="letter-spacing:.5px;">Listings Uploaded by Referrals</p>
                <div class="row g-3">
                    <div class="col-4">
                        <div class="card border-0 text-center p-3" style="background:#eff6ff;">
                            <div class="fs-24 fw-800" style="color:#2563eb;">{{ $stats['total_products'] }}</div>
                            <div class="fs-12 mt-1" style="color:#2563eb;">
                                <i class="feather-shopping-bag me-1"></i> Products
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 text-center p-3" style="background:#f5f3ff;">
                            <div class="fs-24 fw-800" style="color:#7c3aed;">{{ $stats['total_properties'] }}</div>
                            <div class="fs-12 mt-1" style="color:#7c3aed;">
                                <i class="feather-home me-1"></i> Properties
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 text-center p-3" style="background:#fff1f2;">
                            <div class="fs-24 fw-800" style="color:#e11d48;">{{ $stats['total_services'] }}</div>
                            <div class="fs-12 mt-1" style="color:#e11d48;">
                                <i class="feather-tool me-1"></i> Services
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">
 
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-2" style="letter-spacing:.5px;">
                    Buyers Referred
                </p>
                <div class="row g-3 mb-3">
                    <div class="col-6 col-sm-4">
                        <div class="card border-0 bg-light text-center p-3">
                            <div class="fs-24 fw-800" style="color:#7B1FA2;">{{ $buyerStats['total'] }}</div>
                            <div class="text-muted fs-12">Total Buyers</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="card border-0 text-center p-3" style="background:#f0fdf4;">
                            <div class="fs-24 fw-800 text-success">{{ $buyerStats['with_orders'] }}</div>
                            <div class="text-muted fs-12">Placed Orders</div>
                            @if($buyerStats['total'] > 0)
                            <div class="fs-11 text-success">
                                {{ round(($buyerStats['with_orders'] / $buyerStats['total']) * 100, 1) }}%
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="card border-0 text-center p-3" style="background:#eff6ff;">
                            <div class="fs-24 fw-800 text-primary">{{ $buyerStats['with_bookings'] }}</div>
                            <div class="text-muted fs-12">Booked Delivery</div>
                            @if($buyerStats['total'] > 0)
                            <div class="fs-11 text-primary">
                                {{ round(($buyerStats['with_bookings'] / $buyerStats['total']) * 100, 1) }}%
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="card border-0 text-center p-3" style="background:#fff7ed;">
                            <div class="fs-24 fw-800" style="color:#ea580c;">{{ $buyerStats['total_orders'] }}</div>
                            <div class="text-muted fs-12">Total Orders Placed</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="card border-0 text-center p-3" style="background:#fef2f2;">
                            <div class="fs-24 fw-800" style="color:#dc2626;">{{ $buyerStats['total_bookings'] }}</div>
                            <div class="text-muted fs-12">Total Deliveries Booked</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section for Sellers --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">Filter Sellers</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.marketers.show', $marketer) }}" class="row g-3">
                    <div class="col-12">
                        <label class="form-label fs-13 fw-semibold">Search Seller</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="feather-search"></i></span>
                            <input type="text" name="seller_search" class="form-control"
                                   placeholder="Search by name, email, or business name..."
                                   value="{{ $sellerSearch ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">Date From</label>
                        <input type="date" name="seller_date_from" class="form-control form-control-sm"
                               value="{{ $sellerDateFrom ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">Date To</label>
                        <input type="date" name="seller_date_to" class="form-control form-control-sm"
                               value="{{ $sellerDateTo ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">Verification Status</label>
                        <select name="verification_filter" class="form-select form-select-sm">
                            <option value="all" {{ ($verificationFilter ?? 'all') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="pending" {{ ($verificationFilter ?? 'all') == 'pending' ? 'selected' : '' }}>Pending Verification</option>
                            <option value="approved" {{ ($verificationFilter ?? 'all') == 'approved' ? 'selected' : '' }}>Verified</option>
                            <option value="rejected" {{ ($verificationFilter ?? 'all') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">Approval Status</label>
                        <select name="status_filter" class="form-select form-select-sm">
                            <option value="all" {{ ($statusFilter ?? 'all') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="approved" {{ ($statusFilter ?? 'all') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ ($statusFilter ?? 'all') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="feather-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.marketers.show', $marketer) }}" class="btn btn-secondary btn-sm">
                            <i class="feather-rotate-ccw me-1"></i> Reset All Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Referrals table (Sellers) --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Referred Sellers</h6>
                @if($sellers->count() > 0)
                    <span class="badge bg-secondary">{{ $sellers->count() }} sellers</span>
                @endif
            </div>
            <div class="card-body p-0">
                @if($sellers->isEmpty())
                <div class="text-center py-5">
                    <i class="feather-users" style="font-size:36px;color:#d1d5db;"></i>
                    <p class="text-muted mt-3 mb-0">No sellers found with the selected filters.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Seller</th>
                                <th>Business</th>
                                <th>Email</th>
                                <th>Verified?</th>
                                <th>Status</th>
                                <th class="text-center">
                                    <i class="feather-shopping-bag me-1 text-primary"></i>Products
                                </th>
                                <th class="text-center">
                                    <i class="feather-home me-1" style="color:#7c3aed;"></i>Properties
                                </th>
                                <th class="text-center">
                                    <i class="feather-tool me-1 text-danger"></i>Services
                                </th>
                                <th>Joined</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sellers as $seller)
                            <tr>
                                <td class="text-muted fs-13">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold fs-14">{{ $seller->full_name }}</div>
                                 </td>
                                <td class="fs-13">{{ $seller->business_name ?? 'N/A' }}</td>
                                <td class="fs-13">{{ $seller->email }}</td>
                                <td>
                                    @if($seller->verification_status === 'approved')
                                        <span class="badge bg-success-subtle text-success fw-semibold">
                                            <i class="feather-check-circle me-1"></i> Yes
                                        </span>
                                    @elseif($seller->verification_status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger fw-semibold">
                                            <i class="feather-x-circle me-1"></i> No
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning fw-semibold">
                                            <i class="feather-clock me-1"></i> Pending
                                        </span>
                                    @endif
                                 </td>
                                <td>
                                    @if($seller->is_approved)
                                        <span class="badge bg-success-subtle text-success fw-semibold">Active</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning fw-semibold">Pending</span>
                                    @endif
                                 </td>
                                <td class="text-center">
                                    <span class="badge fw-semibold" style="background:#dbeafe;color:#1d4ed8;min-width:32px;">
                                        {{ $productCounts[$seller->id] ?? 0 }}
                                    </span>
                                 </td>
                                <td class="text-center">
                                    <span class="badge fw-semibold" style="background:#ede9fe;color:#6d28d9;min-width:32px;">
                                        {{ $propertyCounts[$seller->id] ?? 0 }}
                                    </span>
                                 </td>
                                <td class="text-center">
                                    <span class="badge fw-semibold" style="background:#ffe4e6;color:#be123c;min-width:32px;">
                                        {{ $serviceCounts[$seller->id] ?? 0 }}
                                    </span>
                                 </td>
                                <td class="text-muted fs-13">{{ $seller->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.sellers.show', $seller) }}"
                                       class="btn btn-sm btn-outline-secondary" title="View Seller">
                                        <i class="feather-eye"></i>
                                    </a>
                                 </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="6" class="text-end fw-semibold fs-13 text-muted">
                                    Totals (filtered view):
                                 </td>
                                <td class="text-center">
                                    <span class="badge fw-bold" style="background:#dbeafe;color:#1d4ed8;">
                                        {{ $sellers->sum(fn($s) => $productCounts[$s->id] ?? 0) }}
                                    </span>
                                 </td>
                                <td class="text-center">
                                    <span class="badge fw-bold" style="background:#ede9fe;color:#6d28d9;">
                                        {{ $sellers->sum(fn($s) => $propertyCounts[$s->id] ?? 0) }}
                                    </span>
                                 </td>
                                <td class="text-center">
                                    <span class="badge fw-bold" style="background:#ffe4e6;color:#be123c;">
                                        {{ $sellers->sum(fn($s) => $serviceCounts[$s->id] ?? 0) }}
                                    </span>
                                 </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- Buyers Table with Filters --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Referred Buyers</h6>
                @if($buyers->total() > 0)
                    <span class="badge bg-secondary">{{ $buyers->total() }} buyers</span>
                @endif
            </div>
            <div class="card-body">
                {{-- Buyer Filters --}}
                <form method="GET" action="{{ route('admin.marketers.show', $marketer) }}" class="row g-3 mb-4">
                    <input type="hidden" name="seller_search" value="{{ $sellerSearch ?? '' }}">
                    <input type="hidden" name="seller_date_from" value="{{ $sellerDateFrom ?? '' }}">
                    <input type="hidden" name="seller_date_to" value="{{ $sellerDateTo ?? '' }}">
                    <input type="hidden" name="verification_filter" value="{{ $verificationFilter ?? 'all' }}">
                    <input type="hidden" name="status_filter" value="{{ $statusFilter ?? 'all' }}">
                    
                    <div class="col-12">
                        <label class="form-label fs-13 fw-semibold">Search Buyer</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="feather-search"></i></span>
                            <input type="text" name="buyer_search" class="form-control"
                                   placeholder="Search by name or email..."
                                   value="{{ $buyerSearch ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <label class="form-label fs-13 fw-semibold">Registered From</label>
                        <input type="date" name="buyer_date_from" class="form-control form-control-sm"
                               value="{{ $buyerDateFrom ?? '' }}">
                    </div>
                    
                    <div class="col-md-5">
                        <label class="form-label fs-13 fw-semibold">Registered To</label>
                        <input type="date" name="buyer_date_to" class="form-control form-control-sm"
                               value="{{ $buyerDateTo ?? '' }}">
                    </div>
                    
                    <div class="col-md-7">
                        <label class="form-label fs-13 fw-semibold">Activity Status</label>
                        <select name="activity_filter" class="form-select form-select-sm">
                            <option value="all" {{ ($activityFilter ?? 'all') == 'all' ? 'selected' : '' }}>All Buyers</option>
                            <option value="ordered" {{ ($activityFilter ?? 'all') == 'ordered' ? 'selected' : '' }}>
                                Placed Orders Only
                            </option>
                            <option value="booked" {{ ($activityFilter ?? 'all') == 'booked' ? 'selected' : '' }}>
                                Booked Deliveries Only
                            </option>
                            <option value="both" {{ ($activityFilter ?? 'all') == 'both' ? 'selected' : '' }}>
                                Both Orders & Bookings
                            </option>
                            <option value="inactive" {{ ($activityFilter ?? 'all') == 'inactive' ? 'selected' : '' }}>
                                Inactive (No Activity)
                            </option>
                        </select>
                    </div>
                    
                    <div class="col-md-5 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm me-2">
                            <i class="feather-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.marketers.show', $marketer) }}" class="btn btn-secondary btn-sm">
                            <i class="feather-rotate-ccw me-1"></i> Reset
                        </a>
                    </div>
                </form>

                {{-- Buyers Table --}}
                @if($buyers->isEmpty())
                <div class="text-center py-5">
                    <i class="feather-users" style="font-size:36px;color:#d1d5db;"></i>
                    <p class="text-muted mt-3 mb-0">No buyers found with the selected filters.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Buyer</th>
                                <th>Email</th>
                                <th class="text-center">
                                    <i class="feather-shopping-cart"></i> Orders
                                </th>
                                <th class="text-center">
                                    <i class="feather-truck"></i> Bookings
                                </th>
                                <th>Activity</th>
                                <th>Registered</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($buyers as $buyer)
                            @php
                                $orders = $buyerOrderCounts[$buyer->id] ?? 0;
                                $bookings = $buyerBookingCounts[$buyer->id] ?? 0;
                            @endphp
                            <tr>
                                <td class="text-muted fs-13">
                                    {{ ($buyers->currentPage() - 1) * $buyers->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <div class="fw-semibold fs-14">{{ $buyer->full_name }}</div>
                                </td>
                                <td class="fs-13">{{ $buyer->email }}</td>
                                <td class="text-center">
                                    <span class="badge fw-semibold {{ $orders > 0 ? 'bg-success-subtle text-success' : 'bg-light text-muted' }}">
                                        {{ $orders }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge fw-semibold {{ $bookings > 0 ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }}">
                                        {{ $bookings }}
                                    </span>
                                </td>
                                <td>
                                    @if($orders > 0 && $bookings > 0)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="feather-star me-1"></i> Fully Active
                                        </span>
                                    @elseif($orders > 0)
                                        <span class="badge bg-warning-subtle text-warning">
                                            <i class="feather-shopping-cart me-1"></i> Ordered
                                        </span>
                                    @elseif($bookings > 0)
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="feather-truck me-1"></i> Booked Only
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted">Registered Only</span>
                                    @endif
                                </td>
                                <td class="text-muted fs-13">{{ $buyer->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.buyers.show', $buyer) }}"
                                       class="btn btn-sm btn-outline-secondary" title="View Buyer">
                                        <i class="feather-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($buyers->hasPages())
                <div class="mt-3">
                    {{ $buyers->appends(request()->query())->links() }}
                </div>
                @endif
                @endif
            </div>
        </div>

    </div>
</div>

@endif
@endsection