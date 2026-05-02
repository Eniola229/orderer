@extends('layouts.auth')
@section('title', 'Admin Login')
@section('content')
<div class="auth-main">

    <div class="auth-left-panel">
        <div class="auth-left-inner">

            <div class="auth-panel-logo">
                <img src="{{ asset('img/core-img/logo.png') }}"
                     style="height:32px;filter:brightness(0) invert(1);" alt="Orderer">
                <span>Orderer Admin</span>
            </div>

            <div class="auth-panel-tag">Admin Portal</div>

            <h1>Authorized<br>Access Only</h1>
            <p>Securely manage orders, users, and platform operations with full administrative control.</p>

            <div class="auth-panel-features">

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-shopping-bag"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Order Management</strong>
                        <span>Track and manage all orders</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-users"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>User Management</strong>
                        <span>Manage buyers and sellers</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-bar-chart-2"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Analytics Dashboard</strong>
                        <span>Real-time platform insights</span>
                    </div>
                </div>

            </div>

            <div class="auth-trust-bar">
                <div class="auth-trust-avatars">
                    <i class="feather-shield" style="font-size:24px;"></i>
                </div>
                <div class="auth-trust-text">
                    <strong>Secure Access</strong>
                    <span>Role-based authentication</span>
                </div>
            </div>

        </div>
    </div>

    <div class="auth-right-panel">
        <div class="auth-form-box">

            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('img/core-img/logo.png') }}"
                     class="auth-logo-img" alt="Orderer">
            </a>

            <h2>Admin Sign In</h2>
            <p class="subtitle">
                Enter your credentials to continue
            </p>

            <form action="{{ route('admin.login') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold">Email address</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="admin@ordererweb.com"
                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                           required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label fw-bold mb-0">Password</label>
                        <a href="#" class="auth-link fs-13">
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

                <a href="{{ route('login') }}"
                   class="btn btn-outline-secondary w-100 mt-3">
                    Sign in as a Buyer
                </a>

                <a href="{{ route('seller.login') }}"
                   class="btn btn-outline-secondary w-100 mt-2">
                    Sign in as a Seller
                </a>

            </form>
        </div>
    </div>

</div>
@endsection