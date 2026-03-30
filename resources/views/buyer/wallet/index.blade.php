@extends('layouts.buyer')
@section('title', 'My Wallet')
@section('page_title', 'My Wallet')
@section('breadcrumb')
    <li class="breadcrumb-item active">Wallet</li>
@endsection

@section('content')

<div class="row mb-4">
    <div class="col-md-6 mx-auto">
        <div class="card" style="background: linear-gradient(135deg, #27AE60, #2ECC71); color: #fff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p style="color:rgba(255,255,255,.8);font-size:13px;margin-bottom:6px;text-transform:uppercase;font-weight:600;">
                            Available Balance
                        </p>
                        <h1 style="color:#fff;font-size:42px;font-weight:800;margin-bottom:4px;">
                            ₦{{ number_format($wallet->balance, 2) }}
                        </h1>
                        <small style="color:rgba(255,255,255,.7);">NGN Wallet</small>
                    </div>
                    <i class="feather-credit-card" style="font-size:48px;color:rgba(255,255,255,.3);"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    {{-- Top up --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Up Wallet</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('buyer.wallet.topup') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (NGN)</label>
                        <div class="input-group">
                            <span class="input-group-text">₦</span>
                            <input type="number" name="amount" class="form-control"
                                   min="1" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>

                    {{-- Quick amount buttons --}}
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        @foreach([10, 25, 50, 100] as $amount)
                        <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                onclick="document.querySelector('[name=amount]').value='{{ $amount }}'">
                            ₦{{ $amount }}
                        </button>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="feather-credit-card me-2"></i> Pay via Korapay
                    </button>
                </form>
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
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Balance</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $txn)
                            @php $isCredit = in_array($txn->type, ['credit','escrow_refund','referral_credit']); @endphp
                            <tr>
                                <td><code class="fs-12">{{ Str::limit($txn->reference, 18) }}</code></td>
                                <td>
                                    <span class="badge orderer-badge {{ $isCredit ? 'badge-approved' : 'badge-pending' }}">
                                        {{ str_replace('_', ' ', ucfirst($txn->type)) }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                    {{ $isCredit ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="fw-semibold">₦{{ number_format($txn->balance_after, 2) }}</td>
                                <td class="text-muted fs-12">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $transactions->links() }}</div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="feather-activity mb-2 d-block" style="font-size:36px;"></i>
                    No transactions yet.
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection
