@extends('layouts.admin')
@section('title', 'Services')
@section('page_title', 'Service Management')
@section('breadcrumb')
    <li class="breadcrumb-item active">Services</li>
@endsection

@section('content')

<style>
    .service-row:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .service-details td {
        border-top: none !important;
        padding: 0 !important;
    }
    .feather-chevron-right, .feather-chevron-down {
        transition: transform 0.3s ease;
    }
    .portfolio-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .portfolio-images img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        cursor: pointer;
    }
</style>

<div class="row mb-4">
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Services</p>
                <h2 class="fw-bold mb-0 text-primary">{{ $services->total() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Pending</p>
                <h2 class="fw-bold mb-0 text-warning">{{ $services->where('status', 'pending')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Approved</p>
                <h2 class="fw-bold mb-0 text-success">{{ $services->where('status', 'approved')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Rejected</p>
                <h2 class="fw-bold mb-0 text-danger">{{ $services->where('status', 'rejected')->count() }}</h2>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.services.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-12">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold fs-12">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Service title, seller name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary w-100">
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
        @if($services->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    践
                        <th class="fs-11 text-uppercase text-muted fw-semibold" width="5%"></th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Service</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Category</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Delivery</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                     </thead>
                <tbody>
                    @foreach($services as $service)
                    @php
                        $statusColors = [
                            'pending' => '#ffc107',
                            'approved' => '#28a745',
                            'rejected' => '#dc3545',
                            'suspended' => '#6c757d',
                        ];
                        $statusColor = $statusColors[$service->status] ?? '#6c757d';
                    @endphp
                    <tr class="service-row" data-id="{{ $service->id }}" style="cursor: pointer;">
                        <td class="text-center">
                            <i class="feather-chevron-right" id="icon-{{ $service->id }}" style="font-size: 16px; color: #6c757d;"></i>
                          
                        
                        <td>
                            <p class="mb-0 fw-semibold fs-13">{{ Str::limit($service->title, 40) }}</p>
                            @if($service->location)
                                <small class="text-muted">
                                    <i class="feather-map-pin me-1"></i>{{ $service->location }}
                                </small>
                            @endif
                        
                        
                        <td>
                            <p class="mb-0 fs-13">{{ $service->seller->business_name ?? '—' }}</p>
                            <small class="text-muted">{{ $service->seller->email ?? '' }}</small>
                        
                        
                        <td class="fs-13 text-muted">{{ $service->category->name ?? '—' }}  
                        
                        <td>
                            @if($service->pricing_type === 'negotiable')
                                <span class="text-muted fs-13">Negotiable</span>
                            @else
                                <span class="fw-bold text-success">
                                    ${{ number_format($service->price, 2) }}
                                    @if($service->pricing_type === 'hourly')
                                        <small class="text-muted fw-normal">/hr</small>
                                    @endif
                                </span>
                            @endif
                        
                        
                        <td class="fs-13 text-muted">{{ $service->delivery_time ?? '—' }}  
                        
                        <td>
                            <span class="badge" style="
                                background-color: {{ $statusColor }};
                                color: {{ $service->status === 'pending' ? '#212529' : '#ffffff' }};
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($service->status) }}
                            </span>
                            @if($service->status === 'rejected' && $service->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">{{ Str::limit($service->rejection_reason, 40) }}</p>
                            @endif
                        
                        
                        <td class="text-muted fs-12">{{ $service->created_at->format('M d, Y') }}  
                        
                        <td>
                            <div class="d-flex gap-1" onclick="event.stopPropagation()">
                                <a href="{{ route('admin.services.show', $service->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="feather-eye"></i>
                                </a>
                                @if($service->status === 'pending' && auth('admin')->user()->canModerateSellers())
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success"
                                        onclick="openApproveModal('{{ $service->id }}', '{{ addslashes($service->title) }}')">
                                    <i class="feather-check"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="openRejectModal('{{ $service->id }}', '{{ addslashes($service->title) }}')">
                                    <i class="feather-x"></i>
                                </button>
                                @endif
                                @if($service->status === 'approved' && auth('admin')->user()->canModerateSellers())
                                <button type="button" 
                                        class="btn btn-sm btn-outline-warning"
                                        onclick="openSuspendModal('{{ $service->id }}', '{{ addslashes($service->title) }}')">
                                    <i class="feather-pause"></i>
                                </button>
                                @endif
                            </div>
                        
                      
                    
                    <tr class="service-details" id="details-{{ $service->id }}" style="display: none;">
                        <td colspan="9" class="bg-light p-0">
                            <div style="padding: 20px;">
                                <h6 class="mb-3 text-primary">
                                    <i class="feather-info me-2"></i>Service Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Title</label>
                                            <strong>{{ $service->title }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Seller</label>
                                            <strong>{{ $service->seller->business_name ?? '—' }}</strong>
                                            <small class="text-muted d-block">{{ $service->seller->email ?? '' }}</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Description</label>
                                            <p class="mb-0">{{ $service->description ?? 'No description' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Category</label>
                                            <strong>{{ $service->category->name ?? '—' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Pricing Type</label>
                                            <strong>{{ ucfirst($service->pricing_type) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Price</label>
                                            <strong class="text-success">
                                                @if($service->pricing_type === 'negotiable')
                                                    Negotiable
                                                @else
                                                    ${{ number_format($service->price, 2) }}
                                                    @if($service->pricing_type === 'hourly')
                                                        /hour
                                                    @endif
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Delivery Time</label>
                                            <strong>{{ $service->delivery_time ?? 'Not specified' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Location</label>
                                            <strong>{{ $service->location ?? 'Not specified' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Revisions</label>
                                            <strong>{{ $service->revisions ?? 'Not specified' }}</strong>
                                        </div>
                                    </div>
                                    
                                    @if($service->portfolio_images && count($service->portfolio_images) > 0)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-2 d-block">Portfolio Images</label>
                                            <div class="portfolio-images">
                                                @foreach($service->portfolio_images as $image)
                                                    <img src="{{ $image['url'] }}" 
                                                         alt="Portfolio image"
                                                         onclick="window.open('{{ $image['url'] }}', '_blank')">
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($service->status === 'rejected' && $service->rejection_reason)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-danger bg-opacity-10">
                                            <label class="fs-11 text-danger mb-1 d-block">Rejection Reason</label>
                                            <p class="mb-0 text-danger">{{ $service->rejection_reason }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        
                      
                    @endforeach
                </tbody>
              
        </div>
        <div class="p-3">{{ $services->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-settings mb-3 d-block" style="font-size:40px;"></i>
            <p>No services found.</p>
        </div>
        @endif
    </div>
</div>

{{-- Approve Modal --}}
<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Approve Service</h5>
        </div>
        <form id="approveForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="approveServiceInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Approve Service</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Reject Service</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="rejectServiceInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Rejection Reason</label>
                    <textarea name="reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Why is this service being rejected?" required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Reject Service</button>
            </div>
        </form>
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Suspend Service</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="suspendServiceInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #ffc107; color: #212529; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Suspend Service</button>
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
    // Expand/Collapse functionality
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.service-row');
        
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('.btn')) {
                    return;
                }
                
                const serviceId = this.getAttribute('data-id');
                const detailsRow = document.getElementById(`details-${serviceId}`);
                const chevron = this.querySelector(`#icon-${serviceId}`);
                
                if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
                    document.querySelectorAll('.service-details').forEach(detail => {
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
    function openApproveModal(id, serviceName) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        const serviceInfo = document.getElementById('approveServiceInfo');
        
        form.action = `/admin/services/${id}/approve`;
        serviceInfo.innerHTML = `<strong>${serviceName}</strong><br>This service will be approved and become visible to buyers.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Reject Modal Functions
    function openRejectModal(id, serviceName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const serviceInfo = document.getElementById('rejectServiceInfo');
        
        form.action = `/admin/services/${id}/reject`;
        serviceInfo.innerHTML = `<strong>${serviceName}</strong><br>Please provide a reason for rejection.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Suspend Modal Functions
    function openSuspendModal(id, serviceName) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const serviceInfo = document.getElementById('suspendServiceInfo');
        
        form.action = `/admin/services/${id}/suspend`;
        serviceInfo.innerHTML = `<strong>${serviceName}</strong><br>This service will be suspended and hidden from buyers.`;
        
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