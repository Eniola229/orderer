@extends('layouts.admin')
@section('title', 'Categories')
@section('page_title', 'Categories & Subcategories')
@section('breadcrumb')
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')

<style>
    .category-row:hover {
        background-color: #f8f9fa;
    }
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

<div class="row">
    <div class="col-lg-8">

        {{-- Categories table --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">All Categories</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Name</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Slug</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Commission</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Products</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Subcategories</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                             </thead>
                        <tbody>
                            @foreach($categories as $cat)
                            <tr class="category-row">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($cat->icon)
                                        <i class="{{ $cat->icon }}" style="color:#2ECC71;font-size:18px;"></i>
                                        @endif
                                        <strong class="fs-13">{{ $cat->name }}</strong>
                                    </div>
                                </td>
                                <td><code class="fs-12">{{ $cat->slug }}</code></td>
                                <td class="fw-semibold">{{ $cat->commission_rate ?? 10 }}%</td>
                                <td class="fw-semibold">{{ $cat->products_count ?? 0 }}</td>
                                <td class="fw-semibold">{{ $cat->subcategories_count ?? 0 }}</td>
                                <td>
                                    <span class="badge orderer-badge {{ $cat->is_active ? 'badge-approved' : 'badge-rejected' }}">
                                        {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button"
                                                class="btn btn-xs btn-outline-primary"
                                                style="font-size:11px;padding:2px 8px;"
                                                onclick="openEditModal('{{ $cat->id }}', '{{ addslashes($cat->name) }}', '{{ $cat->commission_rate ?? 10 }}', '{{ $cat->icon }}', '{{ $cat->is_active }}')">
                                            Edit
                                        </button>
                                        <button type="button"
                                                class="btn btn-xs btn-outline-success"
                                                style="font-size:11px;padding:2px 8px;"
                                                onclick="openSubModal('{{ $cat->id }}', '{{ addslashes($cat->name) }}')">
                                            + Sub
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Subcategories row --}}
                            @if($cat->subcategories && $cat->subcategories->count())
                            <tr style="background:#fafafa;">
                                <td colspan="7" class="py-2 px-4">
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($cat->subcategories as $sub)
                                        <span style="background:#EBF5FB;color:#1A5276;padding:2px 12px;border-radius:12px;font-size:12px;font-weight:600;">
                                            {{ $sub->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Create category sidebar --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New Category</h5>
            </div>
            <div class="card-body">

                @if(!auth('admin')->user()->canManageCategories())
                <div class="alert alert-warning">
                    <i class="feather-lock me-2"></i>
                    You don't have permission to manage categories.
                </div>
                @else
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control"
                               placeholder="e.g. Electronics" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Commission Rate (%)</label>
                        <input type="number" name="commission_rate" class="form-control"
                               value="10" min="0" max="50" step="0.1">
                        <small class="text-muted">
                            % deducted from each sale in this category.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Icon (Feather icon class)</label>
                        <input type="text" name="icon" class="form-control"
                               placeholder="feather-tag">
                        <small class="text-muted">
                            e.g. feather-smartphone, feather-home, feather-tool
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="feather-plus me-2"></i> Create Category
                    </button>
                </form>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- Custom Edit Category Modal --}}
<div id="editCategoryModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Edit Category</h5>
        </div>
        <form id="editCategoryForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Name</label>
                    <input type="text" name="name" id="editCategoryName" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" required>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Commission Rate (%)</label>
                    <input type="number" name="commission_rate" id="editCommissionRate" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" min="0" max="50" step="0.1">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Icon class</label>
                    <input type="text" name="icon" id="editIcon" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="feather-tag">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" id="editIsActive" value="1">
                        <span style="font-weight: 500;">Active</span>
                    </label>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeEditModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #2ECC71; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- Custom Add Subcategory Modal --}}
<div id="addSubcategoryModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;" id="addSubcategoryTitle">Add Subcategory</h5>
        </div>
        <form id="addSubcategoryForm" method="POST" action="">
            @csrf
            <div style="padding: 20px;">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Subcategory Name</label>
                    <input type="text" name="name" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="e.g. Smartphones" required>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSubModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #2ECC71; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Add Subcategory</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name, commissionRate, icon, isActive) {
        const modal = document.getElementById('editCategoryModal');
        const form = document.getElementById('editCategoryForm');
        const nameInput = document.getElementById('editCategoryName');
        const commissionInput = document.getElementById('editCommissionRate');
        const iconInput = document.getElementById('editIcon');
        const isActiveCheckbox = document.getElementById('editIsActive');
        
        form.action = "{{ route('admin.categories.update', ['category' => '__ID__']) }}".replace('__ID__', id);

        nameInput.value = name;
        commissionInput.value = commissionRate;
        iconInput.value = icon || '';
        isActiveCheckbox.checked = isActive == '1' || isActive == 1 || isActive === true;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeEditModal() {
        const modal = document.getElementById('editCategoryModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function openSubModal(id, categoryName) {
        const modal = document.getElementById('addSubcategoryModal');
        const form = document.getElementById('addSubcategoryForm');
        const title = document.getElementById('addSubcategoryTitle');
        
        form.action = "{{ route('admin.categories.subcategory', ['category' => '__ID__']) }}".replace('__ID__', id);
        title.innerHTML = `Add Subcategory to ${categoryName}`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeSubModal() {
        const modal = document.getElementById('addSubcategoryModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close modals when clicking outside
    document.getElementById('editCategoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
    
    document.getElementById('addSubcategoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSubModal();
        }
    });
</script>

@endsection