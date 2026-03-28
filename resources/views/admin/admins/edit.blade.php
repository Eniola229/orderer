@extends('layouts.admin')
@section('title', 'Edit Admin')
@section('page_title', 'Edit Admin: ' . $adminUser->full_name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

@if(!auth('admin')->user()->canManageAdmins())
<div class="alert alert-danger">
    <i class="feather-lock me-2"></i>
    You do not have permission to edit admin accounts.
</div>
@else

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Role &amp; Status</h5>
            </div>
            <div class="card-body">

                <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded">
                    <div style="width:52px;height:52px;border-radius:50%;background:#1a1a2e;color:#2ECC71;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($adminUser->first_name,0,1)) }}
                    </div>
                    <div>
                        <p class="mb-0 fw-bold">{{ $adminUser->full_name }}</p>
                        <small class="text-muted">{{ $adminUser->email }}</small>
                    </div>
                </div>

                @if($adminUser->isSuperAdmin() && !auth('admin')->user()->isSuperAdmin())
                <div class="alert alert-danger">
                    <i class="feather-lock me-2"></i>
                    Only a Super Admin can edit another Super Admin.
                </div>
                @else

                <form action="{{ route('admin.admins.update', $adminUser->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold">Role</label>
                        <select name="role" class="form-select" required>
                            @foreach(\App\Models\Admin::roles() as $value => $label)
                            @if(!(auth('admin')->user()->role === 'hr' && $value === 'super_admin'))
                            <option value="{{ $value }}"
                                    {{ $adminUser->role === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <select name="is_active" class="form-select" required>
                            <option value="1" {{ $adminUser->is_active == 1 ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="0" {{ $adminUser->is_active == 0 ? 'selected' : '' }}>
                                Inactive / Suspended
                            </option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.admins.index') }}"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>

                @endif
            </div>
        </div>

        {{-- Activity summary --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Activity Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Last login</span>
                    <strong>
                        {{ $adminUser->last_login_at ? $adminUser->last_login_at->format('M d, Y H:i') : 'Never' }}
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Member since</span>
                    <strong>{{ $adminUser->created_at->format('M d, Y') }}</strong>
                </div>
                @if(auth('admin')->user()->canViewLogs())
                <a href="{{ route('admin.logs.index', ['guard' => 'admin']) }}"
                   class="btn btn-sm btn-outline-secondary w-100 mt-2">
                    <i class="feather-activity me-1"></i> View Activity Logs
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endif

@endsection