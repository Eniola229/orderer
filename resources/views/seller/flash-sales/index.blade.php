@extends('layouts.seller')
@section('title', 'My Flash Sales')
@section('page_title', 'My Flash Sales')
@section('breadcrumb')
    <li class="breadcrumb-item active">Flash Sales</li>
@endsection
@section('page_actions')
    <a href="{{ route('seller.flash-sales.create') }}" class="btn btn-primary btn-sm">
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
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Original</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Sale Price</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Discount</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Period</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Sold / Limit</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Admin Approval</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($flashSales as $sale)
                    @php
                        $discount = round((($sale->original_price - $sale->sale_price) / $sale->original_price) * 100);
                        $active   = $sale->is_active && now()->between($sale->starts_at, $sale->ends_at);
                        $ended    = $sale->ends_at < now();
                        $isApproved = !is_null($sale->created_by);
                    @endphp
                    <tr>
                        <td>
                            <div>
                                <p class="mb-0 fw-semibold fs-13">
                                    {{ Str::limit($sale->product->name ?? '—', 35) }}
                                </p>
                                <small class="text-muted">{{ $sale->title }}</small>
                            </div>
                        </td>
                        <td>₦{{ number_format($sale->original_price, 2) }}</td>
                        <td class="fw-bold text-success">₦{{ number_format($sale->sale_price, 2) }}</td>
                        <td>
                            <span class="badge" style="background:#FADBD8;color:#E74C3C;">
                                -{{ $discount }}%
                            </span>
                        </td>
                        <td class="fs-12 text-muted">
                            {{ $sale->starts_at->format('M d') }}
                            –
                            {{ $sale->ends_at->format('M d, Y') }}
                        </td>
                        <td class="fs-13">
                            {{ $sale->quantity_sold }} / {{ $sale->quantity_limit ?? '∞' }}
                        </td>
                        <td>
                            @if($isApproved)
                                <span class="badge badge-success" style="background:#28a745;color:#fff;">
                                    <i class="feather-check-circle me-1"></i> Approved
                                </span>
                            @else
                                <span class="badge badge-warning" style="background:#ffc107;color:#212529;">
                                    <i class="feather-clock me-1"></i> Pending Approval
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($active)
                                <span class="badge orderer-badge badge-approved">Live</span>
                            @elseif($ended)
                                <span class="badge orderer-badge badge-completed">Ended</span>
                            @elseif(!$sale->is_active && $isApproved)
                                <span class="badge orderer-badge badge-pending">Paused</span>
                            @elseif(!$isApproved)
                                <span class="badge orderer-badge badge-draft">Awaiting Approval</span>
                            @else
                                <span class="badge orderer-badge badge-draft">Scheduled</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                {{-- Only show toggle button if:
                                     1. Sale hasn't ended
                                     2. Admin has approved (created_by is not null)
                                     3. Sale is approved and not ended
                                --}}
                                @if(!$ended && $isApproved)
                                <form action="{{ route('seller.flash-sales.toggle', $sale->id) }}"
                                      method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit"
                                            class="btn btn-sm {{ $sale->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            title="{{ $sale->is_active ? 'Pause this flash sale' : 'Activate this flash sale' }}">
                                        {{ $sale->is_active ? 'Pause' : 'Activate' }}
                                    </button>
                                </form>
                                @elseif(!$ended && !$isApproved)
                                <button class="btn btn-sm btn-secondary" disabled
                                        title="Waiting for admin approval">
                                    <i class="feather-lock me-1"></i> Activate
                                </button>
                                @endif

                                {{-- Delete button - always available --}}
                             <!--    <form action="{{ route('seller.flash-sales.destroy', $sale->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this flash sale?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form> -->
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
            <p>You haven't created any flash sales yet.</p>
            <a href="{{ route('seller.flash-sales.create') }}" class="btn btn-primary">
                Create First Flash Sale
            </a>
        </div>
        @endif
    </div>
</div>
@endsection