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
        </div>

        {{-- Referrals table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold">Referred Sellers</h6>
            </div>
            <div class="card-body p-0">
                @if($marketer->referredSellers->isEmpty())
                <div class="text-center py-5">
                    <i class="feather-users" style="font-size:36px;color:#d1d5db;"></i>
                    <p class="text-muted mt-3 mb-0">No sellers referred yet.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Seller</th>
                                <th>Business</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($marketer->referredSellers as $seller)
                            <tr>
                                <td class="text-muted fs-13">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold fs-14">{{ $seller->full_name }}</div>
                                    <div class="text-muted fs-12">{{ $seller->email }}</div>
                                </td>
                                <td class="fs-13">{{ $seller->business_name }}</td>
                                <td>
                                    @if($seller->is_approved)
                                        <span class="badge bg-success-subtle text-success fw-semibold">Approved</span>
                                    @elseif($seller->verification_status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger fw-semibold">Rejected</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning fw-semibold">Pending</span>
                                    @endif
                                </td>
                                <td class="text-muted fs-13">{{ $seller->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.sellers.show', $seller) }}"
                                       class="btn btn-sm btn-outline-secondary">
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