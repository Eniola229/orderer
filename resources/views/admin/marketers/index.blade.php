@extends('layouts.admin')
@section('title', 'Marketers')
@section('page_title', 'Marketers')
@section('breadcrumb')
    <li class="breadcrumb-item active">Marketers</li>
@endsection

@section('content')

@if(!auth('admin')->user()->canManageAdmins())
<div class="alert alert-danger">
    <i class="feather-lock me-2"></i> You do not have permission to manage marketers.
</div>
@else

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-26 fw-800 text-dark">{{ $marketers->total() }}</div>
            <div class="text-muted fs-13">Total Marketers</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-26 fw-800 text-success">{{ $marketers->where('is_active', true)->count() }}</div>
            <div class="text-muted fs-13">Active</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-26 fw-800 text-danger">{{ $marketers->where('is_active', false)->count() }}</div>
            <div class="text-muted fs-13">Suspended</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">All Marketers</h5>
        <a href="{{ route('admin.marketers.create') }}" class="btn btn-primary btn-sm">
            <i class="feather-user-plus me-1"></i> Add Marketer
        </a>
    </div>
    <div class="card-body p-0">
        @if($marketers->isEmpty())
        <div class="text-center py-5">
            <i class="feather-users" style="font-size:40px;color:#d1d5db;"></i>
            <p class="text-muted mt-3">No marketers yet. <a href="{{ route('admin.marketers.create') }}">Create one</a>.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Marketing Code</th>
                        <th>Referrals</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marketers as $i => $mkt)
                    <tr>
                        <td class="text-muted fs-13">{{ ($marketers->currentPage() - 1) * $marketers->perPage() + $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $mkt->full_name }}</td>
                        <td class="text-muted fs-13">{{ $mkt->email }}</td>
                        <td>
                            @if($mkt->marketing_code)
                            <code class="fs-13 bg-light px-2 py-1 rounded">{{ $mkt->marketing_code }}</code>
                            @else
                            <span class="text-muted fs-12">Not generated</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary fw-semibold">
                                {{ $mkt->referred_sellers_count }}
                            </span>
                        </td>
                        <td>
                            @if($mkt->is_active)
                            <span class="badge bg-success-subtle text-success fw-semibold">Active</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger fw-semibold">Suspended</span>
                            @endif
                        </td>
                        <td class="text-muted fs-13">
                            {{ $mkt->last_login_at ? $mkt->last_login_at->diffForHumans() : 'Never' }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.marketers.show', $mkt) }}"
                                   class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="feather-eye"></i>
                                </a>
                                <a href="{{ route('admin.marketers.edit', $mkt) }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="feather-edit-2"></i>
                                </a>
                                @if($mkt->is_active)
                                <form method="POST" action="{{ route('admin.marketers.suspend', $mkt) }}">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm btn-outline-danger" title="Suspend"
                                            onclick="return confirm('Suspend this marketer?')">
                                        <i class="feather-slash"></i>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.marketers.activate', $mkt) }}">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm btn-outline-success" title="Activate">
                                        <i class="feather-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($marketers->hasPages())
        <div class="p-3 border-top">{{ $marketers->links() }}</div>
        @endif
        @endif
    </div>
</div>

@endif
@endsection