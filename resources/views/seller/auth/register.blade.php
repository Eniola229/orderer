@extends('layouts.auth')
@section('title', 'Seller Registration')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
@endpush
<div class="auth-main">

    <div class="auth-left-panel">
        <div class="auth-left-inner">

            <div class="auth-panel-logo">
                <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                     style="height:32px;filter:brightness(0) invert(1);" alt="Orderer">
                <span>Orderer</span>
            </div>

            <div class="auth-panel-tag">For Sellers</div>

            <h1>Sell to thousands<br>across Nigeria</h1>
            <p>List products, services or properties in minutes and start earning today.</p>

            <div class="auth-panel-features"> 

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-dollar-sign"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>USD wallet</strong>
                        <span>Withdraw your earnings anytime, no delays</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-lock"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Escrow protection</strong>
                        <span>Every order is secured until delivery confirmed</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-trending-up"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Powerful ads system</strong>
                        <span>Boost listings and grow your sales faster</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-grid"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Seller dashboard</strong>
                        <span>Full analytics, orders &amp; inventory in one place</span>
                    </div>
                </div>

            </div>

            <div class="auth-trust-bar">
                <div class="auth-trust-avatars">
                    <span>AO</span>
                    <span>KF</span>
                    <span>BN</span>
                    <span>+</span>
                </div>
                <div class="auth-trust-text">
                    <strong>4,200+ active sellers</strong>
                    <span>Join the fastest-growing marketplace</span>
                </div>
            </div>

        </div>
    </div>

    <div class="auth-right-panel">
        <div class="auth-form-box">

            <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
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
                  id="sellerRegForm" class="">
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
                        <input type="email" name="email" id="regEmail"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="john@example.com">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone <span class="text-danger">*</span></label><br>
                        <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Business name <span class="text-danger">*</span></label>
                        <input type="text" name="business_name"
                               class="form-control @error('business_name') is-invalid @enderror"
                               value="{{ old('business_name') }}" placeholder="Your store name">
                        @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Address fields --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Business address <span class="text-danger">*</span></label><br>
                        <small>Address should be in full details. No, Street, City/State, Country</small>
                        <input type="text" name="business_address" id="businessAddress" class="form-control"
                               value="{{ old('business_address') }}" placeholder="House number, street name">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">City <span class="text-danger">*</span></label>
                            <input type="text" name="business_city" id="businessCity" class="form-control"
                                   value="{{ old('business_city') }}" placeholder="e.g. Lagos">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">State / Province <span class="text-danger">*</span></label>
                            <input type="text" name="business_state" id="businessState" class="form-control"
                                   value="{{ old('business_state') }}" placeholder="e.g. Lagos">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                        <input type="text" name="business_country" id="businessCountry" class="form-control"
                               value="{{ old('business_country', 'Nigeria') }}" placeholder="e.g. Nigeria">
                    </div>
                    
                    {{-- Hidden address code --}}
                    <input type="hidden" name="address_code" id="addressCode" value="{{ old('address_code') }}">

                    {{-- Address validation feedback --}}
                    <div id="addrFeedback" class="mb-3" style="display:none;"></div>

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
                            id="step1ContinueBtn" onclick="validateAndProceed()">
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
                                <a href="{{ route('legal.seller-terms') }}" class="auth-link">Seller Terms</a> and
                                <a href="{{ route('legal.privacy') }}" class="auth-link">Privacy Policy</a>
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
@include('layouts.partials.auth-seller-footer')

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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

// Validate address then proceed to step 2
async function validateAndProceed() {
    const btn      = document.getElementById('step1ContinueBtn');
    const feedback = document.getElementById('addrFeedback');

    const address = document.getElementById('businessAddress').value.trim();
    const city    = document.getElementById('businessCity').value.trim();
    const state   = document.getElementById('businessState').value.trim();
    const country = document.getElementById('businessCountry').value.trim();
    const name    = document.querySelector('[name="first_name"]').value.trim()
                  + ' ' + document.querySelector('[name="last_name"]').value.trim();
    const phone   = iti.getNumber();
    const email   = document.getElementById('regEmail').value.trim();

    if (!address || !city || !state || !country) {
        feedback.style.display = 'block';
        feedback.innerHTML = '<div class="alert alert-danger py-2">Please fill in your address, city, state and country.</div>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Validating address…';
    feedback.style.display = 'none';

    try {
        const res = await fetch('{{ route("address.validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ name, email, phone, address, city, state, country }),
        });

        const data = await res.json();

        if (data.success) {
            document.getElementById('addressCode').value = data.address_code;

            if (data.address) {
                document.getElementById('businessAddress').value = data.address;
            }

            feedback.style.display = 'block';
            feedback.innerHTML = `<div class="alert alert-success py-2">
                <i class="feather-check-circle me-1"></i> Address verified successfully.
            </div>`;

            setTimeout(() => goStep(2), 800);

        } else {
            feedback.style.display = 'block';
            feedback.innerHTML = `<div class="alert alert-danger py-2">
                <i class="feather-alert-circle me-1"></i> ${data.message}
            </div>`;
        }

    } catch (err) {
        feedback.style.display = 'block';
        feedback.innerHTML = `<div class="alert alert-danger py-2">
            Something went wrong. Please try again.
        </div>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Continue <i class="feather-arrow-right ms-1"></i>';
    }
}

// Phone input
const input = document.querySelector("#phone");
const iti = window.intlTelInput(input, {
    initialCountry: "ng",
    separateDialCode: true,
    preferredCountries: ["ng", "us", "gb"],
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
});

const form = input.closest("form");
form.addEventListener("submit", function () {
    input.value = iti.getNumber();
});
</script>
@endpush
@endsection