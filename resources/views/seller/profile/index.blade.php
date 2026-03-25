@extends('layouts.seller')
@section('title', 'Profile')
@section('page_title', 'My Profile')
@section('breadcrumb')
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')

<form action="{{ route('seller.profile.update') }}"
      method="POST"
      enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="row">

        {{-- Left --}}
        <div class="col-lg-8">

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Personal Information</h5>
                </div>
                <div class="card-body">

                    {{-- Avatar --}}
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div style="width:80px;height:80px;border-radius:50%;overflow:hidden;background:#2ECC71;display:flex;align-items:center;justify-content:center;font-size:28px;color:#fff;font-weight:700;flex-shrink:0;">
                            @if(auth('seller')->user()->avatar)
                                <img src="{{ auth('seller')->user()->avatar }}"
                                     style="width:100%;height:100%;object-fit:cover;" alt="">
                            @else
                                {{ strtoupper(substr(auth('seller')->user()->first_name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <label class="form-label fw-bold mb-1">Profile Photo</label>
                            <input type="file"
                                   name="avatar"
                                   accept="image/jpg,image/jpeg,image/png"
                                   class="form-control form-control-sm">
                            <small class="text-muted">JPG, PNG — max 2MB</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">First Name</label>
                            <input type="text"
                                   name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', auth('seller')->user()->first_name) }}">
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Last Name</label>
                            <input type="text"
                                   name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', auth('seller')->user()->last_name) }}">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email"
                               class="form-control"
                               value="{{ auth('seller')->user()->email }}"
                               disabled>
                        <small class="text-muted">Email cannot be changed. Contact support if needed.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Phone</label>
                        <input type="tel"
                               name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', auth('seller')->user()->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Business Information</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Business Name</label>
                        <input type="text"
                               name="business_name"
                               class="form-control @error('business_name') is-invalid @enderror"
                               value="{{ old('business_name', auth('seller')->user()->business_name) }}">
                        @error('business_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Business Description</label>
                        <textarea name="business_description"
                                  class="form-control @error('business_description') is-invalid @enderror"
                                  rows="4"
                                  placeholder="Tell buyers about your business...">{{ old('business_description', auth('seller')->user()->business_description) }}</textarea>
                        @error('business_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Business Address</label>
                        <input type="text"
                               name="business_address"
                               class="form-control @error('business_address') is-invalid @enderror"
                               value="{{ old('business_address', auth('seller')->user()->business_address) }}"
                               placeholder="Street, City, State">
                        @error('business_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Change Password</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Current Password</label>
                        <input type="password"
                               name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               placeholder="Leave blank to keep current password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">New Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min 8 characters">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Confirm New Password</label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   placeholder="Repeat new password">
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- Right --}}
        <div class="col-lg-4">

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Save</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success mb-3">
                            <i class="feather-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profile Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled fs-13 text-muted">
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Add a clear profile photo
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Fill in your business name
                        </li>
                        <li class="mb-2">
                            <i class="feather-check-circle text-success me-2"></i>
                            Write a compelling business description
                        </li>
                        <li class="mb-0">
                            <i class="feather-check-circle text-success me-2"></i>
                            Keep your phone number up to date
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</form>

@endsection