@extends('layouts.buyer')
@section('title', 'My Orders')
@section('page_title', 'My Orders')
@section('breadcrumb')
    <li class="breadcrumb-item active">Orders</li>
@endsection

@section('content')

{{-- Status filter tabs --}}
<div class="d-flex gap-2 mb-3 flex-wrap">
    @foreach(['all','pending','confirmed','shipped','delivered','completed','cancelled'] as $tab)
    <a href="{{ route('buyer.orders', ['status' => $tab]) }}"
       class="btn btn-sm {{ request('status','all') === $tab ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ ucfirst($tab) }}
    </a>
    @endforeach
</div>

<div class="card">
    <div class="card-body p-0">
        @if($orders->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Order #</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Items</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Total</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Payment</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('buyer.orders.show', $order->id) }}"
                               class="fw-semibold text-primary">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td class="text-muted fs-13">{{ $order->items->count() }} item(s)</td>
                        <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                        <td>
                            <span class="badge orderer-badge badge-{{ $order->payment_status }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge orderer-badge badge-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="text-muted fs-12">{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('buyer.orders.show', $order->id) }}"
                               class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $orders->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-shopping-bag mb-2 d-block" style="font-size:40px;"></i>
            <p class="mb-3">No orders found.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary">Browse Shop</a>
        </div>
        @endif
    </div>
</div>

@endsection
