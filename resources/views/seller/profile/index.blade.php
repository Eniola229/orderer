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

            {{-- Address display row --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Business Address</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="text"
                           name="business_address"
                           id="profileAddress"
                           class="form-control @error('business_address') is-invalid @enderror"
                           value="{{ old('business_address', auth('seller')->user()->business_address) }}"
                           placeholder="House number, street name">
                    <button type="button"
                            class="btn btn-outline-primary btn-sm text-nowrap"
                            onclick="toggleAddressFields()">
                        <i class="feather-map-pin me-1"></i> Validate
                    </button>
                </div>
                @if(auth('seller')->user()->address_code)
                    <small class="text-success mt-1 d-block">
                        <i class="feather-check-circle me-1"></i> Address previously validated
                    </small>
                @endif
                @error('business_address')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Hidden address fields — only shown when validating --}}
            <div id="addressFieldsPanel" style="display:none;">
                <div class="p-3 mb-3 border rounded" style="background:#f9f9f9;">
                    <p class="fs-13 text-muted mb-3">
                        <i class="feather-info me-1"></i>
                        Fill in city, state and country to validate your address with our courier network.
                    </p>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label fw-bold fs-13">City</label>
                            <input type="text" id="profileCity" class="form-control form-control-sm"
                                   placeholder="e.g. Lagos">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold fs-13">State</label>
                            <input type="text" id="profileState" class="form-control form-control-sm"
                                   placeholder="e.g. Lagos">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold fs-13">Country</label>
                            <input type="text" id="profileCountry" class="form-control form-control-sm"
                                   placeholder="e.g. Nigeria" value="Nigeria">
                        </div>
                    </div>

                    {{-- Hidden address code saved to DB --}}
                    <input type="hidden" name="address_code" id="profileAddressCode" value="">

                    <div id="profileAddrFeedback" class="mb-2" style="display:none;"></div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm" id="validateAddrBtn"
                                onclick="validateProfileAddress()">
                            <i class="feather-check me-1"></i> Validate Address
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                onclick="toggleAddressFields()">
                            Cancel
                        </button>
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
@push('scripts')
<script>
function toggleAddressFields() {
    const panel = document.getElementById('addressFieldsPanel');
    const isHidden = panel.style.display === 'none';
    panel.style.display = isHidden ? 'block' : 'none';

    // Clear feedback when closing
    if (!isHidden) {
        document.getElementById('profileAddrFeedback').style.display = 'none';
    }
}

async function validateProfileAddress() {
    const btn      = document.getElementById('validateAddrBtn');
    const feedback = document.getElementById('profileAddrFeedback');

    const address = document.getElementById('profileAddress').value.trim();
    const city    = document.getElementById('profileCity').value.trim();
    const state   = document.getElementById('profileState').value.trim();
    const country = document.getElementById('profileCountry').value.trim();
    const name    = '{{ auth("seller")->user()->first_name }} {{ auth("seller")->user()->last_name }}';
    const email   = '{{ auth("seller")->user()->email }}';
    const phone   = '{{ auth("seller")->user()->phone }}';

    if (!address || !city || !state || !country) {
        feedback.style.display = 'block';
        feedback.innerHTML = '<div class="alert alert-danger py-2 mb-0">Please fill in address, city, state and country.</div>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Validating…';
    feedback.style.display = 'none';

    try {
        const res = await fetch('{{ route("address.validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ name, email, phone, address, city, state, country }),
        });

        const data = await res.json();

        if (data.success) {
            // Save address_code into the hidden form field
            document.getElementById('profileAddressCode').value = data.address_code;

            // Update address field with validated address if returned
            if (data.address) {
                document.getElementById('profileAddress').value = data.address;
            }

            feedback.style.display = 'block';
            feedback.innerHTML = `<div class="alert alert-success py-2 mb-0">
                <i class="feather-check-circle me-1"></i> Address validated! Save your profile to confirm.
            </div>`;

            // Hide the panel after short delay
            setTimeout(() => toggleAddressFields(), 1500);

        } else {
            feedback.style.display = 'block';
            feedback.innerHTML = `<div class="alert alert-danger py-2 mb-0">
                <i class="feather-alert-circle me-1"></i> ${data.message}
            </div>`;
        }

    } catch (err) {
        feedback.style.display = 'block';
        feedback.innerHTML = `<div class="alert alert-danger py-2 mb-0">Something went wrong. Please try again.</div>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="feather-check me-1"></i> Validate Address';
    }
}
</script>
@endpush
@endsection