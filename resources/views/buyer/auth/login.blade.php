@extends('layouts.auth')
@section('title', 'Sign In')

@section('content')
<div class="auth-wrapper">

    <div class="auth-left">
        <div class="auth-left-content">
            <a href="{{ route('home') }}">
                <img src="{{ asset('img/core-img/logo.png') }}"
                     alt="Orderer"
                     style="height:40px; margin-bottom:30px;">
            </a>
            <h1>Welcome back to Orderer</h1>
            <p>
                Sign in to track your orders, manage your wishlist,
                and continue shopping from where you left off.
            </p>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-box">

            <a href="{{ route('home') }}" class="auth-logo">
                <img src="{{ asset('img/core-img/logo.png') }}" alt="Orderer">
            </a>

            <h2>Sign in</h2>
            <p class="auth-subtitle">
                New to Orderer?
                <a href="{{ route('register') }}" class="orderer-auth-link">Create account</a>
            </p>

            <form action="{{ route('login') }}" method="POST" autocomplete="off">
                @csrf

                <div class="auth-form-group">
                    <label>Email address</label>
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
                    <label style="display:flex; justify-content:space-between; text-transform:none;">
                        <span style="text-transform:uppercase; font-size:13px; font-weight:600;">Password</span>
                        <a href="{{ route('password.request') }}"
                           class="orderer-auth-link"
                           style="font-size:12px; font-weight:400;">
                            Forgot password?
                        </a>
                    </label>
                    <input type="password"
                           name="password"
                           placeholder="Your password"
                           required>
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-form-group">
                    <label style="display:flex; align-items:center; gap:8px; text-transform:none;">
                        <input type="checkbox"
                               name="remember"
                               style="width:auto;">
                        <span style="font-size:13px; color:#555; font-weight:400;">
                            Keep me signed in
                        </span>
                    </label>
                </div>

                <button type="submit" class="orderer-auth-btn">
                    Sign In
                </button>

                <div class="auth-divider">or</div>

                <div style="display:flex; gap:12px; flex-direction:column;">
                    <a href="{{ route('seller.login') }}"
                       style="display:block; text-align:center; padding:11px; border:1px solid #2ECC71; color:#27AE60; border-radius:4px; font-size:14px; text-decoration:none;">
                        Sign in as a Seller
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection