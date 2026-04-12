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
                <h2 class="fw-bold mb-0 text-primary">{{ $stats['total'] }}</h2>
                <small class="text-muted">All advertisements</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Active</p>
                <h2 class="fw-bold mb-0 text-success">{{ $stats['active'] }}</h2>
                <small class="text-muted">Currently running</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Pending</p>
                <h2 class="fw-bold mb-0 text-warning">{{ $stats['pending'] }}</h2>
                <small class="text-muted">Awaiting approval</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Spent</p>
                <h2 class="fw-bold mb-0 text-info">₦{{ number_format($stats['spent'], 2) }}</h2>
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
                    <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="paused"   {{ request('status') == 'paused'   ? 'selected' : '' }}>Paused</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="expired"  {{ request('status') == 'expired'  ? 'selected' : '' }}>Expired</option>
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
        @if($pro->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
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
                    </tr>
                </thead>
                <tbody>
                    @foreach($pro as $pr)
                    @php
                        $statusColors = [
                            'pending'  => '#ffc107',
                            'active'   => '#28a745',
                            'paused'   => '#17a2b8',
                            'rejected' => '#dc3545',
                            'expired'  => '#6c757d',
                        ];
                        $statusColor = $statusColors[$pr->status] ?? '#6c757d';
                    @endphp

                    {{-- Main Row --}}
                    <tr class="ad-row" data-id="{{ $pr->id }}" style="cursor: pointer;">

                        {{-- Chevron --}}
                        <td class="text-center">
                            <i class="feather-chevron-right" id="icon-{{ $pr->id }}" style="font-size: 16px; color: #6c757d;"></i>
                        </td>

                        {{-- Title / Media --}}
                        <td class="fw-semibold fs-13">
                            <div class="d-flex align-items-center gap-2">
                                @if($pr->media_url && $pr->media_type === 'image')
                                    <img src="{{ $pr->media_url }}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;" alt="">
                                @elseif($pr->media_url && $pr->media_type === 'video')
                                    <div style="width:40px;height:40px;background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;">
                                        <i class="feather-video" style="font-size:20px;"></i>
                                    </div>
                                @else
                                    <div style="width:40px;height:40px;background:#f0f0f0;border-radius:4px;display:flex;align-items:center;justify-content:center;">
                                        <i class="feather-image" style="font-size:20px;"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold">{{ Str::limit($pr->title, 30) }}</p>
                                    <small class="text-muted">Impressions: {{ number_format($pr->total_impressions ?? 0) }}</small>
                                </div>
                            </div>
                        </td>

                        {{-- Seller --}}
                        <td>
                            <p class="mb-0 fs-13">{{ $pr->seller->business_name ?? '—' }}</p>
                            <small class="text-muted">{{ $pr->seller->email ?? '' }}</small>
                        </td>

                        {{-- Category --}}
                        <td>
                            <span class="badge bg-light text-dark fs-11">
                                {{ $pr->adCategory->name ?? '—' }}
                            </span>
                        </td>

                        {{-- Slot --}}
                        <td class="fs-13">{{ $pr->bannerSlot->name ?? '—' }}</td>

                        {{-- Budget --}}
                        <td class="fw-bold text-success">₦{{ number_format($pr->budget, 2) }}</td>

                        {{-- Spent --}}
                        <td class="text-muted">₦{{ number_format($pr->amount_spent, 2) }}</td>

                        {{-- Status --}}
                        <td>
                            <span class="badge" style="
                                background-color: {{ $statusColor }};
                                color: {{ $pr->status === 'pending' ? '#212529' : '#ffffff' }};
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($pr->status) }}
                            </span>
                            @if($pr->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">{{ Str::limit($pr->rejection_reason, 30) }}</p>
                            @endif
                        </td>

                        {{-- Date --}}
                        <td class="text-muted fs-12">
                            {{ $pr->created_at->format('M d, Y') }}
                            @if($pr->start_date)
                                <br><small class="text-success">Start: {{ $pr->start_date->format('M d') }}</small>
                            @endif
                            @if($pr->end_date)
                                <br><small class="text-danger">End: {{ $pr->end_date->format('M d') }}</small>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.ads.show', $pr->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   onclick="event.stopPropagation();">
                                    <i class="feather-eye"></i>
                                </a>
                                @if($pr->status === 'pending' && auth('admin')->user()->canManageAds())
                                    <button type="button"
                                            class="btn btn-sm btn-outline-success"
                                            onclick="event.stopPropagation(); openApproveModal('{{ $pr->id }}', '{{ addslashes($pr->title) }}')">
                                        <i class="feather-check"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="event.stopPropagation(); openRejectModal('{{ $pr->id }}', '{{ addslashes($pr->title) }}')">
                                        <i class="feather-x"></i>
                                    </button>
                                @endif
                                @if($pr->status === 'active' && auth('admin')->user()->canManageAds())
                                    <button type="button"
                                            class="btn btn-sm btn-outline-warning"
                                            onclick="event.stopPropagation(); openSuspendModal('{{ $pr->id }}', '{{ addslashes($pr->title) }}')">
                                        <i class="feather-pause"></i>
                                    </button>
                                @endif
                            </div>
                        </td>

                    </tr>

                    {{-- Expandable Details Row --}}
                    <tr class="ad-details" id="details-{{ $pr->id }}" style="display: none;">
                        <td colspan="10" class="bg-light">
                            <div style="padding: 20px;">
                                <h6 class="mb-3 text-primary">
                                    <i class="feather-info me-2"></i>Full Ad Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Ad ID</label>
                                            <code>{{ $pr->id }}</code>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Title</label>
                                            <strong>{{ $pr->title }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Click URL</label>
                                            <a href="{{ $pr->click_url }}" target="_blank" class="fs-12">{{ Str::limit($pr->click_url, 40) }}</a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Media Type</label>
                                            <strong>{{ strtoupper($pr->media_type ?? 'N/A') }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Seller</label>
                                            <strong>{{ $pr->seller->business_name ?? '—' }}</strong>
                                            <small class="text-muted d-block">{{ $pr->seller->email ?? '' }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Ad Category</label>
                                            <strong>{{ $pr->adCategory->name ?? '—' }}</strong>
                                            <small class="text-muted d-block">Type: {{ $pr->adCategory->type ?? '—' }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Banner Slot</label>
                                            <strong>{{ $pr->bannerSlot->name ?? '—' }}</strong>
                                            <small class="text-muted d-block">Location: {{ $pr->bannerSlot->location ?? '—' }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Budget</label>
                                            <strong class="text-success">₦{{ number_format($pr->budget, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Amount Spent</label>
                                            <strong>₦{{ number_format($pr->amount_spent, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Cost Per Day / Click</label>
                                            <strong>₦{{ number_format($pr->cost_per_day ?? 0, 2) }} / ₦{{ number_format($pr->cost_per_click ?? 0, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Impressions</label>
                                            <strong>{{ number_format($pr->total_impressions ?? 0) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Clicks</label>
                                            <strong>{{ number_format($pr->total_clicks ?? 0) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Conversions</label>
                                            <strong>{{ number_format($pr->total_conversions ?? 0) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">CTR</label>
                                            <strong>
                                                @if(($pr->total_impressions ?? 0) > 0)
                                                    {{ round(($pr->total_clicks ?? 0) / ($pr->total_impressions ?? 0) * 100, 2) }}%
                                                @else
                                                    0%
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Start Date</label>
                                            <strong>{{ $pr->start_date ? $pr->start_date->format('M d, Y') : 'Not set' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">End Date</label>
                                            <strong>{{ $pr->end_date ? $pr->end_date->format('M d, Y') : 'Not set' }}</strong>
                                        </div>
                                    </div>
                                    @if($pr->media_url)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Media Preview</label>
                                            @if($pr->media_type === 'image')
                                                <img src="{{ $pr->media_url }}" style="max-width:100%;max-height:200px;border-radius:8px;" alt="">
                                            @elseif($pr->media_type === 'video')
                                                <video controls style="max-width:100%;max-height:200px;border-radius:8px;">
                                                    <source src="{{ $pr->media_url }}" type="video/mp4">
                                                </video>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                    @if($pr->rejection_reason)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-danger bg-opacity-10">
                                            <label class="fs-11 text-danger mb-1 d-block">Rejection Reason</label>
                                            <p class="mb-0 text-danger">{{ $pr->rejection_reason }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $pro->links() }}</div>
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
        from { opacity: 0; transform: translateY(-20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    // Expand/Collapse row functionality
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('.ad-row');

        rows.forEach(row => {
            row.addEventListener('click', function (e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }

                const adId       = this.getAttribute('data-id');
                const detailsRow = document.getElementById(`details-${adId}`);
                const chevron    = document.getElementById(`icon-${adId}`);
                const isOpen     = detailsRow.style.display === 'table-row';

                // Close all
                document.querySelectorAll('.ad-details').forEach(d => d.style.display = 'none');
                document.querySelectorAll('[id^="icon-"]').forEach(i => i.className = 'feather-chevron-right');

                // Toggle open
                if (!isOpen) {
                    detailsRow.style.display = 'table-row';
                    if (chevron) chevron.className = 'feather-chevron-down';
                }
            });
        });
    });

    // Approve Modal
    function openApproveModal(id, title) {
        document.getElementById('approveForm').action = `/admin/ads/${id}/approve`;
        document.getElementById('approveAdInfo').innerHTML = `<strong>${title}</strong><br>This ad will be approved and activated.`;
        document.getElementById('approveModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeApproveModal() {
        document.getElementById('approveModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Reject Modal
    function openRejectModal(id, title) {
        document.getElementById('rejectForm').action = `/admin/ads/${id}/reject`;
        document.getElementById('rejectAdInfo').innerHTML = `<strong>${title}</strong><br>Please provide a reason for rejection.`;
        document.getElementById('rejectModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Suspend Modal
    function openSuspendModal(id, title) {
        document.getElementById('suspendForm').action = `/admin/ads/${id}/suspend`;
        document.getElementById('suspendAdInfo').innerHTML = `<strong>${title}</strong><br>This ad will be paused.`;
        document.getElementById('suspendModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeSuspendModal() {
        document.getElementById('suspendModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close modals on outside click
    document.getElementById('approveModal').addEventListener('click', function (e) { if (e.target === this) closeApproveModal(); });
    document.getElementById('rejectModal').addEventListener('click',  function (e) { if (e.target === this) closeRejectModal();  });
    document.getElementById('suspendModal').addEventListener('click', function (e) { if (e.target === this) closeSuspendModal(); });
</script>

@endsection