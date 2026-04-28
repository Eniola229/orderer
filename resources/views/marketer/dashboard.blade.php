@extends('layouts.marketer')
@section('title', 'Dashboard')
@section('page_title', 'Marketing Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Overview</li>
@endsection

@section('content')

{{-- ── Marketing code alert strip (only if they have no code yet) ─────────── --}}
@if(!$marketer->marketing_code)
<div class="row mb-3">
    <div class="col-12">
        <div class="alert mb-0 d-flex align-items-center gap-3"
             style="background:#FEF9E7;border:1px solid #F9CA24;border-radius:10px;">
            <i class="feather-zap" style="font-size:20px;color:#B7950B;flex-shrink:0;"></i>
            <div class="flex-grow-1">
                <strong style="color:#B7950B;">You don't have a marketing code yet</strong>
                <p class="mb-0 text-muted" style="font-size:12px;">Generate your code below so sellers can start using it.</p>
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

            {{-- Custom date inputs --}}
            <div id="customRange"
                 class="{{ ($range ?? '30') == 'custom' ? 'd-flex' : 'd-none' }} gap-2 align-items-center">
                <input type="hidden" name="range" id="rangeHidden" value="custom">
                <input type="date" name="from"
                       class="form-control form-control-sm"
                       value="{{ $from ? $from->format('Y-m-d') : '' }}"
                       style="width:145px;">
                <span class="text-muted">–</span>
                <input type="date" name="to"
                       class="form-control form-control-sm"
                       value="{{ $to ? $to->format('Y-m-d') : '' }}"
                       style="width:145px;">
                <button type="submit" class="btn btn-sm btn-primary">Apply</button>
            </div>

            @if($from || $to)
            <a href="{{ route('marketer.dashboard') }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="feather-x me-1"></i> Clear
            </a>
            @endif

            @if($from && $to)
            <span class="ms-auto text-muted fs-12">
                <strong>{{ $from->format('M d, Y') }}</strong>
                –
                <strong>{{ $to->format('M d, Y') }}</strong>
            </span>
            @endif

        </form>
    </div>
</div>

{{-- ── Stat cards ──────────────────────────────────────────────────────────── --}}
<div class="row">

    {{-- Total Referrals --}}
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Referrals</p>
                        <h2 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h2>
                        @if($from || $to)
                        <small class="text-muted">filtered period</small>
                        @endif
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#D5F5E3;color:#2ECC71;flex-shrink:0;">
                        <i class="feather-users"></i>
                    </div>
                </div>
                <p class="text-muted fs-12 mb-0">
                    <span class="text-success fw-semibold">{{ $stats['this_month'] }}</span>
                    joined this month
                </p>
            </div>
        </div>
    </div>

    {{-- Approved --}}
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Approved Sellers</p>
                        <h2 class="fw-bold mb-0 text-success">{{ number_format($stats['approved']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#D5F5E3;color:#27AE60;flex-shrink:0;">
                        <i class="feather-check-circle"></i>
                    </div>
                </div>
                @if($stats['total'] > 0)
                <p class="text-muted fs-12 mb-0">
                    <span class="text-success fw-semibold">
                        {{ round(($stats['approved'] / $stats['total']) * 100, 1) }}%
                    </span>
                    approval rate
                </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Pending --}}
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Pending Approval</p>
                        <h2 class="fw-bold mb-0 text-warning">{{ number_format($stats['pending']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#FEF9E7;color:#F39C12;flex-shrink:0;">
                        <i class="feather-clock"></i>
                    </div>
                </div>
                <p class="text-muted fs-12 mb-0">Awaiting admin review</p>
            </div>
        </div>
    </div>

    {{-- This Month --}}
    <div class="col-xxl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Joined This Month</p>
                        <h2 class="fw-bold mb-0 text-primary">{{ number_format($stats['this_month']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-lg rounded"
                         style="background:#EBF5FB;color:#2980B9;flex-shrink:0;">
                        <i class="feather-calendar"></i>
                    </div>
                </div>
                <p class="text-muted fs-12 mb-0">{{ now()->format('F Y') }}</p>
            </div>
        </div>
    </div>

</div>

<div class="row">

    {{-- ── Left: marketing code + how it works ──────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Marketing Code Card --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Your Marketing Code</h5>
            </div>
            <div class="card-body text-center py-4">

                @if($marketer->marketing_code)
                <p class="text-muted fs-13 mb-3">
                    Share this with sellers — they enter it in the referral field at registration.
                </p>

                <div id="mktCode"
                     style="background:#1a1f2e;color:#2ECC71;font-family:'Courier New',monospace;
                            font-size:20px;font-weight:700;letter-spacing:3px;
                            padding:16px 24px;border-radius:10px;display:inline-block;
                            cursor:pointer;user-select:all;"
                     onclick="copyCode(this)"
                     title="Click to copy">
                    {{ $marketer->marketing_code }}
                </div>

                <p class="text-muted fs-11 mt-2 mb-3">Click code to copy</p>

                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-sm btn-outline-secondary" onclick="copyCode(document.getElementById('mktCode'))">
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
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted fs-13">Total Referred</span>
                    <strong>{{ $stats['total'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted fs-13">Approved</span>
                    <strong class="text-success">{{ $stats['approved'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted fs-13">Pending</span>
                    <strong class="text-warning">{{ $stats['pending'] }}</strong>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="text-muted fs-13">This Month</span>
                    <strong class="text-primary">{{ $stats['this_month'] }}</strong>
                </div>
            </div>
        </div>

        {{-- How it works --}}
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
                        <p class="text-muted fs-12 mb-0">Send your OR-MRT- code to potential sellers</p>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-3 align-items-start">
                    <div class="avatar-text rounded-circle flex-shrink-0"
                         style="width:32px;height:32px;background:#EBF5FB;color:#2980B9;font-size:13px;font-weight:700;">
                        2
                    </div>
                    <div>
                        <p class="fw-semibold mb-0 fs-13">Seller registers</p>
                        <p class="text-muted fs-12 mb-0">They paste your code in the referral field</p>
                    </div>
                </div>
                <div class="d-flex gap-3 align-items-start">
                    <div class="avatar-text rounded-circle flex-shrink-0"
                         style="width:32px;height:32px;background:#FEF9E7;color:#F39C12;font-size:13px;font-weight:700;">
                        3
                    </div>
                    <div>
                        <p class="fw-semibold mb-0 fs-13">Tracked instantly</p>
                        <p class="text-muted fs-12 mb-0">They appear in your referrals table immediately</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Right: referrals table ─────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    Referred Sellers
                    @if($from || $to)
                    <small class="text-muted fw-normal fs-12">
                        — {{ $from ? $from->format('M d, Y') : '…' }}
                        to {{ $to ? $to->format('M d, Y') : '…' }}
                    </small>
                    @endif
                </h5>
                <span class="badge bg-light text-dark fw-semibold border">
                    {{ $sellers->total() }} total
                </span>
            </div>

            <div class="card-body p-0">
                @if($sellers->isEmpty())
                <div class="text-center py-5">
                    <i class="feather-users" style="font-size:48px;color:#d1d5db;"></i>
                    <h6 class="mt-3 text-muted fw-normal">
                        @if($from || $to)
                            No referrals found for the selected period.
                        @else
                            No sellers have used your code yet.
                        @endif
                    </h6>
                    @if(!($from || $to) && $marketer->marketing_code)
                    <p class="text-muted fs-13">
                        Share <code style="background:#1a1f2e;color:#2ECC71;padding:2px 8px;border-radius:4px;">{{ $marketer->marketing_code }}</code>
                        to get started.
                    </p>
                    @endif
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">#</th>
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
                                    <div class="d-flex align-items-center gap-2">
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
                                <td class="text-muted fs-12">
                                    {{ $seller->created_at->format('M d, Y') }}
                                </td>
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
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function toggleCustom() {
    const el = document.getElementById('customRange');
    const isHidden = el.classList.contains('d-none');
    el.classList.toggle('d-none', !isHidden);
    el.classList.toggle('d-flex', isHidden);
    if (isHidden) {
        document.getElementById('rangeHidden').value = 'custom';
    }
}

function copyCode(el) {
    const code = el.textContent.trim();
    navigator.clipboard.writeText(code).then(() => {
        const original = el.style.color;
        el.style.color = '#fff';
        el.style.background = '#27AE60';
        setTimeout(() => {
            el.style.color = original;
            el.style.background = '#1a1f2e';
        }, 1500);
    });
}
</script>
@endpush