@extends('layouts.admin')
@section('title', 'Free Shipping Rules')
@section('page_title', 'Free Shipping Rules')
@section('breadcrumb')
    <li class="breadcrumb-item active">Free Shipping</li>
@endsection

@section('content')

{{-- ── Date Filter ─────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.free-shipping.index') }}"
              class="d-flex align-items-center gap-3 flex-wrap">
            <span class="fw-semibold fs-13 text-muted">Period:</span>
            <input type="date" name="date_from"
                   class="form-control form-control-sm"
                   value="{{ request('date_from', $dateFrom->format('Y-m-d')) }}"
                   style="width:145px;">
            <span class="text-muted">–</span>
            <input type="date" name="date_to"
                   class="form-control form-control-sm"
                   value="{{ request('date_to', $dateTo->format('Y-m-d')) }}"
                   style="width:145px;">
            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
            <a href="{{ route('admin.free-shipping.index') }}"
               class="btn btn-sm btn-outline-secondary">Reset</a>
            <span class="ms-auto text-muted fs-12">
                <strong>{{ $dateFrom->format('M d, Y') }}</strong>
                –
                <strong>{{ $dateTo->format('M d, Y') }}</strong>
            </span>
        </form>
    </div>
</div>

{{-- ── Stat Cards Row 1 — Rule Counts ──────────────────────────── --}}
<div class="row mb-4">

    <div class="col-xxl-2 col-md-4 col-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Rules</p>
                        <h3 class="fw-bold mb-0">{{ $totalRules }}</h3>
                    </div>
                    <div class="avatar-text avatar-md rounded"
                         style="background:#EBF5FB;color:#2980B9;flex-shrink:0;">
                        <i class="feather-list"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-2 col-md-4 col-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Active</p>
                        <h3 class="fw-bold mb-0 text-success">{{ $activeRules }}</h3>
                    </div>
                    <div class="avatar-text avatar-md rounded"
                         style="background:#D5F5E3;color:#2ECC71;flex-shrink:0;">
                        <i class="feather-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-2 col-md-4 col-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Scheduled</p>
                        <h3 class="fw-bold mb-0 text-warning">{{ $scheduledRules }}</h3>
                    </div>
                    <div class="avatar-text avatar-md rounded"
                         style="background:#FEF9E7;color:#F39C12;flex-shrink:0;">
                        <i class="feather-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-2 col-md-4 col-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Expired</p>
                        <h3 class="fw-bold mb-0 text-danger">{{ $expiredRules }}</h3>
                    </div>
                    <div class="avatar-text avatar-md rounded"
                         style="background:#FADBD8;color:#E74C3C;flex-shrink:0;">
                        <i class="feather-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-2 col-md-4 col-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Disabled</p>
                        <h3 class="fw-bold mb-0 text-secondary">{{ $disabledRules }}</h3>
                    </div>
                    <div class="avatar-text avatar-md rounded"
                         style="background:#F2F3F4;color:#717D7E;flex-shrink:0;">
                        <i class="feather-pause-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-2 col-md-4 col-6 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Orders Used</p>
                        <h3 class="fw-bold mb-0 text-primary">{{ number_format($totalOrdersDiscounted) }}</h3>
                    </div>
                    <div class="avatar-text avatar-md rounded"
                         style="background:#F4ECF7;color:#8E44AD;flex-shrink:0;">
                        <i class="feather-shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Stat Cards Row 2 — Financial + Scope ────────────────────── --}}
<div class="row mb-4">

    <div class="col-md-3 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Discount Given</p>
                <h3 class="fw-bold text-danger mb-1">₦{{ number_format($totalDiscountGiven, 2) }}</h3>
                <small class="text-muted">Shipping fees waived across all qualifying orders</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Avg Discount / Order</p>
                <h3 class="fw-bold text-warning mb-1">₦{{ number_format($avgDiscountPerOrder, 2) }}</h3>
                <small class="text-muted">Average shipping waived per discounted order</small>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Buyers Enrolled</p>
                <h3 class="fw-bold mb-1">{{ number_format($totalSpecificBuyers) }}</h3>
                <small class="text-muted">Unique buyers in specific-buyer rules</small>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Products Enrolled</p>
                <h3 class="fw-bold mb-1">{{ number_format($totalSpecificProducts) }}</h3>
                <small class="text-muted">Unique products in product-scoped rules</small>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Sellers Enrolled</p>
                <h3 class="fw-bold mb-1">{{ number_format($totalSpecificSellers) }}</h3>
                <small class="text-muted">Unique sellers in seller-scoped rules</small>
            </div>
        </div>
    </div>

</div>

{{-- ── Breakdown Charts Row ─────────────────────────────────────── --}}
<div class="row mb-4">

    {{-- Audience breakdown --}}
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-title mb-0">Rules by Audience</h6>
            </div>
            <div class="card-body">
                @php
                    $audienceLabels = [
                        'all_buyers'       => ['All Buyers',               'bg-success'],
                        'new_buyers'       => ['New Buyers',               'bg-info'],
                        'buyers_no_orders' => ['Buyers – No Orders',       'bg-warning'],
                        'specific_buyers'  => ['Specific Buyers',          'bg-primary'],
                    ];
                @endphp
                @forelse($audienceBreakdown as $type => $count)
                @php [$label, $color] = $audienceLabels[$type] ?? [ucfirst($type), 'bg-secondary']; @endphp
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $color }}">{{ $label }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="min-width:60%;">
                        <div class="progress flex-grow-1" style="height:8px;">
                            <div class="progress-bar {{ $color }}"
                                 style="width:{{ $totalRules > 0 ? round(($count / $totalRules) * 100) : 0 }}%">
                            </div>
                        </div>
                        <span class="fw-semibold fs-13" style="min-width:30px;">{{ $count }}</span>
                    </div>
                </div>
                @empty
                <p class="text-muted fs-13 text-center py-3">No data for this period.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Scope breakdown --}}
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-title mb-0">Rules by Product Scope</h6>
            </div>
            <div class="card-body">
                @php
                    $scopeLabels = [
                        'all'               => ['All Products',       'bg-success'],
                        'specific_products' => ['Specific Products',  'bg-primary'],
                        'specific_sellers'  => ['Specific Sellers',   'bg-warning'],
                    ];
                @endphp
                @forelse($scopeBreakdown as $type => $count)
                @php [$label, $color] = $scopeLabels[$type] ?? [ucfirst($type), 'bg-secondary']; @endphp
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $color }}">{{ $label }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="min-width:60%;">
                        <div class="progress flex-grow-1" style="height:8px;">
                            <div class="progress-bar {{ $color }}"
                                 style="width:{{ $totalRules > 0 ? round(($count / $totalRules) * 100) : 0 }}%">
                            </div>
                        </div>
                        <span class="fw-semibold fs-13" style="min-width:30px;">{{ $count }}</span>
                    </div>
                </div>
                @empty
                <p class="text-muted fs-13 text-center py-3">No data for this period.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ── Rules Table ──────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">
            Rules
            <span class="text-muted fw-normal fs-13 ms-1">({{ $rules->total() }} in period)</span>
        </h5>
        <a href="{{ route('admin.free-shipping.create') }}" class="btn btn-primary btn-sm">
            <i class="feather-plus me-1"></i> New Rule
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Name</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Applies To</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Scope</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Min Order</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Max Discount</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Period</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Orders Used</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Discount Given</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                    @php
                        $ruleOrders        = \App\Models\Order::where('free_shipping_rule_id', $rule->id)
                                                ->where('payment_status', 'paid');
                        $ruleOrderCount    = (clone $ruleOrders)->count();
                        $ruleDiscountTotal = (clone $ruleOrders)->sum('free_shipping_discount');

                        $statusColors = [
                            'Active'    => 'bg-success',
                            'Scheduled' => 'bg-warning text-dark',
                            'Expired'   => 'bg-danger',
                            'Disabled'  => 'bg-secondary',
                        ];
                    @endphp
                    <tr>
                        <td>
                            <p class="fw-semibold mb-0 fs-13">{{ $rule->name }}</p>
                            @if($rule->description)
                            <small class="text-muted">{{ Str::limit($rule->description, 45) }}</small>
                            @endif
                            <small class="text-muted d-block">by {{ $rule->creator->full_name ?? '—' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info text-white">{{ $rule->applies_to_label }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $rule->product_scope_label }}</span>
                        </td>
                        <td class="fs-13">
                            {{ $rule->minimum_order_amount
                                ? '₦' . number_format($rule->minimum_order_amount, 2)
                                : '—' }}
                        </td>
                        <td class="fs-13">
                            {{ $rule->max_discount_amount
                                ? '₦' . number_format($rule->max_discount_amount, 2)
                                : 'Full' }}
                        </td>
                        <td class="fs-12 text-muted">
                            @if($rule->starts_at || $rule->ends_at)
                            {{ $rule->starts_at?->format('M d, Y') ?? '—' }}
                            @if($rule->ends_at)
                            <br>→ {{ $rule->ends_at->format('M d, Y') }}
                            @endif
                            @else
                            <span class="text-muted">No limit</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold text-primary">{{ number_format($ruleOrderCount) }}</span>
                        </td>
                        <td>
                            <span class="fw-semibold text-danger">
                                ₦{{ number_format($ruleDiscountTotal, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $statusColors[$rule->status_label] ?? 'bg-secondary' }}">
                                {{ $rule->status_label }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.free-shipping.edit', $rule) }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="feather-edit-2"></i>
                                </a>
                                <form action="{{ route('admin.free-shipping.toggle', $rule) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm {{ $rule->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            title="{{ $rule->is_active ? 'Disable' : 'Enable' }}">
                                        <i class="feather-{{ $rule->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.free-shipping.destroy', $rule) }}" method="POST"
                                      onsubmit="return confirm('Delete this rule?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="feather-truck" style="font-size:32px;opacity:0.3;display:block;margin-bottom:8px;"></i>
                            No free shipping rules in this period.
                            <a href="{{ route('admin.free-shipping.create') }}" class="d-block mt-2">Create one</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($rules->hasPages())
    <div class="card-footer">
        {{ $rules->links() }}
    </div>
    @endif
</div>

@endsection