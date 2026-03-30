@extends('layouts.admin')
@section('title', 'Disputes')
@section('page_title', 'Order Disputes')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active">Disputes</li>
@endsection

@section('content')

<div class="card">
    <div class="card-body p-0">
        @if($orders->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Order #</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Buyer</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Total</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}"
                               class="fw-semibold text-primary">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td class="fs-13">{{ $order->user->email ?? '—' }}</td>
                        <td class="fw-bold">₦{{ number_format($order->total, 2) }}</td>
                        <td>
                            <span class="badge orderer-badge badge-{{ $order->status }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="text-muted fs-12">{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                   class="btn btn-sm btn-outline-primary">View</a>
                                @if(auth('admin')->user()->canEditOrders())
                                <form action="{{ route('admin.orders.complete', $order->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Force complete and release escrow?')">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        Release
                                    </button>
                                </form>
                                <form action="{{ route('admin.orders.refund', $order->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Refund to buyer?')">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Refund
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $orders->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-check-circle mb-2 d-block" style="font-size:40px;color:#2ECC71;"></i>
            <p>No disputes. Everything is clean!</p>
        </div>
        @endif
    </div>
</div>

@endsection