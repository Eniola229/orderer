@extends('layouts.seller')
@section('title', 'Promotions')
@section('page_title', 'My Promotions')
@section('breadcrumb')
    <li class="breadcrumb-item active">Promotions</li>
@endsection
@section('page_actions')
    <a href="{{ route('seller.ads.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> Create Ad
    </a>
@endsection

@section('content')

<style>
    .orderer-badge.badge-expired    { background-color: #EAECEE; color: #566573; }
</style>

{{-- Stats Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card mb-0">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Ads</p>
                <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                <p class="fs-12 mt-1 mb-0 text-muted">
                    <span class="text-success fw-semibold">{{ $stats['active'] }} active</span>
                    &middot; {{ $stats['pending'] }} pending
                </p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mb-0">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Spent</p>
                <h4 class="fw-bold mb-0">₦{{ number_format($stats['total_spent'], 2) }}</h4>
                <p class="fs-12 mt-1 mb-0 text-muted">
                    of ₦{{ number_format($stats['total_budget'], 2) }} budget
                </p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mb-0">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Impressions</p>
                <h4 class="fw-bold mb-0">{{ number_format($stats['impressions']) }}</h4>
                <p class="fs-12 mt-1 mb-0 text-muted">Total views across all ads</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mb-0">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Clicks / CTR</p>
                <h4 class="fw-bold mb-0">{{ number_format($stats['clicks']) }}</h4>
                <p class="fs-12 mt-1 mb-0 text-muted">
                    <span class="fw-semibold text-primary">{{ $stats['ctr'] }}% CTR</span>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('seller.ads.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="fs-12 text-muted fw-semibold mb-1">Search</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control form-control-sm"
                           placeholder="Search by title...">
                </div>
                <div class="col-md-2">
                    <label class="fs-12 text-muted fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        @foreach(['pending','active','paused','rejected','completed'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="fs-12 text-muted fw-semibold mb-1">Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="fs-12 text-muted fw-semibold mb-1">From</label>
                    <input type="date"
                           name="date_from"
                           value="{{ request('date_from') }}"
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="fs-12 text-muted fw-semibold mb-1">To</label>
                    <input type="date"
                           name="date_to"
                           value="{{ request('date_to') }}"
                           class="form-control form-control-sm">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="feather-filter"></i>
                    </button>
                    @if(request()->hasAny(['search','status','category','date_from','date_to']))
                        <a href="{{ route('seller.ads.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="feather-x"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Ads Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($ads->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Ad</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Slot</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Budget</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Spent</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Impressions</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Clicks</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">CTR</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Period</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ads as $ad)
                    @php
                        $ctr = $ad->total_impressions > 0
                            ? round(($ad->total_clicks / $ad->total_impressions) * 100, 2)
                            : 0;
                        $budgetUsed = $ad->budget > 0
                            ? round(($ad->amount_spent / $ad->budget) * 100)
                            : 0;
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($ad->media_url && $ad->media_type === 'image')
                                    <img src="{{ $ad->media_url }}"
                                         style="width:42px;height:42px;object-fit:cover;border-radius:8px;"
                                         alt="">
                                @elseif($ad->media_url && $ad->media_type === 'video')
                                    <div style="width:42px;height:42px;background:#1a1a2e;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="feather-play text-white" style="font-size:16px;"></i>
                                    </div>
                                @else
                                    <div style="width:42px;height:42px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="feather-trending-up text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold fs-13">{{ Str::limit($ad->title, 28) }}</p>
                                    <small class="text-muted fs-11">{{ ucfirst($ad->media_type ?? 'listing') }} Ad</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark fs-11">
                                {{ $ad->adCategory->name ?? '—' }}
                            </span>
                        </td>
                        <td class="fs-12 text-muted">
                            {{ $ad->bannerSlot->name ?? 'Top Listing / CPC' }}
                        </td>
                        <td>
                            <p class="fw-bold fs-13 mb-0">₦{{ number_format($ad->budget, 2) }}</p>
                            {{-- Budget usage bar --}}
                            <div style="height:3px;width:70px;background:#eee;border-radius:2px;margin-top:4px;">
                                <div style="height:3px;border-radius:2px;width:{{ min($budgetUsed, 100) }}%;background:{{ $budgetUsed >= 90 ? '#E74C3C' : ($budgetUsed >= 60 ? '#F39C12' : '#2ECC71') }};"></div>
                            </div>
                            <span class="fs-11 text-muted">{{ $budgetUsed }}% used</span>
                        </td>
                        <td class="text-danger fw-semibold fs-13">
                            ₦{{ number_format($ad->amount_spent, 2) }}
                        </td>
                        <td class="fs-13">
                            <i class="feather-eye text-muted me-1" style="font-size:12px;"></i>
                            {{ number_format($ad->total_impressions) }}
                        </td>
                        <td class="fs-13">
                            <i class="feather-mouse-pointer text-muted me-1" style="font-size:12px;"></i>
                            {{ number_format($ad->total_clicks) }}
                        </td>
                        <td>
                            <span class="fw-semibold fs-13 {{ $ctr >= 3 ? 'text-success' : ($ctr >= 1 ? 'text-warning' : 'text-muted') }}">
                                {{ $ctr }}%
                            </span>
                        </td>
                        <td>
                            <span class="badge orderer-badge badge-{{ $ad->status }}">
                                {{ ucfirst($ad->status) }}
                            </span>
                            @if($ad->status === 'rejected' && $ad->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">
                                    {{ Str::limit($ad->rejection_reason, 30) }}
                                </p>
                            @endif
                        </td>
                        <td class="fs-12 text-muted">
                            {{ $ad->start_date?->format('M d') }} –
                            {{ $ad->end_date?->format('M d, Y') }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('seller.ads.show', $ad->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="View">
                                    <i class="feather-eye"></i>
                                </a>
                                @if($ad->status === 'active')
                                <form action="{{ route('seller.ads.pause', $ad->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Pause">
                                        <i class="feather-pause"></i>
                                    </button>
                                </form>
                                @elseif($ad->status === 'paused')
                                <form action="{{ route('seller.ads.resume', $ad->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Resume">
                                        <i class="feather-play"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('seller.ads.destroy', $ad->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this ad? Unspent budget will be refunded.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
        <div class="p-3 d-flex align-items-center justify-content-between">
            <p class="text-muted fs-12 mb-0">
                Showing {{ $ads->firstItem() }}–{{ $ads->lastItem() }} of {{ $ads->total() }} ads
            </p>
            {{ $ads->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-trending-up mb-3 d-block" style="font-size:40px;"></i>
            <p class="mb-1 fw-semibold">
                {{ request()->hasAny(['search','status','category','date_from','date_to']) ? 'No ads match your filters.' : 'No ads created yet.' }}
            </p>
            @if(request()->hasAny(['search','status','category','date_from','date_to']))
                <a href="{{ route('seller.ads.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                    <i class="feather-x me-1"></i> Clear Filters
                </a>
            @else
                <a href="{{ route('seller.ads.create') }}" class="btn btn-sm btn-primary mt-2">
                    <i class="feather-plus me-1"></i> Create Your First Ad
                </a>
            @endif
        </div>
        @endif
    </div>
</div>

@endsection