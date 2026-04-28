@extends('layouts.auth')
@section('title', 'Marketer Login')

@section('content')
<div class="auth-main">

    <div class="auth-left-panel">
        <div class="auth-left-inner">

            <div class="auth-panel-logo">
                <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                     style="height:32px;filter:brightness(0) invert(1);" alt="Orderer">
                <span>Orderer</span>
            </div>

            <div class="auth-panel-tag">Marketer Portal</div>

            <h1>Grow with<br>every referral</h1>
            <p>Track every seller you bring onboard, generate your marketing code and watch your network grow.</p>

            <div class="auth-panel-features">

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-users"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Track your referrals</strong>
                        <span>See every seller you've invited in real time</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-code"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Your unique code</strong>
                        <span>Share your OR-MRT- code with sellers to register</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-bar-chart-2"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Date filters</strong>
                        <span>Analyse performance by any time period</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="auth-right-panel">
        <div class="auth-form-box">

            <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                 class="auth-logo-img" alt="Orderer">

            <h2>Marketer sign in</h2>
            <p class="subtitle">Access your marketing dashboard</p>

            @if(session('error'))
            <div class="alert alert-danger">
                <i class="feather-alert-circle me-2"></i>{{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('marketer.login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Email address</label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="you@example.com" autofocus required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label class="form-check-label text-muted fs-13" for="remember">Keep me signed in</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="feather-log-in me-2"></i> Sign In
                </button>
            </form>

        </div>
    </div>

</div>
@endsection