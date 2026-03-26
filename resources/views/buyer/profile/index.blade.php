@extends('layouts.buyer')
@section('title', 'My Profile')
@section('page_title', 'Profile Settings')
@section('breadcrumb')
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-8">

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('buyer.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    {{-- Avatar --}}
                    <div class="d-flex align-items-center gap-3 mb-4">
                        @if(auth('web')->user()->avatar)
                            <img src="{{ auth('web')->user()->avatar }}"
                                 style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #2ECC71;" alt="">
                        @else
                            <div style="width:80px;height:80px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr(auth('web')->user()->first_name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <label class="form-label fw-bold mb-1">Profile Photo</label>
                            <input type="file" name="avatar" class="form-control form-control-sm"
                                   accept="image/jpg,image/jpeg,image/png">
                            <small class="text-muted">JPG, PNG — max 2MB</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">First Name</label>
                            <input type="text" name="first_name" class="form-control"
                                   value="{{ old('first_name', auth('web')->user()->first_name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Name</label>
                            <input type="text" name="last_name" class="form-control"
                                   value="{{ old('last_name', auth('web')->user()->last_name) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control"
                               value="{{ auth('web')->user()->email }}" disabled
                               style="background:#f5f5f5;">
                        <small class="text-muted">Email cannot be changed. Contact support.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number</label>
                        <input type="tel" name="phone" class="form-control"
                               value="{{ old('phone', auth('web')->user()->phone) }}"
                               placeholder="+234 800 000 0000">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        {{-- Change password --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('buyer.profile.password') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Password</label>
                        <input type="password" name="current_password" class="form-control"
                               placeholder="Your current password">
                        @error('current_password')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">New Password</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Minimum 8 characters">
                            @error('password')
                                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Repeat new password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-outline-primary">
                        <i class="feather-lock me-2"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                @if(auth('web')->user()->avatar)
                    <img src="{{ auth('web')->user()->avatar }}"
                         style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:4px solid #2ECC71;margin-bottom:16px;" alt="">
                @else
                    <div style="width:100px;height:100px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:36px;font-weight:700;margin:0 auto 16px;">
                        {{ strtoupper(substr(auth('web')->user()->first_name, 0, 1)) }}
                    </div>
                @endif
                <h5 class="fw-bold">{{ auth('web')->user()->full_name }}</h5>
                <p class="text-muted fs-13">{{ auth('web')->user()->email }}</p>
                <div class="border rounded p-3 text-start mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Wallet Balance</small>
                        <strong class="text-success">${{ number_format(auth('web')->user()->wallet_balance, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Referral Code</small>
                        <strong>{{ auth('web')->user()->referral_code }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Member Since</small>
                        <strong>{{ auth('web')->user()->created_at->format('M Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
