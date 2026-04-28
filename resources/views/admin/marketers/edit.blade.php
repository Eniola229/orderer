@extends('layouts.admin')
@section('title', 'Edit Marketer')
@section('page_title', 'Edit Marketer')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.marketers.index') }}">Marketers</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

@if(!auth('admin')->user()->canManageAdmins())
<div class="alert alert-danger">
    <i class="feather-lock me-2"></i> You do not have permission to edit marketer accounts.
</div>
@else

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ $marketer->full_name }}</h5>
                @if($marketer->marketing_code)
                <code class="fs-13 bg-dark text-success px-3 py-1 rounded">{{ $marketer->marketing_code }}</code>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('admin.marketers.update', $marketer) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $marketer->first_name) }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $marketer->last_name) }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $marketer->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">New Password <span class="text-muted fw-normal">(leave blank to keep)</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $marketer->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $marketer->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Internal Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $marketer->notes) }}</textarea>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.marketers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>

                <hr>

                {{-- Regenerate code section --}}
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="fw-bold mb-1 fs-14">Marketing Code</p>
                        <p class="text-muted fs-13 mb-0">Generate a fresh OR-MRT- code. The old one stops working immediately.</p>
                    </div>
                    <form method="POST" action="{{ route('admin.marketers.regen-code', $marketer) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning"
                                onclick="return confirm('This will invalidate the current code. Continue?')">
                            <i class="feather-refresh-cw me-1"></i> Regenerate
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

@endif
@endsection