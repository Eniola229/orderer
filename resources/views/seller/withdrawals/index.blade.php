@extends('layouts.seller')
@section('title', 'Withdrawals')
@section('page_title', 'Withdrawal Requests')
@section('breadcrumb')
    <li class="breadcrumb-item active">Withdrawals</li>
@endsection
@section('page_actions')
    <a href="{{ route('seller.withdrawals.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> New Withdrawal
    </a>
@endsection

@section('content')

{{-- Status filter tabs --}}
<div class="d-flex gap-2 mb-3 flex-wrap">
    @foreach(['all','pending','approved','rejected','processed'] as $tab)
    <a href="{{ route('seller.withdrawals.index', ['status' => $tab]) }}"
       class="btn btn-sm {{ request('status','all') === $tab ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ ucfirst($tab) }}
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
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Amount (NGN)</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Bank</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Account</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Requested</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Processed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawals as $w)
                    <tr>
                        <td>
                            <span class="fw-bold text-success" style="font-size:16px;">
                                ₦{{ number_format($w->amount, 2) }}
                            </span>
                            @if($w->payout_fee)
                                <br><small class="text-muted">Fee: ₦{{ number_format($w->payout_fee, 2) }}</small>
                            @endif
                        </td>
                        <td>
                            <p class="mb-0 fw-semibold fs-13">{{ $w->bank_name }}</p>
                        </td>
                        <td>
                            <p class="mb-0 fs-13">{{ $w->account_name }}</p>
                            <small class="text-muted">{{ $w->account_number }}</small>
                        </td>
                        <td>
                            {{-- Main status badge --}}
                            @php
                                $badgeStyle = match($w->status) {
                                    'pending'    => 'background:#ffc107;color:#212529;',
                                    'approved'   => 'background:#28a745;color:#fff;',
                                    'rejected'   => 'background:#dc3545;color:#fff;',
                                    'failed'     => 'background:#343a40;color:#fff;',
                                    'processing' => 'background:#007bff;color:#fff;',
                                    default      => 'background:#6c757d;color:#fff;',
                                };
                            @endphp
                            <span style="{{ $badgeStyle }} padding:4px 10px;border-radius:4px;font-size:12px;font-weight:600;">
                                {{ ucfirst($w->status) }}
                            </span>

                            {{-- Transfer progress label (no Korapay branding) --}}
                            @if($w->korapay_status)
                                @php
                                    [$transferLabel, $transferStyle, $transferIcon] = match($w->korapay_status) {
                                        'processing' => [
                                            'Transfer in progress',
                                            'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;',
                                            'feather-clock',
                                        ],
                                        'success' => [
                                            'Sent to your bank',
                                            'background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;',
                                            'feather-check-circle',
                                        ],
                                        'failed' => [
                                            'Transfer failed — refunded',
                                            'background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;',
                                            'feather-alert-circle',
                                        ],
                                        default => [
                                            'Verifying transfer',
                                            'background:#f9fafb;color:#374151;border:1px solid #e5e7eb;',
                                            'feather-loader',
                                        ],
                                    };
                                @endphp
                                <br>
                                <span style="display:inline-flex;align-items:center;gap:4px;margin-top:5px;font-size:11px;font-weight:600;padding:3px 8px;border-radius:4px;{{ $transferStyle }}">
                                    <i class="{{ $transferIcon }}" style="font-size:11px;"></i>
                                    {{ $transferLabel }}
                                </span>
                            @endif

                            @if($w->status === 'rejected' && $w->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">
                                    {{ Str::limit($w->rejection_reason, 60) }}
                                </p>
                            @endif
                        </td>
                        <td class="text-muted fs-12">{{ $w->created_at->format('M d, Y') }}</td>
                        <td class="text-muted fs-12">
                            {{ $w->processed_at?->format('M d, Y') ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $withdrawals->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-dollar-sign mb-3 d-block" style="font-size:40px;"></i>
            <p class="mb-0">No withdrawal requests yet.</p>
        </div>
        @endif
    </div>
</div>

@if($withdrawals->count())
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Withdrawn</p>
                <h3 class="fw-bold mb-0 text-success">
                    ₦{{ number_format($withdrawals->where('status', 'approved')->sum('amount'), 2) }}
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Pending Amount</p>
                <h3 class="fw-bold mb-0 text-warning">
                    ₦{{ number_format($withdrawals->where('status', 'pending')->sum('amount'), 2) }}
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Last Withdrawal</p>
                <h6 class="fw-bold mb-0">
                    @php $lastApproved = $withdrawals->where('status', 'approved')->first(); @endphp
                    @if($lastApproved)
                        {{ $lastApproved->processed_at?->format('M d, Y') ?? $lastApproved->created_at->format('M d, Y') }}
                    @else
                        No withdrawals yet
                    @endif
                </h6>
            </div>
        </div>
    </div>
</div>
@endif

@endsection