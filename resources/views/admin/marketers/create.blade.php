@extends('layouts.admin')
@section('title', 'Add Marketer')
@section('page_title', 'Add New Marketer')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.marketers.index') }}">Marketers</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')

@if(!auth('admin')->user()->canManageAdmins())
<div class="alert alert-danger">
    <i class="feather-lock me-2"></i> You do not have permission to create marketer accounts.
</div>
@else

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Marketer Account Details</h5>
            </div>
            <div class="card-body">

                <div class="alert alert-info mb-4">
                    <i class="feather-info me-2"></i>
                    A unique <strong>OR-MRT-</strong> marketing code will be auto-generated for this marketer when you create the account.
                </div>

                <form action="{{ route('admin.marketers.store') }}" method="POST">
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
                               value="{{ old('email') }}" required>
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
                        <label class="form-label fw-bold">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <small class="text-muted">Inactive marketers cannot log in.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Internal Notes <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="e.g. Assigned to Lagos region">{{ old('notes') }}</textarea>
                    </div>

                    <div class="alert alert-warning mb-4">
                        <i class="feather-alert-triangle me-2"></i>
                        The marketer will log in at <strong>/marketer/login</strong> — not the admin panel.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-user-plus me-2"></i> Create Marketer
                        </button>
                        <a href="{{ route('admin.marketers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endif
@endsection