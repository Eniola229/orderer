@extends('layouts.admin')
@section('title', 'Activity Logs')
@section('page_title', 'Activity Logs')
@section('breadcrumb')
    <li class="breadcrumb-item active">Logs</li>
@endsection

@section('content')

<style>
    .log-row:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .log-details td {
        border-top: none !important;
    }
    .feather-chevron-right, .feather-chevron-down {
        transition: transform 0.3s ease;
    }
</style>

@if(!auth('admin')->user()->canViewLogs())
<div class="alert alert-danger">
    <i class="feather-lock me-2"></i>
    Only Super Admins can view activity logs.
</div>
@else

{{-- Stats Cards --}}
<div class="row mb-4">
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Logs</p>
                <h2 class="fw-bold mb-0 text-primary">{{ number_format($stats['total']) }}</h2>
                <small class="text-muted">All activities</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Admins</p>
                <h2 class="fw-bold mb-0 text-success">{{ number_format($stats['admin']) }}</h2>
                <small class="text-muted">Admin activities</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Sellers</p>
                <h2 class="fw-bold mb-0 text-warning">{{ number_format($stats['seller']) }}</h2>
                <small class="text-muted">Seller activities</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Buyers</p>
                <h2 class="fw-bold mb-0 text-info">{{ number_format($stats['buyer']) }}</h2>
                <small class="text-muted">Buyer activities</small>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.logs.index') }}" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Guard Type</label>
                <select name="guard" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="admin" {{ request('guard') == 'admin' ? 'selected' : '' }}>Admins</option>
                    <option value="seller" {{ request('guard') == 'seller' ? 'selected' : '' }}>Sellers</option>
                    <option value="buyer" {{ request('guard') == 'buyer' ? 'selected' : '' }}>Buyers</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Method</label>
                <select name="method" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="GET" {{ request('method') == 'GET' ? 'selected' : '' }}>GET</option>
                    <option value="POST" {{ request('method') == 'POST' ? 'selected' : '' }}>POST</option>
                    <option value="PUT" {{ request('method') == 'PUT' ? 'selected' : '' }}>PUT</option>
                    <option value="DELETE" {{ request('method') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Status Code</label>
                <select name="status_code" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="200" {{ request('status_code') == '200' ? 'selected' : '' }}>200 OK</option>
                    <option value="201" {{ request('status_code') == '201' ? 'selected' : '' }}>201 Created</option>
                    <option value="302" {{ request('status_code') == '302' ? 'selected' : '' }}>302 Redirect</option>
                    <option value="400" {{ request('status_code') == '400' ? 'selected' : '' }}>400 Bad Request</option>
                    <option value="401" {{ request('status_code') == '401' ? 'selected' : '' }}>401 Unauthorized</option>
                    <option value="403" {{ request('status_code') == '403' ? 'selected' : '' }}>403 Forbidden</option>
                    <option value="404" {{ request('status_code') == '404' ? 'selected' : '' }}>404 Not Found</option>
                    <option value="500" {{ request('status_code') == '500' ? 'selected' : '' }}>500 Server Error</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold fs-12">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Email, name, user ID, URL..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="feather-x"></i> Clear
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="feather-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($logs->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    
                        <th class="fs-11 text-uppercase text-muted fw-semibold" width="5%"></th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">User</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Guard</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Method</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">URL</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">IP</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Time</th>
                     </thead>
                <tbody>
                    @foreach($logs as $log)
                    @php
                        $user = $log->getUser();
                        $userName = $user ? ($user->first_name . ' ' . $user->last_name) : 'Guest';
                        if ($log->guard_type === 'seller' && $user && $user->business_name) {
                            $userName = $user->business_name;
                        }
                        $userEmail = $user ? ($user->email ?? '—') : '—';
                        $userAvatar = $user ? ($user->avatar ?? null) : null;
                        $guardTypeLabel = [
                            'admin' => 'Admin',
                            'seller' => 'Seller',
                            'buyer' => 'Buyer',
                        ][$log->guard_type] ?? 'Guest';
                        
                        $methodColors = [
                            'GET'    => '#D5F5E3',
                            'POST'   => '#EBF5FB',
                            'PUT'    => '#FEF9E7',
                            'DELETE' => '#FADBD8',
                        ];
                        $textColors = [
                            'GET'    => '#1E8449',
                            'POST'   => '#1A5276',
                            'PUT'    => '#B7950B',
                            'DELETE' => '#A93226',
                        ];
                        $m   = $log->method ?? 'GET';
                        $bg  = $methodColors[$m] ?? '#eee';
                        $clr = $textColors[$m] ?? '#333';
                    @endphp
                    <tr class="log-row" data-id="{{ $log->id }}" style="cursor: pointer;">
                        <td class="text-center">
                            <i class="feather-chevron-right" id="icon-{{ $log->id }}" style="font-size: 16px; color: #6c757d;"></i>
                          
                        
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($userAvatar)
                                <img src="{{ $userAvatar }}" 
                                     style="width:32px;height:32px;border-radius:50%;object-fit:cover;" alt="">
                                @else
                                <div style="width:32px;height:32px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">
                                    {{ strtoupper(substr($userName, 0, 1)) }}
                                </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold fs-13">{{ $userName }}</p>
                                    <small class="text-muted">{{ $userEmail }}</small>
                                </div>
                            </div>
                        
                        
                        <td>
                            <span class="badge bg-light text-dark fs-11">
                                {{ $guardTypeLabel }}
                            </span>
                        
                        
                        <td>
                            <span style="background:{{ $bg }};color:{{ $clr }};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;">
                                {{ $m }}
                            </span>
                        
                        
                        <td>
                            <code class="fs-12" style="word-break:break-all;max-width:300px;display:block;">
                                {{ Str::limit($log->url, 50) }}
                            </code>
                        
                        
                        <td class="fs-12 text-muted">{{ $log->ip_address ?? '—' }}  
                        
                        <td>
                            <span class="{{ ($log->status_code ?? 200) >= 400 ? 'text-danger' : 'text-success' }} fw-semibold fs-12">
                                {{ $log->status_code ?? '—' }}
                            </span>
                        
                        
                        <td class="text-muted fs-12">
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                        
                      


                    <tr class="log-details" id="details-{{ $log->id }}" style="display: none;">
                        <td colspan="8" class="bg-light">
                            <div style="padding: 20px;">
                                <h6 class="mb-3 text-primary">
                                    <i class="feather-info me-2"></i>Full Log Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">User</label>
                                            <strong>{{ $userName }}</strong>
                                            <small class="text-muted d-block">{{ $userEmail }}</small>
                                            <small class="text-muted">{{ $guardTypeLabel }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">User ID</label>
                                            <code>{{ $log->guard_id ?? 'N/A' }}</code>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Log ID</label>
                                            <code>{{ $log->id }}</code>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Method</label>
                                            <strong>{{ $log->method ?? 'GET' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Status Code</label>
                                            <strong class="{{ ($log->status_code ?? 200) >= 400 ? 'text-danger' : 'text-success' }}">
                                                {{ $log->status_code ?? '—' }}
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">IP Address</label>
                                            <code>{{ $log->ip_address ?? '—' }}</code>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">URL</label>
                                            <code class="d-block" style="word-break:break-all;">{{ $log->url }}</code>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">User Agent</label>
                                            <p class="mb-0 fs-12">{{ $log->user_agent ?? '—' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Payload / Request Data</label>
                                            <pre class="bg-dark text-white p-3 rounded mb-0" style="max-height: 300px; overflow-y: auto; font-size: 0.75rem;"><code>{{ is_array($log->payload) ? json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ($log->payload ?? 'No payload data') }}</code></pre>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Created At</label>
                                            <strong>{{ $log->created_at->format('M d, Y H:i:s') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                      

                    @endforeach
                </tbody>
            </table>
              
        </div>
        <div class="p-3">{{ $logs->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-activity mb-2 d-block" style="font-size:40px;"></i>
            <p>No logs found.</p>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.log-row');
        
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                    return;
                }
                
                const logId = this.getAttribute('data-id');
                const detailsRow = document.getElementById(`details-${logId}`);
                const chevron = this.querySelector(`#icon-${logId}`);
                
                if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
                    document.querySelectorAll('.log-details').forEach(detail => {
                        detail.style.display = 'none';
                    });
                    document.querySelectorAll('.feather-chevron-right, .feather-chevron-down').forEach(icon => {
                        icon.className = 'feather-chevron-right';
                    });
                    
                    detailsRow.style.display = 'table-row';
                    if (chevron) {
                        chevron.className = 'feather-chevron-down';
                    }
                } else {
                    detailsRow.style.display = 'none';
                    if (chevron) {
                        chevron.className = 'feather-chevron-right';
                    }
                }
            });
        });
    });
</script>

@endif

@endsection