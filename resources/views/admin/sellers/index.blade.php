@extends('layouts.admin')
@section('title', 'Sellers')
@section('page_title', 'Seller Management')
@section('breadcrumb')
    <li class="breadcrumb-item active">Sellers</li>
@endsection

@section('content')

<form action="{{ route('admin.sellers.index') }}" method="GET"
      class="d-flex gap-2 mb-4 flex-wrap">
    <div class="btn-group">
        @foreach(['all'=>'All','approved'=>'Approved','pending'=>'Pending','suspended'=>'Suspended'] as $val => $label)
        <a href="{{ route('admin.sellers.index', ['status' => $val]) }}"
           class="btn btn-sm {{ request('status','all') === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
    <div class="d-flex gap-2 ms-auto">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search name, email..." value="{{ request('search') }}"
               style="width:240px;">
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="feather-search"></i>
        </button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        @if($sellers->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Email</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Products</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Joined</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sellers as $seller)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($seller->avatar)
                                <img src="{{ $seller->avatar }}"
                                     style="width:36px;height:36px;border-radius:50%;object-fit:cover;" alt="">
                                @else
                                <div style="width:36px;height:36px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($seller->first_name, 0, 1)) }}
                                </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold fs-13">{{ $seller->business_name }}</p>
                                    <small class="text-muted">{{ $seller->full_name }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="fs-13 text-muted">{{ $seller->email }}</td>
                        <td>
                            @if($seller->is_verified_business)
                            <span class="badge orderer-badge badge-approved">Verified</span>
                            @else
                            <span class="badge orderer-badge badge-draft">Individual</span>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $seller->products_count ?? 0 }}</td>
                        <td>
                            @if(!$seller->is_approved)
                            <span class="badge orderer-badge badge-pending">Pending</span>
                            @elseif($seller->status === 'suspended')
                            <span class="badge orderer-badge badge-rejected">Suspended</span>
                            @else
                            <span class="badge orderer-badge badge-approved">Approved</span>
                            @endif
                        </td>
                        <td class="text-muted fs-12">{{ $seller->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.sellers.show', $seller->id) }}"
                                   class="btn btn-sm btn-outline-primary">View</a>
                                @if(!$seller->is_approved && auth('admin')->user()->canModerateSellers())
                                <form action="{{ route('admin.sellers.approve', $seller->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        Approve
                                    </button>
                                </form>
                                @endif
                                @if($seller->status !== 'suspended' && auth('admin')->user()->canModerateSellers())
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#suspendModal{{ $seller->id }}">
                                    Suspend
                                </button>
                                @elseif($seller->status === 'suspended' && auth('admin')->user()->canModerateSellers())
                                <form action="{{ route('admin.sellers.unsuspend', $seller->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-success">Reinstate</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- Suspend modal --}}
                    <div class="modal fade" id="suspendModal{{ $seller->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Suspend {{ $seller->business_name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.sellers.suspend', $seller->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body">
                                        <label class="form-label fw-bold">Reason</label>
                                        <textarea name="reason" class="form-control" rows="3" required
                                                  placeholder="Reason for suspension..."></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Suspend</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $sellers->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-users mb-2 d-block" style="font-size:40px;"></i>
            <p>No sellers found.</p>
        </div>
        @endif
    </div>
</div>

@endsection