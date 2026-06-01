@extends('layouts.admin')
@section('title', 'Withdrawals')
@section('page_title', 'Withdrawal Requests')
@section('breadcrumb')
    <li class="breadcrumb-item active">Withdrawals</li>
@endsection

@section('content')

<div class="row mb-4">
    <div class="col-md-4 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Pending</p>
                <h2 class="fw-bold mb-0 text-warning">{{ $stats['pending'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Approved</p>
                <h2 class="fw-bold mb-0 text-success">{{ $stats['approved'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Paid Out</p>
                <h2 class="fw-bold mb-0 text-primary">₦{{ number_format($stats['total_paid'], 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mb-4">
    @foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','processing'=>'Processing','failed'=>'Failed'] as $val=>$label)
    <a href="{{ route('admin.withdrawals.index', array_merge(request()->only(['search','date_from','date_to','amount_min','amount_max']), ['status'=>$val])) }}"
       class="btn btn-sm {{ request('status','all')===$val ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ $label }}
    </a>
    @endforeach
</div>


{{-- ── Search & Filter Bar ─────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.withdrawals.index') }}" id="filterForm">
    {{-- preserve status tab selection --}}
    @if(request('status') && request('status') !== 'all')
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif

    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-2 align-items-end">

                {{-- Search --}}
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="feather-search" style="font-size:13px;"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Seller, account number, reference…"
                               value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Date From --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ request('date_from') }}">
                </div>

                {{-- Date To --}}
                <div class="col-lg-2 col-md-3 col-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ request('date_to') }}">
                </div>

                {{-- Amount Min --}}
                <div class="col-lg-1 col-md-3 col-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">Min ₦</label>
                    <input type="number" name="amount_min" class="form-control form-control-sm"
                           placeholder="0" min="0" value="{{ request('amount_min') }}">
                </div>

                {{-- Amount Max --}}
                <div class="col-lg-1 col-md-3 col-6">
                    <label class="form-label fs-12 fw-semibold text-muted mb-1">Max ₦</label>
                    <input type="number" name="amount_max" class="form-control form-control-sm"
                           placeholder="∞" min="0" value="{{ request('amount_max') }}">
                </div>

                {{-- Buttons --}}
                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="feather-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search','date_from','date_to','amount_min','amount_max']))
                        <a href="{{ route('admin.withdrawals.index', request()->only('status')) }}"
                           class="btn btn-outline-secondary btn-sm"
                           title="Clear filters">
                            <i class="feather-x"></i>
                        </a>
                    @endif
                </div>

            </div>

            {{-- Active filter summary --}}
            @if(request()->hasAny(['search','date_from','date_to','amount_min','amount_max']))
                <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted fw-semibold">Active filters:</small>
                    @if(request('search'))
                        <span class="badge bg-light text-dark border">
                            Search: "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('date_from') || request('date_to'))
                        <span class="badge bg-light text-dark border">
                            Date: {{ request('date_from') ?? '…' }} → {{ request('date_to') ?? 'now' }}
                        </span>
                    @endif
                    @if(request('amount_min') || request('amount_max'))
                        <span class="badge bg-light text-dark border">
                            Amount: ₦{{ number_format(request('amount_min', 0)) }} – {{ request('amount_max') ? '₦'.number_format(request('amount_max')) : '∞' }}
                        </span>
                    @endif
                    <small class="text-muted">— {{ $withdrawals->total() }} result(s)</small>
                </div>
            @endif
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        @if($withdrawals->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Amount (NGN)</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Bank</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Account</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Requested</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawals as $wd)
                    <tr
                        data-reject-url="{{ route('admin.withdrawals.reject', $wd->id) }}"
                        data-wd-id="{{ $wd->id }}"
                    >
                        <td>
                            <a href="{{ route('admin.sellers.show', $wd->seller_id) }}"
                               class="text-decoration-none d-block"
                               title="View seller profile">
                                <p class="mb-0 fw-semibold fs-13 text-primary">
                                    {{ $wd->seller->business_name ?? '—' }}
                                    <i class="feather-external-link" style="font-size:10px;opacity:.6;"></i>
                                </p>
                                <small class="text-muted">{{ $wd->seller->email ?? '' }}</small>
                            </a>
                        </td>
                        <td>
                            <span class="fw-bold text-success">₦{{ number_format($wd->amount, 2) }}</span>
                        </td>
                        <td class="fs-13">{{ $wd->bank_name }}</td>
                        <td class="fs-13">
                            {{ $wd->account_name }}<br>
                            <code class="fs-12">{{ $wd->account_number }}</code>
                            @if($wd->bank_code)
                                <small class="text-muted">({{ $wd->bank_code }})</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="
                                @if($wd->status === 'pending')        background:#ffc107;color:#212529;
                                @elseif($wd->status === 'approved')   background:#28a745;color:#fff;
                                @elseif($wd->status === 'rejected')   background:#dc3545;color:#fff;
                                @elseif($wd->status === 'completed')  background:#17a2b8;color:#fff;
                                @elseif($wd->status === 'failed')     background:#343a40;color:#fff;
                                @elseif($wd->status === 'processing') background:#007bff;color:#fff;
                                @else                                 background:#6c757d;color:#fff;
                                @endif
                                padding:5px 10px;border-radius:4px;font-size:12px;font-weight:600;">
                                {{ ucfirst($wd->status) }}
                            </span>
                            {{-- Korapay transfer status --}}
                            @if($wd->korapay_status)
                                @php
                                    $kStyle = match($wd->korapay_status) {
                                        'success'    => 'background:#d1fae5;color:#065f46;',
                                        'processing' => 'background:#dbeafe;color:#1e40af;',
                                        'failed'     => 'background:#fee2e2;color:#991b1b;',
                                        default      => 'background:#f3f4f6;color:#374151;',
                                    };
                                @endphp
                                <br><span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:3px;{{ $kStyle }}">
                                    Korapay: {{ strtoupper($wd->korapay_status) }}
                                </span>
                            @endif
                            @if($wd->rejection_reason)
                                <p class="fs-11 text-muted mb-0 mt-1">{{ $wd->rejection_reason }}</p>
                            @endif
                            @if($wd->korapay_reference)
                                <p class="fs-10 text-muted mb-0 mt-1">
                                    <code style="font-size:10px;">{{ $wd->korapay_reference }}</code>
                                </p>
                            @endif
                        </td>
                        <td class="text-muted fs-12">
                            {{ $wd->created_at->format('M d, Y') }}
                            @if($wd->processed_at)
                                <br><small class="text-success">Processed: {{ $wd->processed_at->format('M d') }}</small>
                            @endif
                        </td>
                        <td>
                            @if(auth('admin')->user()->canManageFinance())
                                <div class="d-flex flex-column gap-1">

                                    @if($wd->status === 'processing')

                                        {{-- Server error state — let admin resolve manually --}}
                                        <form action="{{ route('admin.withdrawals.change-status', $wd->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Reset to pending? Only do this if the Korapay transfer was NOT sent.')">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                                <i class="feather-rotate-ccw me-1"></i> Reset to Pending
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.withdrawals.change-status', $wd->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Force approve? Only do this after confirming the transfer succeeded in the Korapay dashboard.')">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success w-100">
                                                <i class="feather-check me-1"></i> Force Approve
                                            </button>
                                        </form>

                                    @elseif($wd->status === 'pending')

                                        <form action="{{ route('admin.withdrawals.approve', $wd->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Approve this withdrawal and initiate payout?')">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success w-100">
                                                <i class="feather-check me-1"></i> Approve
                                            </button>
                                        </form>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger w-100"
                                                onclick="openRejectModal(this, '{{ $wd->amount }}', '{{ addslashes($wd->seller->business_name ?? 'Unknown') }}')">
                                            <i class="feather-x me-1"></i> Reject
                                        </button>

                                    @elseif($wd->status === 'approved')
                                        <small class="text-success fw-semibold">
                                            <i class="feather-check-circle me-1"></i>
                                            Paid {{ $wd->processed_at?->format('M d') }}
                                        </small>

                                    @elseif($wd->status === 'rejected')
                                        <small class="text-danger fw-semibold">
                                            <i class="feather-x-circle me-1"></i> Rejected
                                        </small>
                                    @endif

                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $withdrawals->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-arrow-up-right mb-2 d-block" style="font-size:40px;"></i>
            <p>No withdrawal requests found.</p>
        </div>
        @endif
    </div>
</div>

{{-- ── Reject Modal ─────────────────────────────────────────────────────── --}}
<div id="rejectModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;max-width:500px;width:90%;margin:auto;box-shadow:0 10px 40px rgba(0,0,0,0.2);animation:modalFadeIn .3s ease;">
        <div style="padding:20px;border-bottom:1px solid #e5e7eb;">
            <h5 style="margin:0;font-size:18px;font-weight:600;">Reject Withdrawal</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding:20px;">
                <p id="modalAmountInfo" style="margin-bottom:20px;color:#6b7280;font-size:14px;"></p>
                <div style="margin-bottom:20px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;font-size:14px;">Reason</label>
                    <textarea name="reason" rows="4"
                              style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;"
                              placeholder="Why are you rejecting this withdrawal?" required></textarea>
                </div>
            </div>
            <div style="padding:20px;border-top:1px solid #e5e7eb;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeRejectModal()"
                        style="padding:8px 20px;background:#f3f4f6;border:none;border-radius:6px;cursor:pointer;font-size:14px;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:8px 20px;background:#dc2626;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px;">
                    Reject & Refund
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity:0; transform:translateY(-20px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

<script>
function getRow(el) {
    return el.closest('tr');
}

function openRejectModal(btn, amount, sellerName) {
    const row   = getRow(btn);
    const url   = row.dataset.rejectUrl;
    const modal = document.getElementById('rejectModal');

    document.getElementById('rejectForm').action = url;
    document.getElementById('modalAmountInfo').innerHTML =
        `₦${parseFloat(amount).toFixed(2)} from <strong>${sellerName}</strong><br>Amount will be refunded to seller wallet.`;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>

@endsection