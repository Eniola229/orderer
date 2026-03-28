@extends('layouts.admin')
@section('title', 'Ads Management')
@section('page_title', 'Advertisements')
@section('breadcrumb')
    <li class="breadcrumb-item active">Ads</li>
@endsection

@section('content')

<style>
    .ad-row:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .ad-details td {
        border-top: none !important;
    }
    .feather-chevron-right, .feather-chevron-down {
        transition: transform 0.3s ease;
    }
</style>

{{-- Stats Cards --}}
<div class="row mb-4">
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Ads</p>
                <h2 class="fw-bold mb-0 text-primary">{{ $ads->total() }}</h2>
                <small class="text-muted">All advertisements</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Active</p>
                <h2 class="fw-bold mb-0 text-success">{{ $ads->where('status', 'active')->count() }}</h2>
                <small class="text-muted">Currently running</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Pending</p>
                <h2 class="fw-bold mb-0 text-warning">{{ $ads->where('status', 'pending')->count() }}</h2>
                <small class="text-muted">Awaiting approval</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Spent</p>
                <h2 class="fw-bold mb-0 text-info">${{ number_format($ads->sum('amount_spent'), 2) }}</h2>
                <small class="text-muted">Across all ads</small>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.ads.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-12">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-12">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Title, seller..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <a href="{{ route('admin.ads.index') }}" class="btn btn-sm btn-outline-secondary w-100">
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
        @if($ads->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    践
                        <th class="fs-11 text-uppercase text-muted fw-semibold" width="5%"></th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Title / Media</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Category</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Slot</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Budget</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Spent</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                     </thead>
                <tbody>
                    @foreach($ads as $ad)
                    @php
                        $statusColors = [
                            'pending' => '#ffc107',
                            'active' => '#28a745',
                            'paused' => '#17a2b8',
                            'rejected' => '#dc3545',
                            'expired' => '#6c757d',
                        ];
                        $statusColor = $statusColors[$ad->status] ?? '#6c757d';
                    @endphp
                    <tr class="ad-row" data-id="{{ $ad->id }}" style="cursor: pointer;">
                        <td class="text-center">
                            <i class="feather-chevron-right" id="icon-{{ $ad->id }}" style="font-size: 16px; color: #6c757d;"></i>
                          
                        
                        <td class="fw-semibold fs-13">
                            <div class="d-flex align-items-center gap-2">
                                @if($ad->media_url && $ad->media_type === 'image')
                                <img src="{{ $ad->media_url }}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;" alt="">
                                @elseif($ad->media_url && $ad->media_type === 'video')
                                <div style="width:40px;height:40px;background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;">
                                    <i class="feather-video" style="font-size:20px;"></i>
                                </div>
                                @else
                                <div style="width:40px;height:40px;background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;">
                                    <i class="feather-image" style="font-size:20px;"></i>
                                </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold">{{ Str::limit($ad->title, 30) }}</p>
                                    <small class="text-muted">Impressions: {{ number_format($ad->total_impressions ?? 0) }}</small>
                                </div>
                            </div>
                        
                        
                        <td>
                            <p class="mb-0 fs-13">{{ $ad->seller->business_name ?? '—' }}</p>
                            <small class="text-muted">{{ $ad->seller->email ?? '' }}</small>
                        
                        
                        <td>
                            <span class="badge bg-light text-dark fs-11">
                                {{ $ad->adCategory->name ?? '—' }}
                            </span>
                        
                        
                        <td class="fs-13">{{ $ad->bannerSlot->name ?? '—' }}  
                        
                        <td class="fw-bold text-success">${{ number_format($ad->budget, 2) }}  
                        
                        <td class="text-muted">${{ number_format($ad->amount_spent, 2) }}  
                        
                        <td>
                            <span class="badge" style="
                                background-color: {{ $statusColor }};
                                color: {{ $ad->status === 'pending' ? '#212529' : '#ffffff' }};
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($ad->status) }}
                            </span>
                            @if($ad->rejection_reason)
                            <p class="fs-11 text-danger mb-0 mt-1">{{ Str::limit($ad->rejection_reason, 30) }}</p>
                            @endif
                        
                        
                        <td class="text-muted fs-12">
                            {{ $ad->created_at->format('M d, Y') }}
                            @if($ad->start_date)
                            <br>
                            <small class="text-success">Start: {{ $ad->start_date->format('M d') }}</small>
                            @endif
                            @if($ad->end_date)
                            <br>
                            <small class="text-danger">End: {{ $ad->end_date->format('M d') }}</small>
                            @endif
                        
                        
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.ads.show', $ad->id) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   onclick="event.stopPropagation();">
                                    <i class="feather-eye"></i>
                                </a>
                                @if($ad->status === 'pending' && auth('admin')->user()->canManageAds())
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success"
                                        onclick="event.stopPropagation(); openApproveModal('{{ $ad->id }}', '{{ addslashes($ad->title) }}')">
                                    <i class="feather-check"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="event.stopPropagation(); openRejectModal('{{ $ad->id }}', '{{ addslashes($ad->title) }}')">
                                    <i class="feather-x"></i>
                                </button>
                                @endif
                                @if($ad->status === 'active' && auth('admin')->user()->canManageAds())
                                <button type="button" 
                                        class="btn btn-sm btn-outline-warning"
                                        onclick="event.stopPropagation(); openSuspendModal('{{ $ad->id }}', '{{ addslashes($ad->title) }}')">
                                    <i class="feather-pause"></i>
                                </button>
                                @endif
                            </div>
                        
                      
                    
                    <tr class="ad-details" id="details-{{ $ad->id }}" style="display: none;">
                        <td colspan="10" class="bg-light">
                            <div style="padding: 20px;">
                                <h6 class="mb-3 text-primary">
                                    <i class="feather-info me-2"></i>Full Ad Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Ad ID</label>
                                            <code>{{ $ad->id }}</code>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Title</label>
                                            <strong>{{ $ad->title }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Click URL</label>
                                            <a href="{{ $ad->click_url }}" target="_blank" class="fs-12">{{ Str::limit($ad->click_url, 40) }}</a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Media Type</label>
                                            <strong>{{ strtoupper($ad->media_type ?? 'N/A') }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Seller</label>
                                            <strong>{{ $ad->seller->business_name ?? '—' }}</strong>
                                            <small class="text-muted d-block">{{ $ad->seller->email ?? '' }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Ad Category</label>
                                            <strong>{{ $ad->adCategory->name ?? '—' }}</strong>
                                            <small class="text-muted d-block">Type: {{ $ad->adCategory->type ?? '—' }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Banner Slot</label>
                                            <strong>{{ $ad->bannerSlot->name ?? '—' }}</strong>
                                            <small class="text-muted d-block">Location: {{ $ad->bannerSlot->location ?? '—' }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Budget</label>
                                            <strong class="text-success">${{ number_format($ad->budget, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Amount Spent</label>
                                            <strong>${{ number_format($ad->amount_spent, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Cost Per Day / Click</label>
                                            <strong>${{ number_format($ad->cost_per_day ?? 0, 2) }} / ${{ number_format($ad->cost_per_click ?? 0, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Impressions</label>
                                            <strong>{{ number_format($ad->total_impressions ?? 0) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Clicks</label>
                                            <strong>{{ number_format($ad->total_clicks ?? 0) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Conversions</label>
                                            <strong>{{ number_format($ad->total_conversions ?? 0) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">CTR</label>
                                            <strong>
                                                @if(($ad->total_impressions ?? 0) > 0)
                                                    {{ round(($ad->total_clicks ?? 0) / ($ad->total_impressions ?? 0) * 100, 2) }}%
                                                @else
                                                    0%
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Start Date</label>
                                            <strong>{{ $ad->start_date ? $ad->start_date->format('M d, Y') : 'Not set' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">End Date</label>
                                            <strong>{{ $ad->end_date ? $ad->end_date->format('M d, Y') : 'Not set' }}</strong>
                                        </div>
                                    </div>
                                    @if($ad->media_url)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Media Preview</label>
                                            @if($ad->media_type === 'image')
                                            <img src="{{ $ad->media_url }}" style="max-width:100%;max-height:200px;border-radius:8px;" alt="">
                                            @elseif($ad->media_type === 'video')
                                            <video controls style="max-width:100%;max-height:200px;border-radius:8px;">
                                                <source src="{{ $ad->media_url }}" type="video/mp4">
                                            </video>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    @if($ad->rejection_reason)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-danger bg-opacity-10">
                                            <label class="fs-11 text-danger mb-1 d-block">Rejection Reason</label>
                                            <p class="mb-0 text-danger">{{ $ad->rejection_reason }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                          
                      
                    @endforeach
                </tbody>
              
        </div>
        <div class="p-3">{{ $ads->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-trending-up mb-2 d-block" style="font-size:40px;"></i>
            <p>No ads found.</p>
        </div>
        @endif
    </div>
</div>

{{-- Approve Modal --}}
<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Approve Ad</h5>
        </div>
        <form id="approveForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="approveAdInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Notes (Optional)</label>
                    <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Add any notes about this approval..."></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Approve Ad</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Reject Ad</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="rejectAdInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Rejection Reason</label>
                    <textarea name="rejection_reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Why is this ad being rejected?" required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Reject Ad</button>
            </div>
        </form>
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Suspend Ad</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="suspendAdInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Reason (Optional)</label>
                    <textarea name="reason" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Reason for suspension..."></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #ffc107; color: #212529; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Suspend Ad</button>
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
    // Expand/Collapse row functionality
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.ad-row');
        
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                    return;
                }
                
                const adId = this.getAttribute('data-id');
                const detailsRow = document.getElementById(`details-${adId}`);
                const chevron = this.querySelector(`#icon-${adId}`);
                
                if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
                    document.querySelectorAll('.ad-details').forEach(detail => {
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
    
    // Approve Modal Functions
    function openApproveModal(id, title) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        const adInfo = document.getElementById('approveAdInfo');
        
        form.action = `/admin/ads/${id}/approve`;
        adInfo.innerHTML = `<strong>${title}</strong><br>This ad will be approved and activated.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Reject Modal Functions
    function openRejectModal(id, title) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const adInfo = document.getElementById('rejectAdInfo');
        
        form.action = `/admin/ads/${id}/reject`;
        adInfo.innerHTML = `<strong>${title}</strong><br>Please provide a reason for rejection.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Suspend Modal Functions
    function openSuspendModal(id, title) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const adInfo = document.getElementById('suspendAdInfo');
        
        form.action = `/admin/ads/${id}/suspend`;
        adInfo.innerHTML = `<strong>${title}</strong><br>This ad will be paused.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeSuspendModal() {
        const modal = document.getElementById('suspendModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close modals when clicking outside
    document.getElementById('approveModal').addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
    document.getElementById('suspendModal').addEventListener('click', function(e) {
        if (e.target === this) closeSuspendModal();
    });
</script>

@endsection