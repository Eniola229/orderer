@extends('layouts.auth')
@section('title', 'Create Account')


@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
@endpush
<div class="auth-wrapper">

    {{-- Left panel --}}
    <div class="auth-left">
        <div class="auth-left-content">
            <a href="{{ route('home') }}">
                <img src="{{ asset('img/core-img/logo.png') }}"
                     alt="Orderer"
                     style="height:40px; margin-bottom:30px;">
            </a>
            <h1>Shop smarter with Orderer</h1>
            <p>
                Join thousands of buyers discovering amazing products,
                services and properties — all in one place.
                Get instant delivery anywhere in the world.
            </p>
            <ul style="margin-top:24px; padding-left:20px; opacity:.9;">
                <li style="margin-bottom:8px;">Secure payments</li>
                <li style="margin-bottom:8px;">Book riders for local & international delivery</li>
                <li style="margin-bottom:8px;">Verified sellers with escrow protection</li>
                <li>Earn from referrals</li>
            </ul>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="auth-right">
        <div class="auth-box">

            <a href="{{ route('home') }}" class="auth-logo">
                <img src="{{ asset('img/core-img/logo.png') }}" alt="Orderer">
            </a>

            <h2>Create your account</h2>
            <p class="auth-subtitle">
                Already have an account?
                <a href="{{ route('login') }}" class="orderer-auth-link">Sign in</a>
            </p>

            <form action="{{ route('register') }}" method="POST" autocomplete="off">
                @csrf

                <div class="row">
                    <div class="col-6">
                        <div class="auth-form-group">
                            <label>First name <span style="color:red">*</span></label>
                            <input type="text"
                                   name="first_name"
                                   value="{{ old('first_name') }}"
                                   placeholder="John"
                                   class="{{ $errors->has('first_name') ? 'input-error' : '' }}"
                                   required>
                            @error('first_name')
                                <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="auth-form-group">
                            <label>Last name <span style="color:red">*</span></label>
                            <input type="text"
                                   name="last_name"
                                   value="{{ old('last_name') }}"
                                   placeholder="Doe"
                                   class="{{ $errors->has('last_name') ? 'input-error' : '' }}"
                                   required>
                            @error('last_name')
                                <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="auth-form-group">
                    <label>Email address <span style="color:red">*</span></label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="john@example.com"
                           class="{{ $errors->has('email') ? 'input-error' : '' }}"
                           required>
                    @error('email')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                    <div class="auth-form-group">
                        <label>Phone number <span style="color:red">*</span></label>
                        
                        <input type="tel"
                               id="phone"
                               name="phone"
                               value="{{ old('phone') }}"
                               class="{{ $errors->has('phone') ? 'input-error' : '' }}">
                        @error('phone')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                <div class="auth-form-group">
                    <label>Password <span style="color:red">*</span></label>
                    <input type="password"
                           name="password"
                           placeholder="Minimum 8 characters"
                           class="{{ $errors->has('password') ? 'input-error' : '' }}"
                           required>
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-form-group">
                    <label>Confirm password <span style="color:red">*</span></label>
                    <input type="password"
                           name="password_confirmation"
                           placeholder="Repeat your password"
                           required>
                </div>

                {{-- Referral code (pre-fill if in URL) --}}
                @if(request('ref'))
                <div class="auth-form-group">
                    <label>Referral code</label>
                    <input type="text"
                           name="referral_code"
                           value="{{ request('ref') }}"
                           readonly
                           style="background:#f0faf5;">
                </div>
                @endif

                <div class="auth-form-group">
                    <label style="display:flex; align-items:flex-start; gap:8px; text-transform:none;">
                        <input type="checkbox" name="terms" required style="width:auto; margin-top:3px;">
                        <span style="font-size:13px; color:#555;">
                            I agree to the
                            <a href="#" class="orderer-auth-link">Terms of Service</a>
                            and
                            <a href="#" class="orderer-auth-link">Privacy Policy</a>
                        </span>
                    </label>
                </div>

                <button type="submit" class="orderer-auth-btn">
                    Create Account
                </button>

                <div class="auth-divider">or</div>

                <p style="text-align:center; font-size:13px; color:#888;">
                    Want to sell on Orderer?
                    <a href="{{ route('seller.register') }}" class="orderer-auth-link">
                        Register as a seller
                    </a>
                </p>

            </form>
        </div>
    </div>

</div>

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script>
        const input = document.querySelector("#phone");

        const iti = window.intlTelInput(input, {
            initialCountry: "ng", // default Nigeria
            separateDialCode: true,
            preferredCountries: ["ng", "us", "gb"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
        });

        // Optional: format number before submit
        const form = input.closest("form");
        form.addEventListener("submit", function () {
            input.value = iti.getNumber(); // full international format
        });
</script>
@endsection