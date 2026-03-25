@extends('layouts.auth')
@section('title', 'Set New Password')

@section('content')
<div class="auth-main">
    <div class="auth-left-panel">
        <div class="content">
            <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
                 style="height:40px;margin-bottom:28px;filter:brightness(0) invert(1);" alt="">
            <h1>Set new password</h1>
            <p>Choose a strong password for your seller account.</p>
        </div>
    </div>
    <div class="auth-right-panel">
        <div class="auth-form-box">

            <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
                 class="auth-logo-img" alt="Orderer">

            <h2>New password</h2>

            <form action="{{ route('seller.password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                <div class="mb-4">
                    <label class="form-label fw-bold">New password</label>
                    <input type="password" name="password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                           placeholder="Min. 8 characters" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Confirm password</label>
                    <input type="password" name="password_confirmation"
                           class="form-control form-control-lg"
                           placeholder="Repeat password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="feather-lock me-2"></i> Reset Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection