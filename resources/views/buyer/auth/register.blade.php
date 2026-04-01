@extends('layouts.auth')
@section('title', 'Create Account')

@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
@endpush
<div class="auth-main">

    <div class="auth-left-panel">
        <div class="auth-left-inner">

            <div class="auth-panel-logo">
                <img src="{{ asset('img/core-img/logo.png') }}"
                     style="height:32px;filter:brightness(0) invert(1);" alt="Orderer">
                <span>Orderer</span>
            </div>

            <div class="auth-panel-tag">For Buyers</div>

            <h1>Shop smarter<br>with Orderer</h1>
            <p>Join thousands of buyers discovering amazing products, services and properties — all in one place.</p>

            <div class="auth-panel-features">

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-shield"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Secure payments</strong>
                        <span>Every transaction protected with escrow</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-truck"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Local & international delivery</strong>
                        <span>Book riders for fast delivery anywhere</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-check-circle"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Verified sellers</strong>
                        <span>Buy with confidence from trusted stores</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-gift"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Earn from referrals</strong>
                        <span>Invite friends and earn rewards on every sign-up</span>
                    </div>
                </div>

            </div>

            <div class="auth-trust-bar">
                <div class="auth-trust-avatars">
                    <span>TI</span>
                    <span>AM</span>
                    <span>CO</span>
                    <span>+</span>
                </div>
                <div class="auth-trust-text">
                    <strong>50,000+ happy buyers</strong>
                    <span>Trusted across the world</span>
                </div>
            </div>

        </div>
    </div>

    <div class="auth-right-panel">
        <div class="auth-form-box">

            <a href="{{ route('home') }}">
                <img src="{{ asset('img/core-img/logo.png') }}"
                     class="auth-logo-img" alt="Orderer">
            </a>

            <h2>Create your account</h2>
            <p class="subtitle">
                Already have an account?
                <a href="{{ route('login') }}" class="auth-link">Sign in</a>
            </p>

            <form action="{{ route('register') }}" method="POST" autocomplete="off">
                @csrf

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">First name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="first_name"
                               value="{{ old('first_name') }}"
                               placeholder="John"
                               class="form-control @error('first_name') is-invalid @enderror"
                               required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Last name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="last_name"
                               value="{{ old('last_name') }}"
                               placeholder="Doe"
                               class="form-control @error('last_name') is-invalid @enderror"
                               required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email address <span class="text-danger">*</span></label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="john@example.com"
                           class="form-control @error('email') is-invalid @enderror"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Phone number <span class="text-danger">*</span></label><br>
                    <input type="tel"
                           id="phone"
                           name="phone"
                           value="{{ old('phone') }}"
                           class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                        <input type="password"
                               name="password"
                               placeholder="Min. 8 characters"
                               class="form-control @error('password') is-invalid @enderror"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Confirm password <span class="text-danger">*</span></label>
                        <input type="password"
                               name="password_confirmation"
                               placeholder="Repeat"
                               class="form-control"
                               required>
                    </div>
                </div>

                @if(request('ref'))
                <div class="mb-3">
                    <label class="form-label fw-bold">Referral code</label>
                    <input type="text"
                           name="referral_code"
                           value="{{ request('ref') }}"
                           class="form-control"
                           readonly
                           style="background:#f0faf5;">
                </div>
                @endif

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="terms" id="terms" class="form-check-input" required>
                        <label class="form-check-label text-muted fs-13" for="terms">
                            I agree to the
                            <a href="{{ route('legal.terms') }}" class="auth-link">Terms of Service</a> and
                            <a href="{{ route('legal.privacy') }}" class="auth-link">Privacy Policy</a>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="feather-user-plus me-2"></i> Create Account
                </button>

                <div class="auth-divider">or</div>

                <p class="text-center text-muted" style="font-size:13px; margin-top:14px;">
                    Want to sell on Orderer?
                    <a href="{{ route('seller.register') }}" class="auth-link">Register as a seller</a>
                </p>

            </form>
        </div>
    </div>

</div>
@include('layouts.partials.auth-buyer-footer')

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script>
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