@extends('layouts.seller')
@section('title', 'Orders')
@section('page_title', 'My Orders')
@section('breadcrumb')
    <li class="breadcrumb-item active">Orders</li>
@endsection

@section('content')

{{-- Status filter tabs --}}
<div class="d-flex gap-2 mb-3 flex-wrap">
    @foreach(['all','pending','confirmed','shipped','delivered'] as $tab)
    <a href="{{ route('seller.orders.index', ['status' => $tab]) }}"
       class="btn btn-sm {{ request('status','all') === $tab ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ ucfirst($tab) }}
    </a>
    @endforeach
</div>
 
<div class="card">
    <div class="card-body p-0">
        @if($items->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Order #</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Item</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Buyer</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Qty</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Your Earnings</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>
                            <a href="{{ route('seller.orders.show', $item->order_id) }}"
                               class="fw-bold text-primary fs-13">
                                #{{ $item->order->order_number }}
                            </a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($item->item_image)
                                    <img src="{{ $item->item_image }}"
                                         style="width:46px;height:46px;object-fit:cover;border-radius:8px;border:1px solid #eee;"
                                         alt="">
                                @else
                                    <div style="width:46px;height:46px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="feather-image text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold fs-13">{{ Str::limit($item->item_name, 40) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="fs-13 text-muted">{{ $item->order->shipping_name }}</td>
                        <td class="fw-semibold">{{ $item->quantity }}</td>
                        <td><span class="fw-bold">₦{{ number_format($item->total_price, 2) }}</span></td>
                        <td><span class="fw-bold text-success">₦{{ number_format($item->seller_earnings, 2) }}</span></td>
                        <td>
                           <span style="
                                padding: 4px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                                display: inline-block;
                                @switch($item->status)
                                    @case('pending')
                                        background-color: #ffc107; color: #212529;
                                        @break
                                    @case('confirmed')
                                        background-color: #0d6efd; color: #ffffff;
                                        @break
                                    @case('processing')
                                        background-color: #6f42c1; color: #ffffff;
                                        @break
                                    @case('shipped')
                                        background-color: #0dcaf0; color: #212529;
                                        @break
                                    @case('delivered')
                                        background-color: #20c997; color: #212529;
                                        @break
                                    @case('completed')
                                        background-color: #28a745; color: #ffffff;
                                        @break
                                    @case('cancelled')
                                        background-color: #dc3545; color: #ffffff;
                                        @break
                                    @case('disputed')
                                        background-color: #fd7e14; color: #212529;
                                        @break
                                    @default
                                        background-color: #6c757d; color: #ffffff;
                                @endswitch
                            ">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="text-muted fs-12">{{ $item->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('seller.orders.show', $item->order_id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="feather-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $items->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-shopping-bag mb-3 d-block" style="font-size:40px;"></i>
            <p class="mb-3">No orders found.</p>
        </div>
        @endif
    </div>
</div>

@endsection