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
    @foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $val=>$label)
    <a href="{{ route('admin.withdrawals.index', ['status'=>$val]) }}"
       class="btn btn-sm {{ request('status','all')===$val ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

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
                        data-otp-url="{{ route('admin.withdrawals.authorize-otp', $wd->id) }}"
                        data-reject-url="{{ route('admin.withdrawals.reject', $wd->id) }}"
                        data-wd-id="{{ $wd->id }}"
                    >
                        <td>
                            <div>
                                <p class="mb-0 fw-semibold fs-13">{{ $wd->seller->business_name ?? '—' }}</p>
                                <small class="text-muted">{{ $wd->seller->email ?? '' }}</small>
                            </div>
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
                            {{-- Show OTP sub-status badge --}}
                            @if($wd->status === 'processing' && $wd->korapay_status === 'PENDING_AUTHORIZATION')
                                <br><span class="badge bg-warning text-dark mt-1" style="font-size:10px;">
                                    <i class="feather-lock" style="font-size:10px;"></i> Awaiting OTP
                                </span>
                            @endif
                            @if($wd->rejection_reason)
                                <p class="fs-11 text-muted mb-0 mt-1">{{ $wd->rejection_reason }}</p>
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
                                    @if($wd->status === 'processing' && $wd->korapay_status === 'PENDING_AUTHORIZATION')

                                        {{-- Primary action: complete the OTP --}}
                                        <button type="button"
                                                class="btn btn-sm btn-warning w-100"
                                                onclick="openOtpModal(this, '{{ number_format($wd->amount,2) }}', '{{ addslashes($wd->account_name) }}')">
                                            <i class="feather-shield me-1"></i> Enter OTP
                                        </button>
                                        {{-- Escape hatch: reset to pending if OTP is expired/wrong reference --}}
                                        <form action="{{ route('admin.withdrawals.change-status', $wd->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Reset to pending? Only do this if the Monnify transfer was NOT sent.')">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                                <i class="feather-rotate-ccw me-1"></i> Reset to Pending
                                            </button>
                                        </form>

                                    @elseif($wd->status === 'processing')

                                        {{-- Processing but not OTP — server error state --}}
                                        <form action="{{ route('admin.withdrawals.change-status', $wd->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Reset to pending? Only do this if the Monnify transfer was NOT sent.')">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                                <i class="feather-rotate-ccw me-1"></i> Reset to Pending
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.withdrawals.change-status', $wd->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Force approve? Only do this after confirming the transfer succeeded in Monnify dashboard.')">
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

{{-- ── OTP Authorization Modal ──────────────────────────────────────────── --}}
<div id="otpModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:999999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;max-width:460px;width:90%;margin:auto;box-shadow:0 10px 40px rgba(0,0,0,0.2);animation:modalFadeIn .3s ease;">
        <div style="padding:20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;border-radius:50%;background:#fff3cd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="feather-shield" style="color:#f59e0b;font-size:18px;"></i>
            </div>
            <div>
                <h5 style="margin:0;font-size:17px;font-weight:600;">Authorize Payout</h5>
                <p style="margin:0;font-size:12px;color:#6b7280;">Enter the OTP sent to our Monnify registered email</p>
            </div>
        </div>
        <form id="otpForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding:24px;">
                <p id="otpInfo" style="margin:0 0 20px 0;font-size:14px;color:#374151;background:#f9fafb;padding:12px;border-radius:8px;border-left:3px solid #f59e0b;"></p>

                <label style="display:block;margin-bottom:8px;font-weight:600;font-size:14px;">
                    OTP Code <span style="color:#dc2626;">*</span>
                </label>
                <input type="text"
                       id="otpInput"
                       name="otp"
                       inputmode="numeric"
                       autocomplete="one-time-code"
                       maxlength="10"
                       placeholder="e.g. 123456"
                       style="width:100%;padding:12px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:22px;letter-spacing:6px;text-align:center;font-weight:700;outline:none;"
                       required>
                <p style="margin:8px 0 0 0;font-size:12px;color:#6b7280;">
                    Check the email registered on our Monnify account. OTPs expire after a few minutes.
                </p>
            </div>
            <div style="padding:16px 24px;border-top:1px solid #e5e7eb;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeOtpModal()"
                        style="padding:9px 20px;background:#f3f4f6;border:none;border-radius:6px;cursor:pointer;font-size:14px;font-weight:500;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:9px 24px;background:#f59e0b;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px;font-weight:600;">
                    <i class="feather-check me-1"></i> Authorize Transfer
                </button>
            </div>
        </form>
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
#otpInput:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245,158,11,.2);
}
</style>

<script>
// ── Helpers ───────────────────────────────────────────────────────────────
function getRow(el) {
    return el.closest('tr');
}

// ── OTP modal ─────────────────────────────────────────────────────────────
function openOtpModal(btn, amount, accountName) {
    const row   = getRow(btn);
    const url   = row.dataset.otpUrl;
    const modal = document.getElementById('otpModal');

    document.getElementById('otpForm').action = url;
    document.getElementById('otpInfo').innerHTML =
        `Authorizing payout of <strong>₦${parseFloat(amount.replace(/,/g,'')).toLocaleString('en-NG', {minimumFractionDigits:2})}</strong> to <strong>${accountName}</strong>.`;
    document.getElementById('otpInput').value = '';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('otpInput').focus(), 100);
}

function closeOtpModal() {
    document.getElementById('otpModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('otpModal').addEventListener('click', function(e) {
    if (e.target === this) closeOtpModal();
});

// ── Auto-open OTP modal after approve redirect ────────────────────────────
@if(session('otp_required'))
    document.addEventListener('DOMContentLoaded', function() {
        const wdId  = '{{ session('otp_required') }}';
        const info  = @json(session('otp_info', ''));
        const row   = document.querySelector(`tr[data-wd-id="${wdId}"]`);
        const url   = row ? row.dataset.otpUrl : null;
        const modal = document.getElementById('otpModal');

        if (!url) return; // row not on this page

        document.getElementById('otpForm').action = url;
        document.getElementById('otpInfo').innerHTML = info || 'Enter the OTP from your Monnify email.';
        document.getElementById('otpInput').value = '';
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('otpInput').focus(), 150);
    });
@endif

// ── Reject modal ──────────────────────────────────────────────────────────
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