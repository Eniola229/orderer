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
                        <h2 class="fw-bold mb-0 text-success">${{ number_format($wallet->balance, 2) }}</h2>
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
                        <h2 class="fw-bold mb-0 text-warning">${{ number_format($wallet->escrow_balance, 2) }}</h2>
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
                        <h2 class="fw-bold mb-0 text-primary">${{ number_format($wallet->ads_balance, 2) }}</h2>
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
                        <label class="form-label fw-bold">Amount (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
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
                        <label class="form-label fw-bold">Amount (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
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

    {{-- Transactions --}}
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
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Reference</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Balance After</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $txn)
                            @php $isCredit = in_array($txn->type, ['credit','escrow_release','referral_credit','escrow_refund']); @endphp
                            <tr>
                                <td>
                                    <code class="fs-12">{{ Str::limit($txn->reference, 20) }}</code>
                                </td>
                                <td>
                                    <span class="badge orderer-badge {{ $isCredit ? 'badge-approved' : 'badge-pending' }}">
                                        {{ str_replace('_', ' ', ucfirst($txn->type)) }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                    {{ $isCredit ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="fw-semibold">${{ number_format($txn->balance_after, 2) }}</td>
                                <td class="text-muted fs-12">
                                    {{ $txn->created_at->format('M d, Y H:i') }}
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

@endsection