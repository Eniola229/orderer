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
                    亚
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Amount (NGN)</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Exchange Rate</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Local Amount</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Bank</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Account</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Requested</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawals as $wd)
                    <tr>
                        <td>
                            <div>
                                <p class="mb-0 fw-semibold fs-13">
                                    {{ $wd->seller->business_name ?? '—' }}
                                </p>
                                <small class="text-muted">{{ $wd->seller->email ?? '' }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-success">₦{{ number_format($wd->amount, 2) }}</span>
                            @if($wd->currency !== 'NGN')
                                <br>
                                <small class="text-muted">({{ $wd->currency }})</small>
                            @endif
                        </td>
                        <td>
                            @if($wd->exchange_rate && $wd->currency !== 'NGN')
                                <span class="badge bg-info" style="font-size:11px;">
                                    1 NGN = {{ number_format($wd->exchange_rate, 4) }} {{ $wd->currency }}
                                </span>
                            @elseif($wd->currency !== 'NGN' && !$wd->exchange_rate && $wd->status === 'approved')
                                <small class="text-warning">Rate pending</small>
                            @elseif($wd->currency !== 'NGN' && !$wd->exchange_rate)
                                <small class="text-muted">—</small>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </td>
                        <td>
                            @if($wd->converted_amount && $wd->currency !== 'NGN')
                                <span class="fw-semibold text-primary">
                                    {{ number_format($wd->converted_amount, 2) }} {{ $wd->currency }}
                                </span>
                                @if($wd->exchange_rate)
                                    <br>
                                    <small class="text-success">
                                        @ {{ number_format($wd->exchange_rate, 4) }}
                                    </small>
                                @endif
                            @elseif($wd->currency !== 'NGN' && $wd->status === 'approved')
                                <small class="text-warning">Awaiting conversion</small>
                            @elseif($wd->currency !== 'NGN')
                                <small class="text-muted">—</small>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </td>
                        <td class="fs-13">
                            {{ $wd->bank_name }}
                            @if($wd->bank_country)
                                <br>
                                <small class="text-muted">{{ $wd->bank_country }}</small>
                            @endif
                        </td>
                        <td class="fs-13">
                            {{ $wd->account_name }}<br>
                            <code class="fs-12">{{ $wd->account_number }}</code>
                            @if($wd->bank_code)
                                <small class="text-muted">({{ $wd->bank_code }})</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge orderer-badge" style="
                                @if($wd->status === 'pending')
                                    background-color: #ffc107; color: #212529;
                                @elseif($wd->status === 'approved')
                                    background-color: #28a745; color: #ffffff;
                                @elseif($wd->status === 'rejected')
                                    background-color: #dc3545; color: #ffffff;
                                @elseif($wd->status === 'completed')
                                    background-color: #17a2b8; color: #ffffff;
                                @elseif($wd->status === 'failed')
                                    background-color: #343a40; color: #ffffff;
                                @elseif($wd->status === 'processing')
                                    background-color: #007bff; color: #ffffff;
                                @else
                                    background-color: #6c757d; color: #ffffff;
                                @endif
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($wd->status) }}
                            </span>
                            @if($wd->rejection_reason)
                            <p class="fs-11 text-muted mb-0 mt-1">{{ $wd->rejection_reason }}</p>
                            @endif
                        </td>
                        <td class="text-muted fs-12">
                            {{ $wd->created_at->format('M d, Y') }}
                            @if($wd->processed_at)
                                <br>
                                <small class="text-success">Processed: {{ $wd->processed_at->format('M d') }}</small>
                            @endif
                        </td>
                        <td>
                            @if(in_array($wd->status, ['processing', 'pending', 'approved']) && auth('admin')->user()->canManageFinance())
                            <div class="d-flex flex-column gap-1">
                                @if($wd->status === 'processing')
                                    <form action="{{ route('admin.withdrawals.change-status', $wd->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Change this withdrawal from processing to pending?')">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="pending">
                                        <button type="submit" class="btn btn-sm btn-warning w-100 mb-1">
                                            <i class="feather-clock me-1"></i> Change to Pending
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.withdrawals.change-status', $wd->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Change this withdrawal from processing to approved? This will mark it as approved without payout processing.')">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success w-100">
                                            <i class="feather-check me-1"></i> Force Approve
                                        </button>
                                    </form>
                                @elseif($wd->status === 'pending' && auth('admin')->user()->canManageFinance())
                                    <form action="{{ route('admin.withdrawals.approve', $wd->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Approve this withdrawal? Ensure bank transfer is initiated.')">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success w-100">
                                            <i class="feather-check me-1"></i> Approve
                                        </button>
                                    </form>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="openRejectModal('{{ $wd->id }}', '{{ $wd->amount }}', '{{ addslashes($wd->seller->business_name ?? 'Unknown') }}')">
                                        <i class="feather-x me-1"></i> Reject
                                    </button>
                                @elseif($wd->status === 'approved')
                                    <small class="text-success fw-semibold">
                                        <i class="feather-check-circle me-1"></i>
                                        Paid {{ $wd->processed_at?->format('M d') }} 
                                    </small>
                                @endif
                            </div>
                            @elseif($wd->status === 'rejected')
                            <small class="text-danger fw-semibold">
                                <i class="feather-x-circle me-1"></i>
                                Rejected
                            </small>
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

{{-- Custom Modal (no Bootstrap dependency) --}}
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Reject Withdrawal</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="modalAmountInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Reason</label>
                    <textarea name="reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Why are you rejecting this withdrawal?" required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Reject & Refund</button>
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
    function openRejectModal(id, amount, sellerName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const amountInfo = document.getElementById('modalAmountInfo');
        
        form.action = `/admin/withdrawals/${id}/reject`;
        amountInfo.innerHTML = `₦${parseFloat(amount).toFixed(2)} from <strong>${sellerName}</strong><br>Amount will be refunded to seller wallet.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close modal when clicking outside
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>

@endsection