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
                    亚
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
                            @elseif($seller->is_active == 0)
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
                                @if($seller->is_active == 1 && auth('admin')->user()->canModerateSellers())
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="openSuspendModal('{{ $seller->id }}', '{{ addslashes($seller->business_name) }}')">
                                        Suspend
                                    </button>
                                @elseif($seller->is_active == 0 && auth('admin')->user()->canModerateSellers())
                                    <form action="{{ route('admin.sellers.unsuspend', $seller->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-success">Reinstate</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
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

{{-- Custom Modal (same style as withdrawals) --}}
<div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Suspend Seller</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="modalSellerInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Reason</label>
                    <textarea name="reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Reason for suspension..." required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Suspend Seller</button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    function openSuspendModal(id, sellerName) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const sellerInfo = document.getElementById('modalSellerInfo');
        
        form.action = `/admin/sellers/${id}/suspend`;
        sellerInfo.innerHTML = `<strong>${sellerName}</strong><br>This seller will be suspended and unable to list products.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeSuspendModal() {
        const modal = document.getElementById('suspendModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close modal when clicking outside
    document.getElementById('suspendModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSuspendModal();
        }
    });
</script>

@endsection