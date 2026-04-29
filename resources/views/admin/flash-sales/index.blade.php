@extends('layouts.admin')
@section('title', 'Flash Sales')
@section('page_title', 'Flash Sales')
@section('breadcrumb')
    <li class="breadcrumb-item active">Flash Sales</li>
@endsection
@section('page_actions')
    <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> New Flash Sale
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($flashSales->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Product</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Original</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Sale Price</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Discount</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Period</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Sold / Limit</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Live Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($flashSales as $sale)
                    @php
                        $discount = round((($sale->original_price - $sale->sale_price) / $sale->original_price) * 100);
                        $active   = $sale->is_active && now()->between($sale->starts_at, $sale->ends_at);
                    @endphp
                    <tr>
                        <td>
                            <div>
                                <p class="mb-0 fw-semibold fs-13">
                                    <a href="{{ route('admin.flash-sales.show', $sale) }}" class="text-decoration-none">
                                        {{ Str::limit($sale->product->name ?? '—', 35) }}
                                    </a>
                                </p>
                                <small class="text-muted">{{ Str::limit($sale->title, 30) }}</small>
                            </div>
                        </td>
                        <td class="fs-13 text-muted">
                            {{ $sale->product->seller->business_name ?? '—' }}
                        </td>
                        <td class="fs-13">₦{{ number_format($sale->original_price, 2) }}</td>
                        <td class="fw-bold text-success">₦{{ number_format($sale->sale_price, 2) }}</td>
                        <td>
                            <span class="badge" style="background:#FADBD8;color:#E74C3C;">
                                -{{ $discount }}%
                            </span>
                        </td>
                        <td class="fs-12 text-muted">
                            {{ $sale->starts_at->format('M d, g:i A') }}<br>
                            <small>to</small><br>
                            {{ $sale->ends_at->format('M d, g:i A') }}
                        </td>
                        <td class="fs-13">
                            {{ $sale->quantity_sold }}
                            /
                            {{ $sale->quantity_limit ?? '∞' }}
                        </td>
                        <td>
                            @if($active)
                                <span class="badge badge-success" style="background:#28a745;color:#fff;">Live</span>
                            @elseif($sale->ends_at < now())
                                <span class="badge badge-secondary" style="background:#6c757d;color:#fff;">Ended</span>
                            @elseif(!$sale->is_active)
                                <span class="badge badge-warning" style="background:#ffc107;color:#212529;">Paused</span>
                            @else
                                <span class="badge badge-info" style="background:#17a2b8;color:#fff;">Scheduled</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.flash-sales.show', $sale) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="feather-eye"></i>
                                </a>
                                
                                @if($active || $sale->is_active)
                                <form action="{{ route('admin.flash-sales.toggle', $sale->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button type="submit"
                                            class="btn btn-sm {{ $sale->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                        {{ $sale->is_active ? 'Pause' : 'Activate' }}
                                    </button>
                                </form>
                                @endif
                                
                                <form action="{{ route('admin.flash-sales.destroy', $sale->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this flash sale permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $flashSales->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-zap mb-2 d-block" style="font-size:40px;"></i>
            <p>No flash sales yet.</p>
            <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-primary">
                Create First Flash Sale
            </a>
        </div>
        @endif
    </div>
</div>
@endsection