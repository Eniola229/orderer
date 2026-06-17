@extends('layouts.marketer')
@section('title', 'Dashboard')
@section('page_title', 'Marketing Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Overview</li>
@endsection

@section('content')

<style>
/* ── Tab fix ── */
.tab-content > .tab-pane {
    display: none;
}
.tab-content > .tab-pane.active {
    display: block;
}

/* ── Table responsive fix for overlapping ── */
.table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

.table-responsive .table {
    margin-bottom: 0;
    min-width: 650px;
}

/* Ensure card doesn't overflow */
.card {
    overflow: hidden;
}

/* Fix for any overlapping content */
.card .card-body {
    overflow-x: visible;
}

/* Ensure proper spacing */
.tab-content {
    position: relative;
    overflow: hidden;
}

.tab-pane {
    overflow-x: auto;
}
</style>

{{-- ── Marketing code alert strip ──────────────────────────────────────────── --}}
@if(!$marketer->marketing_code)
<div class="row mb-3">
    <div class="col-12">
        <div class="alert mb-0 d-flex align-items-center gap-3"
             style="background:#FEF9E7;border:1px solid #F9CA24;border-radius:10px;">
            <i class="feather-zap" style="font-size:20px;color:#B7950B;flex-shrink:0;"></i>
            <div class="flex-grow-1">
                <strong style="color:#B7950B;">You don't have a marketing code yet</strong>
                <p class="mb-0 text-muted" style="font-size:12px;">Generate your code so sellers and buyers can start using it.</p>
            </div>
            <form method="POST" action="{{ route('marketer.generate-code') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning fw-semibold">
                    <i class="feather-zap me-1"></i> Generate Code
                </button>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ── Date filter bar ─────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('marketer.dashboard') }}"
              class="d-flex align-items-center gap-3 flex-wrap">

            <span class="fw-semibold fs-13 text-muted me-1">Period:</span>

            <div class="btn-group">
                @foreach(['7' => '7d', '14' => '14d', '30' => '30d', '90' => '90d'] as $val => $label)
                <button type="submit" name="range" value="{{ $val }}"
                        class="btn btn-sm {{ ($range ?? '30') == $val ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $label }}
                </button>
                @endforeach
                <button type="button"
                        class="btn btn-sm {{ ($range ?? '30') == 'custom' ? 'btn-primary' : 'btn-outline-secondary' }}"
                        onclick="toggleCustom()">
                    Custom
                </button>
            </div>

            <div id="customRange"
                 class="{{ ($range ?? '30') == 'custom' ? 'd-flex' : 'd-none' }} gap-2 align-items-center">
                <input type="hidden" name="range" id="rangeHidden" value="custom">
                <input type="date" name="from" class="form-control form-control-sm"
                       value="{{ $from ? $from->format('Y-m-d') : '' }}" style="width:145px;">
                <span class="text-muted">–</span>
                <input type="date" name="to" class="form-control form-control-sm"
                       value="{{ $to ? $to->format('Y-m-d') : '' }}" style="width:145px;">
                <button type="submit" class="btn btn-sm btn-primary">Apply</button>
            </div>

            @if($from || $to)
            <a href="{{ route('marketer.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="feather-x me-1"></i> Clear
            </a>
            @endif

            @if($from && $to)
            <span class="ms-auto text-muted fs-12">
                <strong>{{ $from->format('M d, Y') }}</strong> – <strong>{{ $to->format('M d, Y') }}</strong>
            </span>
            @endif
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     SELLER STAT CARDS
════════════════════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center gap-2 mb-3">
    <div class="avatar-text rounded" style="width:28px;height:28px;background:#EBF5FB;color:#2980B9;font-size:14px;">
        <i class="feather-briefcase"></i>
    </div>
    <span class="text-muted fs-12 fw-semibold text-uppercase" style="letter-spacing:.5px;">Sellers</span>
</div>

<div class="row mb-2">
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Referred</p>
                        <h2 class="fw-bold mb-0">{{ number_format($sellerStats['total']) }}</h2>
                        @if($from || $to)<small class="text-muted">filtered period</small>@endif
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#D5F5E3;color:#2ECC71;flex-shrink:0;">
                        <i class="feather-briefcase"></i>
                    </div>
                </div>
                <p class="text-muted fs-12 mb-0">
                    <span class="text-success fw-semibold">{{ $sellerStats['this_month'] }}</span> joined this month
                </p>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Approved Sellers</p>
                        <h2 class="fw-bold mb-0 text-success">{{ number_format($sellerStats['approved']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#D5F5E3;color:#27AE60;flex-shrink:0;">
                        <i class="feather-check-circle"></i>
                    </div>
                </div>
                @if($sellerStats['total'] > 0)
                <p class="text-muted fs-12 mb-0">
                    <span class="text-success fw-semibold">
                        {{ round(($sellerStats['approved'] / $sellerStats['total']) * 100, 1) }}%
                    </span> approval rate
                </p>
                @else
                <p class="text-muted fs-12 mb-0">No sellers yet</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Pending Approval</p>
                        <h2 class="fw-bold mb-0 text-warning">{{ number_format($sellerStats['pending']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#FEF9E7;color:#F39C12;flex-shrink:0;">
                        <i class="feather-clock"></i>
                    </div>
                </div>
                <p class="text-muted fs-12 mb-0">Awaiting admin review</p>
            </div>
        </div>
    </div>

    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Joined This Month</p>
                        <h2 class="fw-bold mb-0 text-primary">{{ number_format($sellerStats['this_month']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#EBF5FB;color:#2980B9;flex-shrink:0;">
                        <i class="feather-calendar"></i>
                    </div>
                </div>
                <p class="text-muted fs-12 mb-0">{{ now()->format('F Y') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     BUYER STAT CARDS
════════════════════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center gap-2 mb-3">
    <div class="avatar-text rounded" style="width:28px;height:28px;background:#EDE7F6;color:#7B1FA2;font-size:14px;">
        <i class="feather-users"></i>
    </div>
    <span class="text-muted fs-12 fw-semibold text-uppercase" style="letter-spacing:.5px;">Buyers</span>
</div>

<div class="row mb-4">
    <div class="col-xxl col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Buyers</p>
                        <h2 class="fw-bold mb-0" style="color:#7B1FA2;">{{ number_format($buyerStats['total']) }}</h2>
                        @if($from || $to)<small class="text-muted">filtered period</small>@endif
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#EDE7F6;color:#7B1FA2;flex-shrink:0;">
                        <i class="feather-users"></i>
                    </div>
                </div>
                <p class="text-muted fs-12 mb-0">
                    <span class="fw-semibold" style="color:#7B1FA2;">{{ $buyerStats['this_month'] }}</span> this month
                </p>
            </div>
        </div>
    </div>

    <div class="col-xxl col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Placed Orders</p>
                        <h2 class="fw-bold mb-0 text-success">{{ number_format($buyerStats['with_orders']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#D5F5E3;color:#27AE60;flex-shrink:0;">
                        <i class="feather-shopping-cart"></i>
                    </div>
                </div>
                @if($buyerStats['total'] > 0)
                <p class="text-muted fs-12 mb-0">
                    <span class="text-success fw-semibold">
                        {{ round(($buyerStats['with_orders'] / $buyerStats['total']) * 100, 1) }}%
                    </span> conversion
                </p>
                @else
                <p class="text-muted fs-12 mb-0">buyers who ordered</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xxl col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Booked Delivery</p>
                        <h2 class="fw-bold mb-0 text-primary">{{ number_format($buyerStats['with_bookings']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#EBF5FB;color:#2980B9;flex-shrink:0;">
                        <i class="feather-truck"></i>
                    </div>
                </div>
                @if($buyerStats['total'] > 0)
                <p class="text-muted fs-12 mb-0">
                    <span class="text-primary fw-semibold">
                        {{ round(($buyerStats['with_bookings'] / $buyerStats['total']) * 100, 1) }}%
                    </span> of buyers
                </p>
                @else
                <p class="text-muted fs-12 mb-0">buyers who booked</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xxl col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Orders</p>
                        <h2 class="fw-bold mb-0" style="color:#E67E22;">{{ number_format($buyerStats['total_orders']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#FEF5E7;color:#E67E22;flex-shrink:0;">
                        <i class="feather-package"></i>
                    </div>
                </div>
                <p class="text-muted fs-12 mb-0">orders by referred buyers</p>
            </div>
        </div>
    </div>

    <div class="col-xxl col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Bookings</p>
                        <h2 class="fw-bold mb-0" style="color:#E74C3C;">{{ number_format($buyerStats['total_bookings']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#FDEDEC;color:#E74C3C;flex-shrink:0;">
                        <i class="feather-map-pin"></i>
                    </div>
                </div>
                <p class="text-muted fs-12 mb-0">deliveries booked</p>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     MAIN BODY — code card + summary LEFT | tabbed tables RIGHT
════════════════════════════════════════════════════════════════════ --}}
<div class="row">

    {{-- ── Left column ─────────────────────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Marketing Code --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Your Marketing Code</h5>
            </div>
            <div class="card-body text-center py-4">
                @if($marketer->marketing_code)
                <p class="text-muted fs-13 mb-3">Share with sellers <em>and</em> buyers — they enter it at registration.</p>
                <div id="mktCode"
                     style="background:#1a1f2e;color:#2ECC71;font-family:'Courier New',monospace;
                            font-size:20px;font-weight:700;letter-spacing:3px;
                            padding:16px 24px;border-radius:10px;display:inline-block;
                            cursor:pointer;user-select:all;"
                     onclick="copyCode(this)" title="Click to copy">
                    {{ $marketer->marketing_code }}
                </div>
                <p class="text-muted fs-11 mt-2 mb-3">Click code to copy</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-sm btn-outline-secondary"
                            onclick="copyCode(document.getElementById('mktCode'))">
                        <i class="feather-copy me-1"></i> Copy
                    </button>
                    <form method="POST" action="{{ route('marketer.regenerate-code') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning"
                                onclick="return confirm('Generate a new code? Your old code will stop working immediately.')">
                            <i class="feather-refresh-cw me-1"></i> Regenerate
                        </button>
                    </form>
                </div>
                @else
                <p class="text-muted fs-13 mb-4">You don't have a marketing code yet.</p>
                <form method="POST" action="{{ route('marketer.generate-code') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-zap me-2"></i> Generate My Code
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Period Summary --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Period Summary</h5>
            </div>
            <div class="card-body">
                <p class="text-muted fs-11 text-uppercase fw-semibold mb-2" style="letter-spacing:.5px;">Sellers</p>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Total Referred</span>
                    <strong>{{ $sellerStats['total'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Approved</span>
                    <strong class="text-success">{{ $sellerStats['approved'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Pending</span>
                    <strong class="text-warning">{{ $sellerStats['pending'] }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted fs-13">This Month</span>
                    <strong class="text-primary">{{ $sellerStats['this_month'] }}</strong>
                </div>

                <hr class="my-3">

                <p class="text-muted fs-11 text-uppercase fw-semibold mb-2" style="letter-spacing:.5px;">Buyers</p>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Total Referred</span>
                    <strong>{{ $buyerStats['total'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Placed an Order</span>
                    <strong class="text-success">{{ $buyerStats['with_orders'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Booked Delivery</span>
                    <strong class="text-primary">{{ $buyerStats['with_bookings'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fs-13">Total Orders</span>
                    <strong style="color:#E67E22;">{{ $buyerStats['total_orders'] }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted fs-13">Total Bookings</span>
                    <strong style="color:#E74C3C;">{{ $buyerStats['total_bookings'] }}</strong>
                </div>
            </div>
        </div>

        {{-- How It Works --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">How It Works</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-3 mb-3 align-items-start">
                    <div class="avatar-text rounded-circle flex-shrink-0"
                         style="width:32px;height:32px;background:#D5F5E3;color:#2ECC71;font-size:13px;font-weight:700;">
                        1
                    </div>
                    <div>
                        <p class="fw-semibold mb-0 fs-13">Share your code</p>
                        <p class="text-muted fs-12 mb-0">Send your OR-MRT- code to sellers and buyers</p>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-3 align-items-start">
                    <div class="avatar-text rounded-circle flex-shrink-0"
                         style="width:32px;height:32px;background:#EBF5FB;color:#2980B9;font-size:13px;font-weight:700;">
                        2
                    </div>
                    <div>
                        <p class="fw-semibold mb-0 fs-13">They register</p>
                        <p class="text-muted fs-12 mb-0">They paste your code in the referral field</p>
                    </div>
                </div>
                <div class="d-flex gap-3 align-items-start">
                    <div class="avatar-text rounded-circle flex-shrink-0"
                         style="width:32px;height:32px;background:#FEF9E7;color:#F39C12;font-size:13px;font-weight:700;">
                        3
                    </div>
                    <div>
                        <p class="fw-semibold mb-0 fs-13">Track everything</p>
                        <p class="text-muted fs-12 mb-0">See who ordered and who booked deliveries</p>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end col-lg-4 --}}

    {{-- ── Right column — tabbed Sellers / Buyers tables ────────────── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header pb-0">
                <ul class="nav nav-tabs card-header-tabs" id="referralTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold"
                                id="sellers-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#sellersPane"
                                type="button"
                                role="tab"
                                aria-controls="sellersPane"
                                aria-selected="true">
                            <i class="feather-briefcase me-1"></i> Sellers
                            <span class="badge bg-secondary ms-1">{{ $sellers->total() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold"
                                id="buyers-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#buyersPane"
                                type="button"
                                role="tab"
                                aria-controls="buyersPane"
                                aria-selected="false">
                            <i class="feather-users me-1"></i> Buyers
                            <span class="badge ms-1" style="background:#7B1FA2;color:#fff;">{{ $buyers->total() }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" style="position:relative;overflow:hidden;">

                {{-- ════ SELLERS PANE ════ --}}
                <div class="tab-pane fade show active"
                     id="sellersPane"
                     role="tabpanel"
                     aria-labelledby="sellers-tab">
                    @if($sellers->isEmpty())
                    <div class="text-center py-5">
                        <i class="feather-briefcase" style="font-size:48px;color:#d1d5db;"></i>
                        <h6 class="mt-3 text-muted fw-normal">
                            @if($from || $to)
                                No sellers found for the selected period.
                            @else
                                No sellers have used your code yet.
                            @endif
                        </h6>
                        @if(!($from || $to) && $marketer->marketing_code)
                        <p class="text-muted fs-13">
                            Share
                            <code style="background:#1a1f2e;color:#2ECC71;padding:2px 8px;border-radius:4px;">
                                {{ $marketer->marketing_code }}
                            </code>
                            to get started.
                        </p>
                        @endif
                    </div>
                    @else
                    <div class="table-responsive" style="overflow-x: auto !important; -webkit-overflow-scrolling: touch;">
                        <table class="table table-hover align-middle mb-0" style="min-width: 650px; width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold" style="width: 50px;">#</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Business</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sellers as $seller)
                                <tr>
                                    <td class="text-muted fs-13">
                                        {{ ($sellers->currentPage() - 1) * $sellers->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2" style="min-width: 150px;">
                                            <div class="avatar-text avatar-sm rounded-circle fw-semibold"
                                                 style="background:#f1f3f5;color:#495057;font-size:11px;flex-shrink:0;">
                                                {{ strtoupper(substr($seller->first_name, 0, 1)) }}{{ strtoupper(substr($seller->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold fs-13">{{ $seller->full_name }}</div>
                                                <div class="text-muted fs-12">{{ $seller->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fs-13">{{ $seller->business_name }}</td>
                                    <td>
                                        @if($seller->is_verified_business)
                                            <span class="badge bg-primary-subtle text-primary fw-semibold">Registered</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary fw-semibold">Individual</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($seller->is_approved)
                                            <span class="badge bg-success-subtle text-success fw-semibold">Approved</span>
                                        @elseif(isset($seller->verification_status) && $seller->verification_status === 'rejected')
                                            <span class="badge bg-danger-subtle text-danger fw-semibold">Rejected</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning fw-semibold">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-muted fs-12" style="white-space: nowrap;">{{ $seller->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($sellers->hasPages())
                    <div class="p-3 border-top">
                        {{ $sellers->appends(request()->query())->links() }}
                    </div>
                    @endif
                    @endif
                </div>{{-- end sellersPane --}}

                {{-- ════ BUYERS PANE ════ --}}
                <div class="tab-pane fade"
                     id="buyersPane"
                     role="tabpanel"
                     aria-labelledby="buyers-tab">
                    @if($buyers->isEmpty())
                    <div class="text-center py-5">
                        <i class="feather-users" style="font-size:48px;color:#d1d5db;"></i>
                        <h6 class="mt-3 text-muted fw-normal">
                            @if($from || $to)
                                No buyers found for the selected period.
                            @else
                                No buyers have used your code yet.
                            @endif
                        </h6>
                        @if(!($from || $to) && $marketer->marketing_code)
                        <p class="text-muted fs-13">
                            Share
                            <code style="background:#1a1f2e;color:#2ECC71;padding:2px 8px;border-radius:4px;">
                                {{ $marketer->marketing_code }}
                            </code>
                            with potential buyers at signup.
                        </p>
                        @endif
                    </div>
                    @else
                    <div class="table-responsive" style="overflow-x: auto !important; -webkit-overflow-scrolling: touch;">
                        <table class="table table-hover align-middle mb-0" style="min-width: 650px; width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold" style="width: 50px;">#</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Buyer</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold text-center">Orders</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold text-center">Bookings</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Activity</th>
                                    <th class="fs-11 text-uppercase text-muted fw-semibold">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($buyers as $buyer)
                                @php
                                    $bOrders   = $orderCounts[$buyer->id]   ?? 0;
                                    $bBookings = $bookingCounts[$buyer->id] ?? 0;
                                @endphp
                                <tr>
                                    <td class="text-muted fs-13">
                                        {{ ($buyers->currentPage() - 1) * $buyers->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2" style="min-width: 150px;">
                                            <div class="avatar-text avatar-sm rounded-circle fw-semibold"
                                                 style="background:#EDE7F6;color:#7B1FA2;font-size:11px;flex-shrink:0;">
                                                {{ strtoupper(substr($buyer->first_name, 0, 1)) }}{{ strtoupper(substr($buyer->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold fs-13">{{ $buyer->full_name }}</div>
                                                <div class="text-muted fs-12">{{ $buyer->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge fw-semibold {{ $bOrders > 0 ? 'bg-success-subtle text-success' : 'bg-light text-muted' }}">
                                            {{ $bOrders }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge fw-semibold {{ $bBookings > 0 ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }}">
                                            {{ $bBookings }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($bOrders > 0 && $bBookings > 0)
                                            <span class="badge bg-success-subtle text-success fw-semibold">
                                                <i class="feather-star me-1"></i> Fully Active
                                            </span>
                                        @elseif($bOrders > 0)
                                            <span class="badge bg-warning-subtle text-warning fw-semibold">
                                                <i class="feather-shopping-cart me-1"></i> Ordered
                                            </span>
                                        @elseif($bBookings > 0)
                                            <span class="badge bg-info-subtle text-info fw-semibold">
                                                <i class="feather-truck me-1"></i> Booked Only
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted fw-semibold">Registered</span>
                                        @endif
                                    </td>
                                    <td class="text-muted fs-12" style="white-space: nowrap;">{{ $buyer->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($buyers->hasPages())
                    <div class="p-3 border-top">
                        {{ $buyers->appends(request()->query())->links() }}
                    </div>
                    @endif
                    @endif
                </div>{{-- end buyersPane --}}

            </div>{{-- end tab-content --}}
        </div>{{-- end card --}}
    </div>{{-- end col-lg-8 --}}

</div>{{-- end row --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Manually init Bootstrap tabs to ensure they work regardless of load order
    const tabButtons = document.querySelectorAll('#referralTabs button[data-bs-toggle="tab"]');

    tabButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            // Deactivate all tabs and panes
            tabButtons.forEach(function (b) {
                b.classList.remove('active');
                b.setAttribute('aria-selected', 'false');
            });

            document.querySelectorAll('.tab-content .tab-pane').forEach(function (pane) {
                pane.classList.remove('show', 'active');
            });

            // Activate the clicked tab and its target pane
            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');

            const target = document.querySelector(btn.getAttribute('data-bs-target'));
            if (target) {
                target.classList.add('show', 'active');
            }
        });
    });
});

function toggleCustom() {
    const el = document.getElementById('customRange');
    const isHidden = el.classList.contains('d-none');
    el.classList.toggle('d-none', !isHidden);
    el.classList.toggle('d-flex', isHidden);
    if (isHidden) document.getElementById('rangeHidden').value = 'custom';
}

function copyCode(el) {
    const code = el.textContent.trim();
    const flash = () => {
        const orig = el.style.color, origBg = el.style.background;
        el.style.color = '#fff';
        el.style.background = '#27AE60';
        setTimeout(() => { el.style.color = orig; el.style.background = origBg; }, 1500);
    };
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(code).then(flash).catch(() => fallbackCopy(code, flash));
    } else {
        fallbackCopy(code, flash);
    }
}

function fallbackCopy(text, onSuccess) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;left:-9999px;top:-9999px;opacity:0;';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    try { document.execCommand('copy'); if (onSuccess) onSuccess(); }
    catch (e) { alert('Copy failed — please copy manually: ' + text); }
    document.body.removeChild(ta);
}
</script>

@endsection