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
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
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
                            <span class="fw-bold text-success" style="font-size:18px;">
                                ${{ number_format($w->amount, 2) }}
                            </span>
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
                            <span class="badge orderer-badge badge-{{ $w->status }}">
                                {{ ucfirst($w->status) }}
                            </span>
                            @if($w->status === 'rejected' && $w->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">
                                    {{ Str::limit($w->rejection_reason, 40) }}
                                </p>
                            @endif
                        </td>
                        <td class="text-muted fs-12">{{ $w->created_at->format('M d, Y') }}</td>
                        <td class="text-muted fs-12">{{ $w->processed_at?->format('M d, Y') ?? '—' }}</td>
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

@endsection