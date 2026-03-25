@extends('layouts.auth')
@section('title', 'Seller Registration')

@section('content')
<div class="auth-main">

    <div class="auth-left-panel">
        <div class="content">
            <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
                 style="height:40px;margin-bottom:28px;filter:brightness(0) invert(1);" alt="Orderer">
            <h1>Sell on Orderer</h1>
            <p>Reach thousands of buyers across Nigeria and the world. List products, services or properties in minutes.</p>
            <ul style="padding-left:18px;margin-top:20px;">
                <li>USD wallet — withdraw anytime</li>
                <li>Escrow protection on every order</li>
                <li>Powerful ads system to grow faster</li>
                <li>Dedicated seller dashboard</li>
            </ul>
        </div>
    </div>

    <div class="auth-right-panel">
        <div class="auth-form-box">

            <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
                 class="auth-logo-img" alt="Orderer">

            <h2>Create seller account</h2>
            <p class="subtitle">
                Already have an account?
                <a href="{{ route('seller.login') }}" class="auth-link">Sign in</a>
            </p>

            {{-- Step bar --}}
            <div class="auth-step-bar" id="stepBar">
                <span class="active" id="bar1"></span>
                <span id="bar2"></span>
                <span id="bar3"></span>
            </div>

            <form action="{{ route('seller.register') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="sellerRegForm">
                @csrf

                {{-- Step 1 --}}
                <div id="step1">
                    <p class="text-muted fs-13 mb-4">Step 1 of 3 — Personal &amp; business details</p>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">First name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name') }}" placeholder="John">
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Last name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name') }}" placeholder="Doe">
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="john@example.com">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control"
                               value="{{ old('phone') }}" placeholder="+234 800 000 0000">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Business name <span class="text-danger">*</span></label>
                        <input type="text" name="business_name"
                               class="form-control @error('business_name') is-invalid @enderror"
                               value="{{ old('business_name') }}" placeholder="Your store name">
                        @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Business address</label>
                        <input type="text" name="business_address" class="form-control"
                               value="{{ old('business_address') }}" placeholder="Your location">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Confirm password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" placeholder="Repeat">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mt-2"
                            onclick="goStep(2)">
                        Continue <i class="feather-arrow-right ms-1"></i>
                    </button>
                </div>

                {{-- Step 2 --}}
                <div id="step2" style="display:none;">
                    <p class="text-muted fs-13 mb-4">Step 2 of 3 — Is your business registered?</p>

                    <div class="seller-type-grid">
                        <div class="seller-type-card" id="cardVerified"
                             onclick="pickType('verified')">
                            <i class="feather-award"></i>
                            <h6>Registered Business</h6>
                            <p>I have a CAC certificate, business license or government ID</p>
                        </div>
                        <div class="seller-type-card" id="cardUnverified"
                             onclick="pickType('unverified')">
                            <i class="feather-user"></i>
                            <h6>Individual Seller</h6>
                            <p>I don't have business documents yet</p>
                        </div>
                    </div>

                    <input type="hidden" name="is_verified_business" id="verifiedInput" value="">

                    <div id="verifiedNote" class="alert alert-success d-none">
                        <i class="feather-check-circle me-2"></i>
                        You'll upload documents in the next step.
                    </div>

                    <div id="unverifiedNote" class="alert alert-warning d-none">
                        <i class="feather-info me-2"></i>
                        No problem. You can still sell and upload documents later.
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="goStep(1)">
                            <i class="feather-arrow-left me-1"></i> Back
                        </button>
                        <button type="button" class="btn btn-primary flex-grow-1"
                                id="step2Btn" onclick="goStep(3)" disabled>
                            Continue <i class="feather-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div id="step3" style="display:none;">
                    <p class="text-muted fs-13 mb-4" id="step3Title">Step 3 of 3 — Final step</p>

                    <div id="docSection" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Document type <span class="text-danger">*</span></label>
                            <select name="document_type" class="form-select">
                                <option value="">Select type</option>
                                <option value="cac_certificate">CAC Certificate</option>
                                <option value="government_id">Government-issued ID</option>
                                <option value="business_license">Business License</option>
                                <option value="school_certificate">School Certificate</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload document <span class="text-danger">*</span></label>
                            <input type="file" name="document_file" class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">PDF, JPG, PNG — max 5MB</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="terms" id="terms"
                                   class="form-check-input" required>
                            <label class="form-check-label text-muted fs-13" for="terms">
                                I agree to Orderer's
                                <a href="#" class="auth-link">Seller Terms</a> and
                                <a href="#" class="auth-link">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    @if(request('ref'))
                    <input type="hidden" name="referral_code" value="{{ request('ref') }}">
                    @endif

                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="goStep(2)">
                            <i class="feather-arrow-left me-1"></i> Back
                        </button>
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="feather-user-check me-1"></i> Create Account
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function goStep(n) {
    [1,2,3].forEach(i => {
        document.getElementById('step'+i).style.display = (i===n) ? 'block' : 'none';
        const bar = document.getElementById('bar'+i);
        bar.className = i < n ? 'done' : (i === n ? 'active' : '');
    });
}

function pickType(type) {
    document.getElementById('cardVerified').classList.toggle('selected', type === 'verified');
    document.getElementById('cardUnverified').classList.toggle('selected', type === 'unverified');
    document.getElementById('verifiedInput').value = type === 'verified' ? '1' : '0';
    document.getElementById('step2Btn').disabled = false;
    document.getElementById('verifiedNote').classList.toggle('d-none', type !== 'verified');
    document.getElementById('unverifiedNote').classList.toggle('d-none', type !== 'unverified');

    if (type === 'verified') {
        document.getElementById('step3Title').textContent = 'Step 3 of 3 — Upload business document';
        document.getElementById('docSection').style.display = 'block';
    } else {
        document.getElementById('step3Title').textContent = 'Step 3 of 3 — Review and confirm';
        document.getElementById('docSection').style.display = 'none';
    }
}
</script>
@endpush
@endsection