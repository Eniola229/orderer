@extends('layouts.admin')
@section('title', 'Properties')
@section('page_title', 'Property Management')
@section('breadcrumb')
    <li class="breadcrumb-item active">Properties</li>
@endsection

@section('content')

<style>
    .house-row:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .house-details td {
        border-top: none !important;
        padding: 0 !important;
    }
    .feather-chevron-right, .feather-chevron-down {
        transition: transform 0.3s ease;
    }
    .property-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .property-images img {
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
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Properties</p>
                <h2 class="fw-bold mb-0 text-primary">{{ $houses->total() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Pending</p>
                <h2 class="fw-bold mb-0 text-warning">{{ $houses->where('status', 'pending')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Approved</p>
                <h2 class="fw-bold mb-0 text-success">{{ $houses->where('status', 'approved')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Rejected</p>
                <h2 class="fw-bold mb-0 text-danger">{{ $houses->where('status', 'rejected')->count() }}</h2>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.houses.index') }}" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Listing Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>For Sale</option>
                    <option value="rent" {{ request('type') == 'rent' ? 'selected' : '' }}>For Rent</option>
                    <option value="shortlet" {{ request('type') == 'shortlet' ? 'selected' : '' }}>Shortlet</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Property Type</label>
                <select name="property_type" class="form-select form-select-sm">
                    <option value="">All Properties</option>
                    <option value="apartment" {{ request('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                    <option value="house" {{ request('property_type') == 'house' ? 'selected' : '' }}>House</option>
                    <option value="land" {{ request('property_type') == 'land' ? 'selected' : '' }}>Land</option>
                    <option value="commercial" {{ request('property_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                    <option value="shortlet" {{ request('property_type') == 'shortlet' ? 'selected' : '' }}>Shortlet</option>
                    <option value="other" {{ request('property_type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold fs-12">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Title, location, seller..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <a href="{{ route('admin.houses.index') }}" class="btn btn-sm btn-outline-secondary w-100">
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
        @if($houses->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    
                        <th class="fs-11 text-uppercase text-muted fw-semibold" width="5%"></th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Property</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Location</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                     </thead>
                <tbody>
                    @foreach($houses as $house)
                    @php
                        $statusColors = [
                            'pending' => '#ffc107',
                            'approved' => '#28a745',
                            'rejected' => '#dc3545',
                            'suspended' => '#6c757d',
                        ];
                        $statusColor = $statusColors[$house->status] ?? '#6c757d';
                    @endphp
                    <tr class="house-row" data-id="{{ $house->id }}" style="cursor: pointer;">
                        <td class="text-center">
                            <i class="feather-chevron-right" id="icon-{{ $house->id }}" style="font-size: 16px; color: #6c757d;"></i>
                          
                        
                        <td class="fw-semibold fs-13">
                            <p class="mb-0">{{ Str::limit($house->title, 40) }}</p>
                            @if($house->bedrooms)
                                <small class="text-muted">
                                    <i class="feather-home me-1"></i>{{ $house->bedrooms }} bed, {{ $house->bathrooms }} bath
                                </small>
                            @endif
                        
                        
                        <td>
                            <p class="mb-0 fs-13">{{ $house->seller->business_name ?? '—' }}</p>
                            <small class="text-muted">{{ $house->seller->email ?? '' }}</small>
                        
                        
                        <td>
                            <span class="badge bg-light text-dark fs-11">
                                {{ ucfirst($house->listing_type) }} / {{ ucfirst($house->property_type) }}
                            </span>
                        
                        
                        <td class="fw-bold text-success">
                            @if($house->listing_type === 'rent')
                                ₦{{ number_format($house->price, 2) }}/month
                            @else
                                ₦{{ number_format($house->price, 2) }}
                            @endif
                        
                        
                        <td class="fs-13 text-muted">
                            {{ Str::limit($house->location, 30) }}
                            @if($house->city)
                                <br><small class="text-muted">{{ $house->city }}, {{ $house->state }}</small>
                            @endif
                        
                        
                        <td>
                            <span class="badge" style="
                                background-color: {{ $statusColor }};
                                color: {{ $house->status === 'pending' ? '#212529' : '#ffffff' }};
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($house->status) }}
                            </span>
                            @if($house->status === 'rejected' && $house->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">{{ Str::limit($house->rejection_reason, 40) }}</p>
                            @endif
                        
                        
                        <td class="text-muted fs-12">{{ $house->created_at->format('M d, Y') }}  
                        
                        <td>
                            <div class="d-flex gap-1" onclick="event.stopPropagation()">
                                <a href="{{ route('admin.houses.show', $house->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="feather-eye"></i>
                                </a>
                                @if($house->status === 'pending' && auth('admin')->user()->canModerateSellers())
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success"
                                        onclick="openApproveModal('{{ $house->id }}', '{{ addslashes($house->title) }}')">
                                    <i class="feather-check"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="openRejectModal('{{ $house->id }}', '{{ addslashes($house->title) }}')">
                                    <i class="feather-x"></i>
                                </button>
                                @endif
                                @if($house->status === 'approved' && auth('admin')->user()->canModerateSellers())
                                <button type="button" 
                                        class="btn btn-sm btn-outline-warning"
                                        onclick="openSuspendModal('{{ $house->id }}', '{{ addslashes($house->title) }}')">
                                    <i class="feather-pause"></i>
                                </button>
                                @endif
                            </div>
                        
                      
                    
                    <tr class="house-details" id="details-{{ $house->id }}" style="display: none;">
                        <td colspan="9" class="bg-light p-0">
                            <div style="padding: 20px;">
                                <h6 class="mb-3 text-primary">
                                    <i class="feather-info me-2"></i>Property Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Title</label>
                                            <strong>{{ $house->title }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Seller</label>
                                            <strong>{{ $house->seller->business_name ?? '—' }}</strong>
                                            <small class="text-muted d-block">{{ $house->seller->email ?? '' }}</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Description</label>
                                            <p class="mb-0">{{ $house->description ?? 'No description' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Listing Type</label>
                                            <strong>{{ ucfirst($house->listing_type) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Property Type</label>
                                            <strong>{{ ucfirst($house->property_type) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Price</label>
                                            <strong class="text-success">
                                                @if($house->listing_type === 'rent')
                                                    ${{ number_format($house->price, 2) }}/month
                                                @else
                                                    ${{ number_format($house->price, 2) }}
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Location</label>
                                            <strong>{{ $house->location }}</strong>
                                            <small class="text-muted d-block">{{ $house->city }}, {{ $house->state }}, {{ $house->country }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Bedrooms</label>
                                            <strong>{{ $house->bedrooms ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Bathrooms</label>
                                            <strong>{{ $house->bathrooms ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Toilets</label>
                                            <strong>{{ $house->toilets ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Size (sqm)</label>
                                            <strong>{{ $house->size_sqm ?? 'N/A' }} sqm</strong>
                                        </div>
                                    </div>
                                    
                                    @if($house->features && count($house->features) > 0)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-2 d-block">Features</label>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($house->features as $feature)
                                                <span class="badge bg-light text-dark">{{ $feature }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($house->video_tour_url)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Video Tour</label>
                                            <a href="{{ $house->video_tour_url }}" target="_blank" class="text-primary">
                                                {{ $house->video_tour_url }}
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($house->images->count() > 0)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-2 d-block">Property Images</label>
                                            <div class="property-images">
                                                @foreach($house->images as $image)
                                                    <img src="{{ $image->image_url }}" 
                                                         alt="Property image"
                                                         onclick="window.open('{{ $image->image_url }}', '_blank')">
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($house->status === 'rejected' && $house->rejection_reason)
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-danger bg-opacity-10">
                                            <label class="fs-11 text-danger mb-1 d-block">Rejection Reason</label>
                                            <p class="mb-0 text-danger">{{ $house->rejection_reason }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        
                      
                    @endforeach
                </tbody>
              </table>
        </div>
        <div class="p-3">{{ $houses->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-home mb-3 d-block" style="font-size:40px;"></i>
            <p>No properties found.</p>
        </div>
        @endif
    </div>
</div>

{{-- Approve Modal --}}
<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Approve Property</h5>
        </div>
        <form id="approveForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="approvePropertyInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Approve Property</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Reject Property</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="rejectPropertyInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Rejection Reason</label>
                    <textarea name="reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Why is this property being rejected?" required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Reject Property</button>
            </div>
        </form>
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Suspend Property</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="suspendPropertyInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #ffc107; color: #212529; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Suspend Property</button>
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
        const rows = document.querySelectorAll('.house-row');
        
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('.btn')) {
                    return;
                }
                
                const houseId = this.getAttribute('data-id');
                const detailsRow = document.getElementById(`details-${houseId}`);
                const chevron = this.querySelector(`#icon-${houseId}`);
                
                if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
                    document.querySelectorAll('.house-details').forEach(detail => {
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
    function openApproveModal(id, propertyName) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        const propertyInfo = document.getElementById('approvePropertyInfo');
        
        form.action = `/admin/houses/${id}/approve`;
        propertyInfo.innerHTML = `<strong>${propertyName}</strong><br>This property will be approved and become visible to buyers.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Reject Modal Functions
    function openRejectModal(id, propertyName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const propertyInfo = document.getElementById('rejectPropertyInfo');
        
        form.action = `/admin/houses/${id}/reject`;
        propertyInfo.innerHTML = `<strong>${propertyName}</strong><br>Please provide a reason for rejection.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Suspend Modal Functions
    function openSuspendModal(id, propertyName) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const propertyInfo = document.getElementById('suspendPropertyInfo');
        
        form.action = `/admin/houses/${id}/suspend`;
        propertyInfo.innerHTML = `<strong>${propertyName}</strong><br>This property will be suspended and hidden from buyers.`;
        
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