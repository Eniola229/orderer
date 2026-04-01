@extends('layouts.auth')
@section('title', 'Seller Sign In')
@section('content')
<div class="auth-main">

    <div class="auth-left-panel">
        <div class="auth-left-inner">

            <div class="auth-panel-logo">
                <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                     style="height:32px;filter:brightness(0) invert(1);" alt="Orderer">
                <span>Orderer</span>
            </div>

            <div class="auth-panel-tag">Seller Dashboard</div>

            <h1>Welcome back,<br>let's get selling</h1>
            <p>Manage your products, track orders, run ads and grow your business — all in one place.</p>

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
                        <span>Every sale is secured until delivery confirmed</span>
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
                        <i class="feather-bar-chart-2"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Full analytics</strong>
                        <span>Track revenue, orders and store performance</span>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label fw-bold mb-0">Password</label>
                        <a href="{{ route('seller.password.request') }}" class="auth-link fs-13">
                            Forgot password?
                        </a>
                    </div>
                    <input type="password"
                           name="password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror mt-2"
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
                    <i class="feather-log-in me-2"></i> Sign In to Dashboard
                </button>

                <div class="auth-divider">or</div>

                <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">
                    Sign in as a Buyer instead
                </a>

            </form>
        </div>
    </div>

</div>
@endsection

@include('layouts.partials.auth-seller-footer')