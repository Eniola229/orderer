@extends('layouts.admin')
@section('title', 'Pending Sellers')
@section('page_title', 'Pending Seller Applications')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.sellers.index') }}">Sellers</a></li>
    <li class="breadcrumb-item active">Pending</li>
@endsection

@section('content')

@if($sellers->count())
<div class="row">
    @foreach($sellers as $seller)
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold">{{ $seller->business_name }}</h6>
                    <small class="text-muted">{{ $seller->full_name }} · {{ $seller->email }}</small>
                </div>
                <span class="badge orderer-badge {{ $seller->is_verified_business ? 'badge-approved' : 'badge-draft' }}">
                    {{ $seller->is_verified_business ? 'Verified Business' : 'Individual' }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Phone</small>
                        <strong>{{ $seller->phone ?? '—' }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Address</small>
                        <strong>{{ $seller->business_address ?? '—' }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Applied</small>
                        <strong>{{ $seller->created_at->format('M d, Y H:i') }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Referral</small>
                        <strong>{{ $seller->referred_by ?? '—' }}</strong>
                    </div>
                </div>

                {{-- Documents --}}
                @if($seller->documents->count())
                <div class="mb-3">
                    <small class="text-muted fw-semibold d-block mb-2">Uploaded Documents</small>
                    @foreach($seller->documents as $doc)
                    <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-light rounded">
                        <i class="feather-file text-primary"></i>
                        <div>
                            <p class="mb-0 fs-13 fw-semibold">
                                {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                            </p>
                            <small class="text-muted">{{ $doc->document_number ?? '' }}</small>
                        </div>
                        <a href="{{ $doc->document_url }}" target="_blank"
                           class="btn btn-xs btn-outline-primary ms-auto"
                           style="font-size:11px;padding:2px 10px;">
                            View
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="alert alert-warning mb-3">
                    <i class="feather-alert-circle me-2"></i>
                    No documents uploaded (individual seller).
                </div>
                @endif

                <div class="d-flex gap-2">
                    @if(auth('admin')->user()->canModerateSellers())
                    <form action="{{ route('admin.sellers.approve', $seller->id) }}" method="POST">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="feather-check me-1"></i> Approve
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal{{ $seller->id }}">
                        <i class="feather-x me-1"></i> Reject
                    </button>
                    @endif

                    <a href="{{ route('admin.sellers.show', $seller->id) }}"
                       class="btn btn-outline-secondary btn-sm">
                        Details
                    </a>
                </div>
            </div>
        </div>

        {{-- Reject modal --}}
        <div class="modal fade" id="rejectModal{{ $seller->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.sellers.reject', $seller->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <label class="form-label fw-bold">Reason for rejection</label>
                            <textarea name="reason" class="form-control" rows="3"
                                      placeholder="Explain clearly..." required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="d-flex justify-content-center">{{ $sellers->links() }}</div>

@else
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="feather-check-circle mb-2 d-block" style="font-size:40px;color:#2ECC71;"></i>
        <p>No pending applications. All caught up!</p>
    </div>
</div>
@endif

@endsection