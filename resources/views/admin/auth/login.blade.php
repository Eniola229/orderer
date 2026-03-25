@extends('layouts.auth')
@section('title', 'Admin Login')

@section('content')
<div class="admin-login-bg">
    <div style="width:100%; max-width:420px; padding:20px;">

        <div style="text-align:center; margin-bottom:32px;">
            <img src="{{ asset('img/core-img/logo.png') }}"
                 alt="Orderer"
                 style="height:36px; filter:brightness(0) invert(1); margin-bottom:16px;">
            <p style="color:#888; font-size:13px; letter-spacing:2px; text-transform:uppercase;">
                Admin Portal
            </p>
        </div>

        <div style="background:#fff; border-radius:8px; padding:36px;">

            <h2 style="font-size:20px; font-weight:700; margin-bottom:24px; color:#1a1a1a;">
                Sign in to admin
            </h2>

            <form action="{{ route('admin.login') }}" method="POST" autocomplete="off">
                @csrf

                <div class="auth-form-group">
                    <label>Email address</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="admin@orderer.com"
                           class="{{ $errors->has('email') ? 'input-error' : '' }}"
                           required
                           autocomplete="off">
                    @error('email')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-form-group">
                    <label>Password</label>
                    <input type="password"
                           name="password"
                           placeholder="Admin password"
                           required
                           autocomplete="new-password">
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="orderer-auth-btn">
                    Sign In
                </button>

            </form>
        </div>

        <p style="text-align:center; margin-top:20px; font-size:12px; color:#555;">
            <a href="{{ route('home') }}" style="color:#666; text-decoration:none;">
                &larr; Back to Orderer
            </a>
        </p>

    </div>
</div>
@endsection