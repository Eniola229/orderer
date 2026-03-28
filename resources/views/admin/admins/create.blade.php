@extends('layouts.admin')
@section('title', 'Add Admin')
@section('page_title', 'Add New Admin')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')

@if(!auth('admin')->user()->canManageAdmins())
<div class="alert alert-danger">
    <i class="feather-lock me-2"></i>
    You do not have permission to create admin accounts.
</div>
@else

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Admin Account Details</h5>
            </div>
            <div class="card-body">

                <div class="alert alert-info mb-4">
                    <i class="feather-info me-2"></i>
                    All admin activity is logged. Assign the minimum role needed for the person's responsibilities.
                </div>

                <form action="{{ route('admin.admins.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name') }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name') }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="admin@orderer.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Role <span class="text-danger">*</span>
                        </label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Select role...</option>
                            @foreach(\App\Models\Admin::roles() as $value => $label)
                            {{-- HR cannot create super_admin --}}
                            @if(!(auth('admin')->user()->role === 'hr' && $value === 'super_admin'))
                            <option value="{{ $value }}"
                                    {{ old('role') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Role descriptions --}}
                    <div class="bg-light rounded p-3 mb-4">
                        <p class="fw-bold fs-13 mb-2">Role Permissions:</p>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <small><strong>Super Admin</strong> — Full access including logs</small><br>
                                <small><strong>Finance Admin</strong> — Withdrawals, orders, finance</small><br>
                                <small><strong>Support Admin</strong> — View all, reply tickets</small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Content Moderator</strong> — Sellers, products, ads, categories</small><br>
                                <small><strong>HR</strong> — Manage admin accounts only</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        <small class="text-muted">Inactive accounts cannot log in.</small>
                        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-warning mb-4">
                        <i class="feather-alert-triangle me-2"></i>
                        This action is logged and visible to Super Admins.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-user-plus me-2"></i> Create Admin
                        </button>
                        <a href="{{ route('admin.admins.index') }}"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endif

@endsection