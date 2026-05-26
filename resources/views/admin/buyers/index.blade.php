@extends('layouts.admin')
@section('title', 'Buyers')
@section('page_title', 'Buyer Management')
@section('breadcrumb')
    <li class="breadcrumb-item active">Buyers</li>
@endsection

@section('content')

{{-- ── Stats Grid ──────────────────────────────────────────────────────── --}}
<div class="buyers-stats-grid mb-4">

    <div class="stat-card stat-total">
        <div class="stat-icon"><i class="feather-users"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['total']) }}</span>
            <span class="stat-label">Total Buyers</span>
        </div>
    </div>

    <div class="stat-card stat-active">
        <div class="stat-icon"><i class="feather-activity"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['active_buyers']) }}</span>
            <span class="stat-label">
                Active Buyers
                <span class="period-badge">{{ $stats['active_period_label'] }}</span>
            </span>
        </div>
    </div>

    <div class="stat-card stat-inactive">
        <div class="stat-icon"><i class="feather-moon"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['inactive_buyers']) }}</span>
            <span class="stat-label">
                Inactive Buyers
                <span class="period-badge">no order in {{ $stats['active_period_label'] }}</span>
            </span>
        </div>
    </div>

    <div class="stat-card stat-ordered">
        <div class="stat-icon"><i class="feather-shopping-bag"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['have_ordered']) }}</span>
            <span class="stat-label">Have Ordered</span>
        </div>
    </div>
    
    <div class="stat-card stat-never">
        <div class="stat-icon"><i class="feather-user-x"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['never_ordered']) }}</span>
            <span class="stat-label">Never Ordered</span>
        </div>
    </div>

    <div class="stat-card stat-total-orders">
        <div class="stat-icon"><i class="feather-package"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['total_orders']) }}</span>
            <span class="stat-label">Total Orders</span>
        </div>
    </div>

    <div class="stat-card stat-suspended">
        <div class="stat-icon"><i class="feather-slash"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['suspended']) }}</span>
            <span class="stat-label">Suspended</span>
        </div>
    </div>

<!--     <div class="stat-card stat-unverified">
        <div class="stat-icon"><i class="feather-mail"></i></div>
        <div class="stat-body">
            <span class="stat-value">{{ number_format($stats['unverified_email']) }}</span>
            <span class="stat-label">Unverified Email</span>
        </div>
    </div> -->

    <div class="stat-card stat-wallet">
        <div class="stat-icon"><i class="feather-credit-card"></i></div>
        <div class="stat-body">
            <span class="stat-value mono">&#8358;{{ number_format($stats['total_wallet'], 2) }}</span>
            <span class="stat-label">Total Wallet Balance</span>
        </div>
    </div>

</div>

{{-- ── Active-period filter + Search ──────────────────────────────────── --}}
<form action="{{ route('admin.buyers.index') }}" method="GET"
      class="d-flex gap-2 mb-4 flex-wrap align-items-center">

    {{-- Status filter --}}
    <div class="btn-group">
        @foreach(['all'=>'All','active'=>'Active','suspended'=>'Suspended'] as $val => $label)
        <a href="{{ route('admin.buyers.index', array_merge(request()->except('status','page'), ['status' => $val])) }}"
           class="btn btn-sm {{ request('status','all') === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Active-period selector --}}
    <div class="d-flex align-items-center gap-1 ms-2">
        <label class="text-muted fs-12 mb-0 me-1">Active period:</label>
        <select name="active_months" class="form-select form-select-sm" style="width:auto;"
                onchange="this.form.submit()">
            @foreach([1=>'1 month',3=>'3 months',6=>'6 months',12=>'1 year',24=>'2 years'] as $val => $label)
            <option value="{{ $val }}" {{ (int)request('active_months', 12) === $val ? 'selected' : '' }}>
                {{ $label }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Search --}}
    <div class="d-flex gap-2 ms-auto">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search name or email..." value="{{ request('search') }}"
               style="width:260px;">
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="feather-search"></i>
        </button>
    </div>

</form>

{{-- ── Table ───────────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-body p-0">
        @if($buyers->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Buyer</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Email</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Phone</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Orders</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Joined</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($buyers as $buyer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($buyer->avatar)
                                <img src="{{ $buyer->avatar }}"
                                     style="width:36px;height:36px;border-radius:50%;object-fit:cover;" alt="">
                                @else
                                <div style="width:36px;height:36px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($buyer->first_name,0,1)) }}
                                </div>
                                @endif
                                <strong class="fs-13">
                                    {{ $buyer->first_name }} {{ $buyer->last_name }}
                                </strong>
                            </div>
                        </td>
                        <td class="fs-13 text-muted">{{ $buyer->email }}</td>
                        <td class="fs-13 text-muted">{{ $buyer->phone ?? '—' }}</td>
                        <td class="fw-semibold">{{ $buyer->orders_count }}</td>
                        <td>
                            @if($buyer->is_active == 1)
                                <span class="badge orderer-badge badge-approved">Active</span>
                            @else
                                <span class="badge orderer-badge badge-rejected">Suspended</span>
                            @endif
                        </td>
                        <td class="text-muted fs-12">{{ $buyer->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.buyers.show', $buyer->id) }}"
                                   class="btn btn-sm btn-outline-primary">View</a>
                                @if(auth('admin')->user()->canModerateSellers())
                                    @if($buyer->is_active == 1)
                                    <form action="{{ route('admin.buyers.suspend', $buyer->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Suspend this buyer?')">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Suspend</button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.buyers.unsuspend', $buyer->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-success">Reinstate</button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $buyers->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-users mb-2 d-block" style="font-size:40px;"></i>
            <p>No buyers found.</p>
        </div>
        @endif
    </div>
</div>

<style>
.buyers-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}
.stat-card {
    background: #fff;
    border-radius: 10px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid #e9ecef;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
    transition: box-shadow .15s;
    min-width: 0;
    overflow: hidden;
}
.stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.stat-icon {
    width: 38px; height: 38px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 17px;
}
.stat-body { display: flex; flex-direction: column; min-width: 0; flex: 1; overflow: hidden; }
.stat-value {
    font-size: 17px;
    font-weight: 700;
    line-height: 1.3;
    color: #111;
    white-space: normal;
    word-break: break-word;
    overflow-wrap: anywhere;
}
.stat-value.mono {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 12.5px;
    font-weight: 700;
    white-space: normal;
    word-break: break-word;
    overflow-wrap: anywhere;
    letter-spacing: -0.02em;
}
.stat-label { font-size: 11px; color: #6c757d; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.period-badge {
    display: inline-block;
    background: #f1f5f9;
    color: #475569;
    border-radius: 4px;
    padding: 1px 5px;
    font-size: 10px;
    font-weight: 600;
    margin-left: 3px;
    vertical-align: middle;
    white-space: nowrap;
}

.stat-total        .stat-icon { background:#eff6ff; color:#2563eb; }
.stat-active       .stat-icon { background:#ecfdf5; color:#059669; }
.stat-inactive     .stat-icon { background:#fffbeb; color:#d97706; }
.stat-never        .stat-icon { background:#fdf2f8; color:#be185d; }
.stat-ordered      .stat-icon { background:#f0fdf4; color:#16a34a; }
.stat-total-orders .stat-icon { background:#fff7ed; color:#ea580c; }
.stat-suspended    .stat-icon { background:#fff1f2; color:#e11d48; }
.stat-unverified   .stat-icon { background:#f8fafc;  color:#64748b; }
.stat-wallet       .stat-icon { background:#eff6ff; color:#1d4ed8; }

@media (max-width:576px) {
    .buyers-stats-grid { grid-template-columns: repeat(2,1fr); }
}
</style>

@endsection