@extends('layouts.admin')
@section('title', 'My Profile')
@section('page_title', 'My Profile')
@section('breadcrumb')
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-4">
        {{-- Profile Card --}}
        <div class="card">
            <div class="card-body text-center">
                
                <div style="width:100px;height:100px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:700;margin:0 auto 15px;">
                    {{ strtoupper(substr($admin->first_name, 0, 1)) }}{{ strtoupper(substr($admin->last_name, 0, 1)) }}
                </div>
                <h4 class="fw-bold mb-1">{{ $admin->full_name }}</h4>
                <p class="text-muted mb-2">{{ $admin->email }}</p>
                <span class="badge orderer-badge" style="
                    background-color: #2ECC71;
                    color: #fff;
                    padding: 5px 15px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 600;
                ">
                    {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                </span>
            </div>
            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Last Login</span>
                    <strong>{{ $admin->last_login_at ? $admin->last_login_at->format('M d, Y H:i') : 'Never' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Member Since</span>
                    <strong>{{ $admin->created_at->format('M d, Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        {{-- Password Update Card --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Update Password</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="feather-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="feather-alert-circle me-2"></i>
                    Please fix the errors below.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <form action="{{ route('admin.profile.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="feather-lock"></i></span>
                            <input type="password" name="current_password" 
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Enter current password" required>
                        </div>
                        @error('current_password')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="feather-key"></i></span>
                            <input type="password" name="password" 
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Enter new password" required>
                        </div>
                        <small class="text-muted">Minimum 8 characters</small>
                        @error('password')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="feather-check"></i></span>
                            <input type="password" name="password_confirmation" 
                                   class="form-control"
                                   placeholder="Confirm new password" required>
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="feather-info me-2"></i>
                        After changing your password, you will need to log in again with your new password.
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

        {{-- Account Information Card (Read Only) --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">First Name</label>
                        <p class="fw-semibold mb-0">{{ $admin->first_name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Last Name</label>
                        <p class="fw-semibold mb-0">{{ $admin->last_name }}</p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted d-block fs-12 mb-1">Email Address</label>
                        <p class="fw-semibold mb-0">{{ $admin->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted d-block fs-12 mb-1">Role</label>
                        <p class="fw-semibold mb-0">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted d-block fs-12 mb-1">Status</label>
                        <p class="fw-semibold mb-0">
                            @if($admin->is_active)
                                <span class="badge badge-approved">Active</span>
                            @else
                                <span class="badge badge-rejected">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection