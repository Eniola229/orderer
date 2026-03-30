@extends('layouts.admin')
@section('title', 'Buyer: ' . $user->first_name)
@section('page_title', $user->first_name . ' ' . $user->last_name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.buyers.index') }}">Buyers</a></li>
    <li class="breadcrumb-item active">{{ $user->email }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">

        {{-- Profile card --}}
        <div class="card mb-3">
            <div class="card-body text-center">
                @if($user->avatar)
                <img src="{{ $user->avatar }}"
                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #2ECC71;margin-bottom:12px;" alt="">
                @else
                <div style="width:80px;height:80px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;margin:0 auto 12px;">
                    {{ strtoupper(substr($user->first_name,0,1)) }}
                </div>
                @endif
                <h5 class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h5>
                <p class="text-muted fs-13">{{ $user->email }}</p>
                <p class="text-muted fs-13">{{ $user->phone ?? 'No phone' }}</p>
                <span class="badge orderer-badge {{ ($user->status ?? 'active') === 'active' ? 'badge-approved' : 'badge-rejected' }}">
                    {{ ucfirst($user->status ?? 'active') }}
                </span>
                <div class="border rounded p-3 text-start mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Referral code</small>
                        <code class="fs-12">{{ $user->referral_code }}</code>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Joined</small>
                        <strong>{{ $user->created_at->format('M d, Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wallet management -- finance admin only --}}
        @if(auth('admin')->user()->canManageFinance())
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Wallet Adjustment</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted d-block">Current Balance</small>
                    <h4 class="fw-bold text-success mb-0">
                        ₦{{ number_format($wallet->balance ?? 0, 2) }}
                    </h4>
                </div>
                <form action="{{ route('admin.buyers.wallet', $user->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-2">
                        <label class="form-label fw-bold fs-13">Adjustment Type</label>
                        <select name="type" class="form-select form-select-sm" required>
                            <option value="credit">Credit (add funds)</option>
                            <option value="debit">Debit (remove funds)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold fs-13">Amount (NGN)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₦</span>
                            <input type="number" name="amount" class="form-control"
                                   min="0.01" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold fs-13">Reason</label>
                        <input type="text" name="reason" class="form-control form-control-sm"
                               placeholder="e.g. Dispute resolution" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100"
                            onclick="return confirm('Adjust this buyer\'s wallet?')">
                        <i class="feather-dollar-sign me-1"></i> Apply Adjustment
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Suspend / reinstate --}}
        @if(auth('admin')->user()->canModerateSellers())
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Actions</h5>
            </div>
            <div class="card-body">
                @if(($user->status ?? 'active') === 'active')
                <form action="{{ route('admin.buyers.suspend', $user->id) }}"
                      method="POST"
                      onsubmit="return confirm('Suspend this buyer account?')">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="feather-user-x me-2"></i> Suspend Account
                    </button>
                </form>
                @else
                <form action="{{ route('admin.buyers.unsuspend', $user->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-success w-100">
                        <i class="feather-user-check me-2"></i> Reinstate Account
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

    </div>

    <div class="col-lg-8">

        {{-- Orders --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Orders ({{ $user->orders->count() }})</h5>
                <a href="{{ route('admin.orders.index', ['search' => $user->email]) }}"
                   class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($user->orders->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Order #</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Total</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->orders->take(8) as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="fw-semibold text-primary">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge orderer-badge badge-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <p>No orders yet.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Wallet transactions --}}
        @if(auth('admin')->user()->canManageFinance() && $wallet)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Wallet Transactions</h5>
            </div>
            <div class="card-body p-0">
                @php
                    $txns = \App\Models\WalletTransaction::where('wallet_id', $wallet->id)
                        ->latest()->take(10)->get();
                @endphp
                @if($txns->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Balance After</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Description</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($txns as $txn)
                            @php $isCredit = in_array($txn->type, ['credit','escrow_release','referral_credit','escrow_refund','withdrawal_refund']); @endphp
                            <tr>
                                <td>
                                    <span class="badge orderer-badge {{ $isCredit ? 'badge-approved' : 'badge-pending' }}">
                                        {{ str_replace('_',' ',ucfirst($txn->type)) }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                    {{ $isCredit ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="fw-semibold">₦{{ number_format($txn->balance_after, 2) }}</td>
                                <td class="text-muted fs-12">{{ Str::limit($txn->description, 40) }}</td>
                                <td class="text-muted fs-12">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">No transactions.</div>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
@endsection