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

        {{-- Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fs-28 fw-800 text-dark">{{ $stats['total'] }}</div>
                    <div class="text-muted fs-13">Total Referrals</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fs-28 fw-800 text-success">{{ $stats['approved'] }}</div>
                    <div class="text-muted fs-13">Approved</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fs-28 fw-800 text-warning">{{ $stats['pending'] }}</div>
                    <div class="text-muted fs-13">Pending</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fs-28 fw-800 text-info">{{ $stats['verified'] }}</div>
                    <div class="text-muted fs-13">Verified</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card border-0 shadow-sm text-center p-3">
                    <div class="fs-28 fw-800 text-secondary">{{ $stats['unverified'] }}</div>
                    <div class="text-muted fs-13">Unverified</div>
                </div>
            </div>
        </div>

        {{-- Enhanced Filter Section --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">Filter Sellers</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.marketers.show', $marketer) }}" class="row g-3">
                    {{-- Seller Search --}}
                    <div class="col-12">
                        <label class="form-label fs-13 fw-semibold">Search Seller</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="feather-search"></i></span>
                            <input type="text" name="seller_search" class="form-control" 
                                   placeholder="Search by name, email, or business name..."
                                   value="{{ $sellerSearch }}">
                        </div>
                    </div>
                    
                    {{-- Date Range --}}
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" 
                               value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" 
                               value="{{ $dateTo }}">
                    </div>
                    
                    {{-- Verification Status --}}
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">Verification Status</label>
                        <select name="verification_filter" class="form-select form-select-sm">
                            <option value="all" {{ $verificationFilter == 'all' ? 'selected' : '' }}>All</option>
                            <option value="pending" {{ $verificationFilter == 'pending' ? 'selected' : '' }}>Pending Verification</option>
                            <option value="approved" {{ $verificationFilter == 'approved' ? 'selected' : '' }}>Verified</option>
                            <option value="rejected" {{ $verificationFilter == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    
                    {{-- Approval Status --}}
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">Approval Status</label>
                        <select name="status_filter" class="form-select form-select-sm">
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All</option>
                            <option value="approved" {{ $statusFilter == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        </select>
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="feather-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.marketers.show', $marketer) }}" class="btn btn-secondary btn-sm">
                            <i class="feather-rotate-ccw me-1"></i> Reset All Filters
                        </a>
                        @if($sellerSearch || $dateFrom || $dateTo || ($verificationFilter && $verificationFilter != 'all') || ($statusFilter && $statusFilter != 'all'))
                            <span class="text-muted fs-12 ms-2">
                                <i class="feather-info me-1"></i> Showing {{ $sellers->count() }} result(s)
                            </span>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Referrals table --}}
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
                    @if($sellerSearch || $dateFrom || $dateTo || ($verificationFilter && $verificationFilter != 'all') || ($statusFilter && $statusFilter != 'all'))
                        <button class="btn btn-link btn-sm mt-2" onclick="window.location.href='{{ route('admin.marketers.show', $marketer) }}'">
                            <i class="feather-rotate-ccw me-1"></i> Clear all filters
                        </button>
                    @endif
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
                    </table>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endif
@endsection