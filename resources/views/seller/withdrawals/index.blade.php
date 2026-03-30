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
                    亚
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Amount (NGN)</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Exchange Rate</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Local Amount</th>
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
                            @if($w->currency !== 'NGN')
                                <br>
                                <small class="text-muted">({{ $w->currency }})</small>
                            @endif
                        </td>
                        <td>
                            @if($w->exchange_rate && $w->currency !== 'NGN')
                                <span class="badge bg-info" style="font-size:11px;">
                                    1 NGN = {{ number_format($w->exchange_rate, 4) }} {{ $w->currency }}
                                </span>
                            @elseif($w->currency !== 'NGN' && !$w->exchange_rate)
                                <small class="text-muted">Rate pending</small>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </td>
                        <td>
                            @if($w->converted_amount && $w->currency !== 'NGN')
                                <span class="fw-semibold">
                                    {{ number_format($w->converted_amount, 2) }} {{ $w->currency }}
                                </span>
                                @if($w->exchange_rate)
                                    <br>
                                    <small class="text-success">
                                        @ {{ number_format($w->exchange_rate, 4) }}
                                    </small>
                                @endif
                            @elseif($w->currency !== 'NGN')
                                <small class="text-muted">Awaiting conversion</small>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </td>
                        <td>
                            <p class="mb-0 fw-semibold fs-13">{{ $w->bank_name }}</p>
                            <small class="text-muted">{{ $w->bank_country }}</small>
                        </td>
                        <td>
                            <p class="mb-0 fs-13">{{ $w->account_name }}</p>
                            <small class="text-muted">{{ $w->account_number }}</small>
                        </td>
                        <td>
                            <span class="badge orderer-badge text-muted">
                                {{ ucfirst($w->status) }}
                            </span>
                            @if($w->status === 'rejected' && $w->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">
                                    {{ Str::limit($w->rejection_reason, 40) }}
                                </p>
                            @endif
                        </td>
                        <td class="text-muted fs-12">{{ $w->created_at->format('M d, Y') }}</td>
                        <td class="text-muted fs-12">
                            {{ $w->processed_at?->format('M d, Y') ?? '—' }}
                            @if($w->processed_at && $w->exchange_rate)
                                <br>
                                <small class="text-success">Rate: {{ number_format($w->exchange_rate, 4) }}</small>
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
            <i class="feather-dollar-sign mb-3 d-block" style="font-size:40px;"></i>
            <p class="mb-0">No withdrawal requests yet.</p>
        </div>
        @endif
    </div>
</div>

{{-- Optional: Show total stats --}}
@if($withdrawals->count())
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Withdrawn (NGN)</p>
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
                    @php
                        $lastApproved = $withdrawals->where('status', 'approved')->first();
                    @endphp
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