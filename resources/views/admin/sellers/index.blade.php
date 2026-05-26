@extends('layouts.admin')
@section('title', 'Sellers')
@section('page_title', 'Seller Management')
@section('breadcrumb')
    <li class="breadcrumb-item active">Sellers</li>
@endsection

@section('content')

{{-- ── Stats Grid ──────────────────────────────────────────────────────── --}}
<div class="sellers-stats-grid mb-4">

    <div class="stat-card stat-total">
        <div class="stat-icon"><i class="feather-users"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['total']) }}</span>
            <span class="stat-label">Total Sellers</span>
        </div>
    </div>

    <div class="stat-card stat-pending">
        <div class="stat-icon"><i class="feather-clock"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['pending']) }}</span>
            <span class="stat-label">Pending</span>
        </div>
    </div>

    <div class="stat-card stat-approved">
        <div class="stat-icon"><i class="feather-check-circle"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['approved']) }}</span>
            <span class="stat-label">Approved</span>
        </div>
    </div>

    <div class="stat-card stat-active">
        <div class="stat-icon"><i class="feather-activity"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['active']) }}</span>
            <span class="stat-label">Active</span>
        </div>
    </div>

    <div class="stat-card stat-suspended">
        <div class="stat-icon"><i class="feather-slash"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['suspended']) }}</span>
            <span class="stat-label">Suspended</span>
        </div>
    </div>

    <div class="stat-card stat-verified">
        <div class="stat-icon"><i class="feather-shield"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['verified']) }}</span>
            <span class="stat-label">Verified Business</span>
        </div>
    </div>

    <div class="stat-card stat-individual">
        <div class="stat-icon"><i class="feather-user"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['individual']) }}</span>
            <span class="stat-label">Individual</span>
        </div>
    </div>

    <div class="stat-card stat-wallet">
        <div class="stat-icon"><i class="feather-credit-card"></i></div>
        <div class="stat-body">
            <span class="stat-value mono">&#8358;{{ number_format($stats['total_wallet'], 2) }}</span>
            <span class="stat-label">Total Wallet Balance</span>
        </div>
    </div>

    <div class="stat-card stat-ads-bal">
        <div class="stat-icon"><i class="feather-zap"></i></div>
        <div class="stat-body">
            <span class="stat-value mono">&#8358;{{ number_format($stats['total_ads_bal'], 2) }}</span>
            <span class="stat-label">Total Ads Balance</span>
        </div>
    </div>

    <div class="stat-card stat-orders">
        <div class="stat-icon"><i class="feather-shopping-bag"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['total_orders']) }}</span>
            <span class="stat-label">Total Orders</span>
        </div>
    </div>

    <div class="stat-card stat-running-ads">
        <div class="stat-icon"><i class="feather-radio"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['running_ads']) }}</span>
            <span class="stat-label">Running Ads</span>
        </div>
    </div>

    <div class="stat-card stat-products">
        <div class="stat-icon"><i class="feather-box"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['with_products']) }}</span>
            <span class="stat-label">With Products</span>
        </div>
    </div>

    <div class="stat-card stat-services">
        <div class="stat-icon"><i class="feather-briefcase"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['with_services']) }}</span>
            <span class="stat-label">With Services</span>
        </div>
    </div>

    <div class="stat-card stat-properties">
        <div class="stat-icon"><i class="feather-home"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['with_properties']) }}</span>
            <span class="stat-label">With Properties</span>
        </div>
    </div>

</div>

{{-- ── Filter / Search bar ─────────────────────────────────────────────── --}}
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

{{-- ── Table ───────────────────────────────────────────────────────────── --}}
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
                                    <button type="submit" class="btn btn-sm btn-outline-success">Approve</button>
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

{{-- ── Suspend Modal ───────────────────────────────────────────────────── --}}
<div id="suspendModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:12px;max-width:500px;width:90%;margin:auto;box-shadow:0 10px 40px rgba(0,0,0,0.2);animation:modalFadeIn 0.3s ease;">
        <div style="padding:20px;border-bottom:1px solid #e5e7eb;">
            <h5 style="margin:0;font-size:18px;font-weight:600;">Suspend Seller</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf @method('PUT')
            <div style="padding:20px;">
                <p id="modalSellerInfo" style="margin-bottom:20px;color:#6b7280;font-size:14px;"></p>
                <div style="margin-bottom:20px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;font-size:14px;">Reason</label>
                    <textarea name="reason" rows="4" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" placeholder="Reason for suspension..." required></textarea>
                </div>
            </div>
            <div style="padding:20px;border-top:1px solid #e5e7eb;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding:8px 20px;background:#f3f4f6;border:none;border-radius:6px;cursor:pointer;font-size:14px;">Cancel</button>
                <button type="submit" style="padding:8px 20px;background:#dc2626;color:white;border:none;border-radius:6px;cursor:pointer;font-size:14px;">Suspend Seller</button>
            </div>
        </form>
    </div>
</div>

<style>
.sellers-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}
.stat-card {
    background: #fff;
    border-radius: 10px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid #e9ecef;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
    transition: box-shadow .15s;
    min-width: 0;
    overflow: hidden; /* prevent card from blowing out */
}
.stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.stat-icon {
    width: 38px; height: 38px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 17px;
}
.stat-body { display: flex; flex-direction: column; min-width: 0; flex: 1; overflow: hidden; }
.stat-value {
    font-size: 17px;
    font-weight: 700;
    line-height: 1.3;
    color: #111;
    /* allow long numbers to wrap naturally, never truncate */
    white-space: normal;
    word-break: break-word;
    overflow-wrap: anywhere;
}
/* Currency values: slightly smaller, monospace, always fully visible */
.stat-value.mono {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 12.5px;
    font-weight: 700;
    white-space: normal;
    word-break: break-word;
    overflow-wrap: anywhere;
    letter-spacing: -0.02em;
}
.stat-label { font-size: 11px; color: #6c757d; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.stat-total      .stat-icon { background:#eff6ff; color:#2563eb; }
.stat-pending    .stat-icon { background:#fffbeb; color:#d97706; }
.stat-approved   .stat-icon { background:#f0fdf4; color:#16a34a; }
.stat-active     .stat-icon { background:#ecfdf5; color:#059669; }
.stat-suspended  .stat-icon { background:#fff1f2; color:#e11d48; }
.stat-verified   .stat-icon { background:#f5f3ff; color:#7c3aed; }
.stat-individual .stat-icon { background:#f8fafc;  color:#475569; }
.stat-wallet     .stat-icon { background:#eff6ff; color:#1d4ed8; }
.stat-ads-bal    .stat-icon { background:#fdf4ff; color:#a21caf; }
.stat-orders     .stat-icon { background:#fff7ed; color:#ea580c; }
.stat-running-ads .stat-icon { background:#fef9c3; color:#ca8a04; }
.stat-products   .stat-icon { background:#f0fdf4; color:#15803d; }
.stat-services   .stat-icon { background:#eff6ff; color:#0369a1; }
.stat-properties .stat-icon { background:#fdf2f8; color:#be185d; }

@keyframes modalFadeIn {
    from { opacity:0; transform:translateY(-20px); }
    to   { opacity:1; transform:translateY(0); }
}
@media (max-width:576px) {
    .sellers-stats-grid { grid-template-columns: repeat(2,1fr); }
}
</style>

<script>
    function openSuspendModal(id, sellerName) {
        document.getElementById('suspendForm').action = `/admin/sellers/${id}/suspend`;
        document.getElementById('modalSellerInfo').innerHTML =
            `<strong>${sellerName}</strong><br>This seller will be suspended and unable to list products.`;
        document.getElementById('suspendModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeSuspendModal() {
        document.getElementById('suspendModal').style.display = 'none';
        document.body.style.overflow = '';
    }
    document.getElementById('suspendModal').addEventListener('click', function(e) {
        if (e.target === this) closeSuspendModal();
    });
</script>

@endsection