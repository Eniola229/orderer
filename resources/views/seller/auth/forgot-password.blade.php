@extends('layouts.auth')
@section('title', 'Reset Password')
@section('content')
<div class="auth-main">

    <div class="auth-left-panel">
        <div class="auth-left-inner">

            <div class="auth-panel-logo">
                <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                     style="height:32px;filter:brightness(0) invert(1);" alt="Orderer">
                <span>Orderer</span>
            </div>

            <div class="auth-panel-tag">Account Recovery</div>

            <h1>Forgot your<br>password?</h1>
            <p>No worries. Enter your seller email and we'll send you a secure link to reset it right away.</p>

            <div class="auth-panel-features">

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-mail"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Check your inbox</strong>
                        <span>We'll send a reset link to your registered email</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-shield"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Secure reset</strong>
                        <span>Links expire after 60 minutes for your protection</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon">
                        <i class="feather-refresh-cw"></i>
                    </div>
                    <div class="auth-feat-text">
                        <strong>Back in minutes</strong>
                        <span>Set a new password and continue selling</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="auth-right-panel">
        <div class="auth-form-box">

            <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                 class="auth-logo-img" alt="Orderer">

            <h2>Forgot password?</h2>
            <p class="subtitle">
                <a href="{{ route('seller.login') }}" class="auth-link">
                    <i class="feather-arrow-left me-1"></i> Back to login
                </a>
            </p>

            @if(session('status'))
            <div class="alert alert-success mb-4">
                <i class="feather-check-circle me-2"></i>
                {{ session('status') }}
            </div>
            @endif

            <form action="{{ route('seller.password.email') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold">Email address</label>
                    <input type="email" name="email"
                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="Your seller email"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="feather-send me-2"></i> Send Reset Link
                </button>

            </form>
        </div>
    </div>

</div>
@endsection