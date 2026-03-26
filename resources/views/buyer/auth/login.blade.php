@extends('layouts.auth')
@section('title', 'Sign In')
@section('content')
<div class="auth-main">

    <div class="auth-left-panel">
        <div class="auth-left-inner">

            <div class="auth-panel-logo">
                <img src="{{ asset('img/core-img/logo.png') }}"
                     style="height:32px;filter:brightness(0) invert(1);" alt="Orderer">
                <span>Orderer</span>
            </div>

            <div class="auth-panel-tag">Welcome Back</div>

            <h1>Good to see<br>you again</h1>
            <p>Sign in to track your orders, manage your wishlist, and continue shopping from where you left off.</p>

            <div class="auth-panel-features">

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-package"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Track your orders</strong>
                        <span>Real-time updates on every delivery</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-heart"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Your wishlist</strong>
                        <span>Save items and come back anytime</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-shield"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Secure checkout</strong>
                        <span>Escrow protection on every purchase</span>
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

            <h2>Sign in</h2>
            <p class="subtitle">
                New to Orderer?
                <a href="{{ route('register') }}" class="auth-link">Create account</a>
            </p>

            <form action="{{ route('login') }}" method="POST" autocomplete="off">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold">Email address</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="john@example.com"
                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label fw-bold mb-0">Password</label>
                        <a href="{{ route('password.request') }}" class="auth-link fs-13">
                            Forgot password?
                        </a>
                    </div>
                    <input type="password"
                           name="password"
                           placeholder="Your password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror mt-2"
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
                    <i class="feather-log-in me-2"></i> Sign In
                </button>

                <div class="auth-divider">or</div>

                <a href="{{ route('seller.login') }}"
                   class="btn btn-outline-secondary w-100 mt-3">
                    Sign in as a Seller
                </a>

            </form>
        </div>
    </div>

</div>
@endsection