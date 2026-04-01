@extends('layouts.admin')
@section('title', 'Orders')
@section('page_title', 'Order Management')
@section('breadcrumb')
    <li class="breadcrumb-item active">Orders</li>
@endsection

@section('content')

<style>
    .filter-card {
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

{{-- Stats Cards --}}
<div class="row mb-4">
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Orders</p>
                <h2 class="fw-bold mb-0 text-primary">{{ number_format($stats['total']) }}</h2>
                @if(request()->anyFilled(['status', 'payment_status', 'date_from', 'date_to', 'search']))
                    <small class="text-muted">Filtered results</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Pending</p>
                <h2 class="fw-bold mb-0 text-warning">{{ number_format($stats['pending']) }}</h2>
                @if(request()->anyFilled(['status', 'payment_status', 'date_from', 'date_to', 'search']))
                    <small class="text-muted">Filtered results</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Completed</p>
                <h2 class="fw-bold mb-0 text-success">{{ number_format($stats['completed']) }}</h2>
                @if(request()->anyFilled(['status', 'payment_status', 'date_from', 'date_to', 'search']))
                    <small class="text-muted">Filtered results</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Revenue</p>
                <h2 class="fw-bold mb-0 text-success">₦{{ number_format($stats['revenue'], 2) }}</h2>
                @if(request()->anyFilled(['status', 'payment_status', 'date_from', 'date_to', 'search']))
                    <small class="text-muted">Filtered results</small>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Advanced Filters --}}
<div class="card filter-card">
    <div class="card-body">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Order Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="disputed" {{ request('status') == 'disputed' ? 'selected' : '' }}>Disputed</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Payment Status</label>
                <select name="payment_status" class="form-select form-select-sm">
                    <option value="">All Payment Status</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" 
                       value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" 
                       value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-12">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Order # or Email..." value="{{ request('search') }}">
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="feather-x"></i>
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="feather-filter"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($orders->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                     践
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Order #</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Customer</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Items</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Payment</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                      </thead>
                <tbody>
                    @foreach($orders as $order)
                      <tr>
                        <td>
                            <code class="fw-bold">{{ $order->order_number }}</code>
                          </td>
                        <td>
                            <div>
                                <p class="mb-0 fw-semibold fs-13">{{ $order->user->full_name ?? '—' }}</p>
                                <small class="text-muted">{{ $order->user->email ?? '—' }}</small>
                            </div>
                          </td>
                        <td class="fw-bold text-success">
                            ₦{{ number_format($order->total, 2) }}
                          </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $order->items->count() }} items
                            </span>
                          </td>
                        <td>
                            <span class="badge orderer-badge" style="
                                @if($order->status === 'pending')
                                    background-color: #ffc107; color: #212529;
                                @elseif($order->status === 'processing')
                                    background-color: #17a2b8; color: #ffffff;
                                @elseif($order->status === 'completed')
                                    background-color: #28a745; color: #ffffff;
                                @elseif($order->status === 'cancelled')
                                    background-color: #dc3545; color: #ffffff;
                                @elseif($order->status === 'refunded')
                                    background-color: #6c757d; color: #ffffff;
                                @else
                                    background-color: #6c757d; color: #ffffff;
                                @endif
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($order->status) }}
                            </span>
                          </td>
                        <td>
                            <span class="badge orderer-badge" style="
                                @if($order->payment_status === 'paid')
                                    background-color: #28a745; color: #ffffff;
                                @elseif($order->payment_status === 'pending')
                                    background-color: #ffc107; color: #212529;
                                @elseif($order->payment_status === 'failed')
                                    background-color: #dc3545; color: #ffffff;
                                @else
                                    background-color: #6c757d; color: #ffffff;
                                @endif
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 11px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                          </td>
                        <td class="text-muted fs-12">
                            {{ $order->created_at->format('M d, Y H:i') }}
                          </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="feather-eye"></i>
                                </a>
                                @if(auth('admin')->user()->canEditOrders() && $order->status !== 'completed')
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success"
                                        onclick="openUpdateStatusModal('{{ $order->id }}', '{{ $order->order_number }}')">
                                    <i class="feather-edit"></i>
                                </button>
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
            <i class="feather-shopping-bag mb-2 d-block" style="font-size:40px;"></i>
            <p>No orders found.</p>
        </div>
        @endif
    </div>
</div>

{{-- Update Status Modal --}}
<div id="updateStatusModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Update Order Status</h5>
        </div>
        <form id="updateStatusForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="orderInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Status</label>
                    <select name="status" id="orderStatus" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Note (Optional)</label>
                    <textarea name="note" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Add a note about this status change..."></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeUpdateStatusModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Update Status</button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    function openUpdateStatusModal(orderId, orderNumber) {
        const modal = document.getElementById('updateStatusModal');
        const form = document.getElementById('updateStatusForm');
        const orderInfo = document.getElementById('orderInfo');
        
        form.action = `/admin/orders/${orderId}/status`;
        orderInfo.innerHTML = `<strong>Order #${orderNumber}</strong><br>Change the status of this order.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeUpdateStatusModal() {
        const modal = document.getElementById('updateStatusModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close modal when clicking outside
    document.getElementById('updateStatusModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeUpdateStatusModal();
        }
    });
</script>

@endsection