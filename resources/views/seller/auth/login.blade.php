@extends('layouts.auth')
@section('title', 'Seller Sign In')

@section('content')
<div class="auth-main">

    <div class="auth-left-panel">
        <div class="content">
            <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
                 style="height:40px;margin-bottom:28px;filter:brightness(0) invert(1);" alt="Orderer">
            <h1>Seller Dashboard</h1>
            <p>Manage your products, track orders, run ads and grow your business all in one place.</p>
            <ul style="padding-left:18px;margin-top:20px;">
                <li>USD wallet — withdraw anytime</li>
                <li>Escrow protection on every sale</li>
                <li>Powerful ads system</li>
                <li>Full analytics dashboard</li>
            </ul>
        </div>
    </div>

    <div class="auth-right-panel">
        <div class="auth-form-box">

            <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
                 class="auth-logo-img" alt="Orderer">

            <h2>Seller sign in</h2>
            <p class="subtitle">
                Not a seller yet?
                <a href="{{ route('seller.register') }}" class="auth-link">Start selling</a>
            </p>

            <form action="{{ route('seller.login') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold">Email address</label>
                    <input type="email"
                           name="email"
                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="seller@example.com"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <label class="form-label fw-bold">Password</label>
                        <a href="{{ route('seller.password.request') }}" class="auth-link fs-13">
                            Forgot password?
                        </a>
                    </div>
                    <input type="password"
                           name="password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                           placeholder="Your password"
                           required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label text-muted" for="remember">
                            Keep me signed in
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    Sign In to Dashboard
                </button>

                <div class="text-center text-muted my-3">or</div>

                <a href="{{ route('login') }}"
                   class="btn btn-outline-secondary w-100">
                    Sign in as a Buyer instead
                </a>

            </form>
        </div>
    </div>

</div>
@endsection