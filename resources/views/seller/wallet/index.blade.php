@extends('layouts.seller')
@section('title', 'Wallet')
@section('page_title', 'Wallet & Earnings')
@section('breadcrumb')
    <li class="breadcrumb-item active">Wallet</li>
@endsection

@section('content')

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Available Balance</p>
                        <h2 class="fw-bold mb-0 text-success">₦{{ number_format($wallet->balance, 2) }}</h2>
                        <small class="text-muted">Ready to withdraw</small>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#D5F5E3;color:#2ECC71;">
                        <i class="feather-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">In Escrow</p>
                        <h2 class="fw-bold mb-0 text-warning">₦{{ number_format($escrowBalance, 2) }}</h2>
                        <small class="text-muted">Pending delivery</small>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#FEF9E7;color:#F39C12;">
                        <i class="feather-lock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Ads Balance</p>
                        <h2 class="fw-bold mb-0 text-primary">₦{{ number_format($wallet->ads_balance, 2) }}</h2>
                        <small class="text-muted">For promotions</small>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#EBF5FB;color:#2980B9;">
                        <i class="feather-trending-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    {{-- Actions --}}
    <div class="col-lg-4">

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Up Wallet</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('seller.wallet.topup') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (NGN)</label>
                        <div class="input-group">
                            <span class="input-group-text">₦</span>
                            <input type="number" name="amount" class="form-control"
                                   min="1" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="feather-credit-card me-2"></i> Pay via Korapay
                    </button>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Up Ads Balance</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('seller.wallet.topup.ads') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (NGN)</label>
                        <div class="input-group">
                            <span class="input-group-text">₦</span>
                            <input type="number" name="amount" class="form-control"
                                   min="1" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="feather-trending-up me-2"></i> Fund Ads via Korapay
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Withdraw Funds</h5>
            </div>
            <div class="card-body">
                @if($wallet->balance >= 10)
                    <a href="{{ route('seller.withdrawals.create') }}"
                       class="btn btn-primary w-100">
                        <i class="feather-arrow-up-right me-2"></i> Request Withdrawal
                    </a>
                    <p class="text-muted fs-12 text-center mt-2 mb-0">Min withdrawal: $10.00</p>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="feather-info me-2"></i>
                        Minimum $10.00 required to withdraw.<br>
                        Current: <strong>${{ number_format($wallet->balance, 2) }}</strong>
                    </div>
                @endif
            </div>
        </div>

    </div>
    
    {{-- Transaction History --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaction History</h5>
            </div>
            <div class="card-body p-0">
                @if($transactions->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            亚
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Reference</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Balance After</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $txn)
                            @php 
                                $isCredit = in_array($txn->type, ['credit','escrow_release','referral_credit','escrow_refund']);
                                
                                $statusColors = [
                                    'completed' => '#28a745',
                                    'pending' => '#ffc107',
                                    'failed' => '#dc3545',
                                    'reversed' => '#6c757d',
                                ];
                                $statusColor = $statusColors[$txn->status] ?? '#6c757d';
                            @endphp
                            <tr class="transaction-row" data-id="{{ $txn->id }}" style="cursor: pointer;">
                                <td>
                                    <code class="fs-12">{{ Str::limit($txn->reference, 20) }}</code>
                                </td>
                                <td>
                                    <span class="badge orderer-badge {{ $isCredit ? 'badge-approved' : 'badge-pending' }}">
                                        {{ str_replace('_', ' ', ucfirst($txn->type)) }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                    {{ $isCredit ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="fw-semibold">₦{{ number_format($txn->balance_after, 2) }}</td>
                                <td>
                                    <span class="badge" style="
                                        background-color: {{ $statusColor }};
                                        color: {{ $txn->status === 'pending' ? '#212529' : '#ffffff' }};
                                        padding: 5px 10px;
                                        border-radius: 4px;
                                        font-size: 11px;
                                        font-weight: 600;
                                    ">
                                        {{ ucfirst($txn->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">
                                    {{ $txn->created_at->format('M d, Y H:i') }}
                                </td>
                                <td>
                                    <i class="feather-chevron-down" style="font-size: 16px; color: #6c757d;"></i>
                                </td>
                            </tr>
                            <tr class="transaction-details" id="details-{{ $txn->id }}" style="display: none;">
                                <td colspan="7" style="background-color: #f8f9fa;">
                                    <div style="padding: 15px;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>Transaction Details</strong></p>
                                                <table style="width: 100%; font-size: 13px;">
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Reference:</td>
                                                        <td style="padding: 5px 0;"><code>{{ $txn->reference }}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Type:</td>
                                                        <td style="padding: 5px 0;">{{ str_replace('_', ' ', ucfirst($txn->type)) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Amount:</td>
                                                        <td style="padding: 5px 0;" class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                                            {{ $isCredit ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Balance Before:</td>
                                                        <td style="padding: 5px 0;">₦{{ number_format($txn->balance_before, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Balance After:</td>
                                                        <td style="padding: 5px 0;">₦{{ number_format($txn->balance_after, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Status:</td>
                                                        <td style="padding: 5px 0;">
                                                            <span class="badge" style="
                                                                background-color: {{ $statusColor }};
                                                                color: {{ $txn->status === 'pending' ? '#212529' : '#ffffff' }};
                                                            ">
                                                                {{ ucfirst($txn->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>Additional Information</strong></p>
                                                <table style="width: 100%; font-size: 13px;">
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Description:</td>
                                                        <td style="padding: 5px 0;">{{ $txn->description ?? '—' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Related To:</td>
                                                        <td style="padding: 5px 0;">
                                                            @if($txn->related_type)
                                                                {{ str_replace('_', ' ', ucfirst($txn->related_type)) }}
                                                                @if($txn->related_id)
                                                                    (ID: {{ $txn->related_id }})
                                                                @endif
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Transaction ID:</td>
                                                        <td style="padding: 5px 0;"><code>{{ $txn->id }}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Created At:</td>
                                                        <td style="padding: 5px 0;">{{ $txn->created_at->format('M d, Y H:i:s') }}</td>
                                                    </tr>
                                                    @if($txn->updated_at != $txn->created_at)
                                                    <tr>
                                                        <td style="padding: 5px 0; color: #6c757d;">Last Updated:</td>
                                                        <td style="padding: 5px 0;">{{ $txn->updated_at->format('M d, Y H:i:s') }}</td>
                                                    </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $transactions->links() }}</div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="feather-activity mb-2 d-block" style="font-size:32px;"></i>
                    No transactions yet.
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.transaction-row');
        
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't toggle if clicking on links or buttons inside
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                    return;
                }
                
                const transactionId = this.getAttribute('data-id');
                const detailsRow = document.getElementById(`details-${transactionId}`);
                const chevron = this.querySelector('.feather-chevron-down');
                
                if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
                    // Close all other open details
                    document.querySelectorAll('.transaction-details').forEach(detail => {
                        detail.style.display = 'none';
                    });
                    document.querySelectorAll('.feather-chevron-down').forEach(icon => {
                        icon.style.transform = 'rotate(0deg)';
                    });
                    
                    // Open this one
                    detailsRow.style.display = 'table-row';
                    if (chevron) {
                        chevron.style.transform = 'rotate(180deg)';
                        chevron.style.transition = 'transform 0.3s ease';
                    }
                } else {
                    detailsRow.style.display = 'none';
                    if (chevron) {
                        chevron.style.transform = 'rotate(0deg)';
                    }
                }
            });
        });
    });
</script>

<style>
    .transaction-row:hover {
        background-color: #f8f9fa;
    }
    
    .feather-chevron-down {
        transition: transform 0.3s ease;
    }
    
    .transaction-details td {
        border-top: none !important;
    }
</style>

@endsection