@extends('layouts.auth')
@section('title', 'Reset Password')

@section('content')
<div class="auth-main">
    <div class="auth-left-panel">
        <div class="content">
            <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
                 style="height:40px;margin-bottom:28px;filter:brightness(0) invert(1);" alt="">
            <h1>Reset your password</h1>
            <p>Enter your seller email and we'll send you a link to reset your password.</p>
        </div>
    </div>
    <div class="auth-right-panel">
        <div class="auth-form-box">

            <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
                 class="auth-logo-img" alt="Orderer">

            <h2>Forgot password?</h2>
            <p class="subtitle">
                <a href="{{ route('seller.login') }}" class="auth-link">
                    <i class="feather-arrow-left me-1"></i> Back to login
                </a>
            </p>

            @if(session('status'))
            <div class="alert alert-success">
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