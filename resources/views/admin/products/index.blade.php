@extends('layouts.admin')
@section('title', 'Products')
@section('page_title', 'Product Management')
@section('breadcrumb')
    <li class="breadcrumb-item active">Products</li>
@endsection

@section('content')

<style>
    .product-row:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .product-details td {
        border-top: none !important;
        padding: 0 !important;
    } 
    .feather-chevron-right, .feather-chevron-down {
        transition: transform 0.3s ease;
    }
</style>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Featured Products</p>
                <h2 class="fw-bold mb-0 text-warning">{{ number_format($featuredCount) }}</h2>
                <a href="{{ route('admin.products.index', ['featured' => 'yes']) }}" 
                   class="fs-12 text-muted">View featured →</a>
            </div>
        </div>
    </div>
</div>
{{-- Advanced Filters Card --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="feather-filter me-2"></i>Advanced Filters
        </h5>
        <div class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search product, SKU, seller..." value="{{ request('search') }}"
                   style="width:280px;" id="searchInput">
            <button type="button" class="btn btn-sm btn-outline-primary" 
                    onclick="window.location.href='{{ route('admin.products.index') }}?search='+encodeURIComponent(document.getElementById('searchInput').value)">
                <i class="feather-search"></i> Search
            </button>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="all">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Category</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Stock Status</label>
                <select name="stock_status" class="form-select form-select-sm">
                    <option value="">All Stock</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock (≤5)</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>

            <div class="col-md-2">
                    <label class="form-label fw-semibold fs-12">Featured</label>
                    <select name="featured" class="form-select form-select-sm">
                        <option value="">All Products</option>
                        <option value="yes" {{ request('featured') === 'yes' ? 'selected' : '' }}>Featured Only</option>
                    </select>
                </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Min Price</label>
                <input type="number" name="min_price" class="form-control form-control-sm" 
                       value="{{ request('min_price') }}" placeholder="₦0">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Max Price</label>
                <input type="number" name="max_price" class="form-control form-control-sm" 
                       value="{{ request('max_price') }}" placeholder="₦9999">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="feather-x"></i> Clear
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="feather-filter"></i> Apply
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($products->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    
                        <th class="fs-11 text-uppercase text-muted fw-semibold" width="5%"></th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Product</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Category</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Stock</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                     </thead>
                <tbody>
                    @foreach($products as $product)
                    @php 
                        $img = $product->images->where('is_primary',true)->first() ?? $product->images->first();
                        $statusColors = [
                            'pending' => '#ffc107',
                            'approved' => '#28a745',
                            'rejected' => '#dc3545',
                            'suspended' => '#6c757d',
                        ];
                        $statusColor = $statusColors[$product->status] ?? '#6c757d';
                    @endphp
                    <tr class="product-row" data-id="{{ $product->id }}" style="cursor: pointer;">
                        <td class="text-center">
                            <i class="feather-chevron-right" id="icon-{{ $product->id }}" style="font-size: 16px; color: #6c757d;"></i>
                        
                        
                        <td class="fw-semibold fs-13">
                            <div class="d-flex align-items-center gap-2">
                                @if($img)
                                <img src="{{ $img->image_url }}"
                                     style="width:40px;height:40px;object-fit:cover;border-radius:6px;" alt="">
                                @else
                                <div style="width:40px;height:40px;background:#f5f5f5;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                    <i class="feather-image text-muted" style="font-size:14px;"></i>
                                </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold fs-13">{{ Str::limit($product->name, 35) }}</p>
                                    @if($product->sku)
                                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                                    @endif
                                </div>
                            </div>
                        
                        
                        <td class="fs-13">
                            <a href="{{ route('admin.sellers.show', $product->seller_id) }}"
                               class="text-primary">
                                {{ $product->seller->business_name ?? '—' }}
                            </a>
                        
                        
                        <td class="fs-13 text-muted">{{ $product->category->name ?? '—' }}  
                        
                        <td>
                            @if($product->sale_price)
                                <small class="text-muted text-decoration-line-through d-block">
                                    ₦{{ number_format($product->price, 2) }}
                                </small>
                                <span class="fw-bold text-success">
                                    ₦{{ number_format($product->sale_price, 2) }}
                                </span>
                            @else
                                <span class="fw-bold">₦{{ number_format($product->price, 2) }}</span>
                            @endif
                        
                        
                        <td class="fw-semibold {{ $product->stock <= 5 ? 'text-danger' : '' }}">
                            {{ $product->stock }}
                            @if($product->stock <= 5 && $product->stock > 0)
                                <small class="text-warning d-block">Low stock</small>
                            @endif
                        
                        
                        <td>
                            <span class="badge" style="
                                background-color: {{ $statusColor }};
                                color: {{ $product->status === 'pending' ? '#212529' : '#ffffff' }};
                                padding: 5px 10px;
                                border-radius: 4px;
                                font-size: 12px;
                                font-weight: 600;
                            ">
                                {{ ucfirst($product->status) }}
                            </span>
                            @if($product->status === 'rejected' && $product->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">
                                    {{ Str::limit($product->rejection_reason, 40) }}
                                </p>
                            @endif
                        
                        
                        <td class="text-muted fs-12">{{ $product->created_at->format('M d, Y') }}  
                        
                        <td>
                            <div class="d-flex gap-1" onclick="event.stopPropagation()">
                                <a href="{{ route('admin.products.show', $product) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="feather-eye"></i>
                                </a>
                                @if($product->status === 'pending' && auth('admin')->user()->canModerateSellers())
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success"
                                        onclick="openApproveModal('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                                    <i class="feather-check"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="openRejectModal('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                                    <i class="feather-x"></i>
                                </button>
                                @endif
                                @if($product->status === 'approved' && auth('admin')->user()->canModerateSellers())
                                <button type="button" 
                                        class="btn btn-sm btn-outline-warning"
                                        onclick="openSuspendModal('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                                    <i class="feather-pause"></i>
                                </button>
                                @endif
                            </div>
                        
                      
                    
                    <tr class="product-details" id="details-{{ $product->id }}" style="display: none;">
                        <td colspan="9" class="bg-light p-0">
                            <div style="padding: 20px;">
                                <h6 class="mb-3 text-primary">
                                    <i class="feather-info me-2"></i>Product Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Product Name</label>
                                            <strong>{{ $product->name }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">SKU</label>
                                            <code>{{ $product->sku ?? 'N/A' }}</code>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Description</label>
                                            <p class="mb-0">{{ $product->description ?? 'No description' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Category</label>
                                            <strong>{{ $product->category->name ?? '—' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Subcategory</label>
                                            <strong>{{ $product->subcategory->name ?? '—' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Condition</label>
                                            <strong>{{ ucfirst($product->condition) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Price</label>
                                            <strong class="text-success">₦{{ number_format($product->price, 2) }}</strong>
                                            @if($product->sale_price)
                                                <br><small class="text-muted">Sale: ₦{{ number_format($product->sale_price, 2) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Stock</label>
                                            <strong class="{{ $product->stock <= 5 ? 'text-danger' : '' }}">{{ $product->stock }} units</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Weight</label>
                                            <strong>{{ $product->weight_kg ?? 'N/A' }} kg</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Location</label>
                                            <strong>{{ $product->location ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Total Sold</label>
                                            <strong>{{ $product->total_sold ?? 0 }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Views</label>
                                            <strong>{{ $product->views ?? 0 }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-2 d-block">Images</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($product->images as $image)
                                                <img src="{{ $image->image_url }}" 
                                                     style="width:60px;height:60px;object-fit:cover;border-radius:6px;" 
                                                     onclick="window.open('{{ $image->image_url }}', '_blank')"
                                                     style="cursor: pointer;">
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                      
                    @endforeach
                </tbody>
              
        </div>
    </table>
        <div class="p-3">{{ $products->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-package mb-2 d-block" style="font-size:40px;"></i>
            <p>No products found.</p>
        </div>
        @endif
    </div>
</div>

{{-- Modals (same as before but with proper styling) --}}
<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Approve Product</h5>
        </div>
        <form id="approveForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="approveProductInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Approve Product</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Reject Product</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="rejectProductInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Rejection Reason</label>
                    <textarea name="reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Why is this product being rejected?" required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Reject Product</button>
            </div>
        </form>
    </div>
</div>

<div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Suspend Product</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="suspendProductInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #ffc107; color: #212529; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Suspend Product</button>
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
        const rows = document.querySelectorAll('.product-row');
        
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('.btn')) {
                    return;
                }
                
                const productId = this.getAttribute('data-id');
                const detailsRow = document.getElementById(`details-${productId}`);
                const chevron = this.querySelector(`#icon-${productId}`);
                
                if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
                    document.querySelectorAll('.product-details').forEach(detail => {
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
    
    // Modal functions
    function openApproveModal(id, productName) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        const info = document.getElementById('approveProductInfo');
        
        form.action = `/admin/products/${id}/approve`;
        info.innerHTML = `<strong>${productName}</strong><br>This product will be approved and become visible to buyers.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function openRejectModal(id, productName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const info = document.getElementById('rejectProductInfo');
        
        form.action = `/admin/products/${id}/reject`;
        info.innerHTML = `<strong>${productName}</strong><br>Please provide a reason for rejection.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function openSuspendModal(id, productName) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const info = document.getElementById('suspendProductInfo');
        
        form.action = `/admin/products/${id}/suspend`;
        info.innerHTML = `<strong>${productName}</strong><br>This product will be suspended and hidden from buyers.`;
        
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