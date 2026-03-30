@extends('layouts.admin')
@section('title', 'Escrow')
@section('page_title', 'Escrow Holds')
@section('breadcrumb')
    <li class="breadcrumb-item active">Finance</li>
    <li class="breadcrumb-item active">Escrow</li>
@endsection

@section('content')

<div class="d-flex gap-2 mb-4">
    @foreach(['all'=>'All','held'=>'Held','released'=>'Released','refunded'=>'Refunded'] as $val=>$label)
    <a href="{{ route('admin.finance.escrow', ['status'=>$val]) }}"
       class="btn btn-sm {{ request('status','all')===$val ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="card">
    <div class="card-body p-0">
        @if($holds->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Order #</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Buyer</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Amount Held</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Held At</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Released At</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($holds as $hold)
                    <tr>
                        <td>
                            @if($hold->order)
                            <a href="{{ route('admin.orders.show', $hold->order->id) }}"
                               class="fw-semibold text-primary">
                                #{{ $hold->order->order_number }}
                            </a>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="fs-13">
                            {{ $hold->order->user->email ?? '—' }}
                        </td>
                        <td class="fw-bold">₦{{ number_format($hold->amount, 2) }}</td>
                        <td>
                            <span class="badge orderer-badge badge-{{ $hold->status }}">
                                {{ ucfirst($hold->status) }}
                            </span>
                        </td>
                        <td class="text-muted fs-12">{{ $hold->created_at?->format('M d, Y H:i') }}</td>
                        <td class="text-muted fs-12">
                            {{ $hold->released_at?->format('M d, Y H:i') ?? '—' }}
                        </td>
                        <td>
                            @if($hold->status === 'held' && auth('admin')->user()->canEditOrders())
                            <div class="d-flex gap-1">
                                <form action="{{ route('admin.orders.complete', $hold->order_id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Release this escrow to sellers?')">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        Release
                                    </button>
                                </form>
                                <form action="{{ route('admin.orders.refund', $hold->order_id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Refund to buyer?')">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Refund
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-muted fs-12">No action</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $holds->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-lock mb-2 d-block" style="font-size:40px;"></i>
            <p>No escrow holds found.</p>
        </div>
        @endif
    </div>
</div>

@endsection