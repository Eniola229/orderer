@extends('layouts.auth')
@section('title', 'Verify Your Email')
@section('content')
<div class="auth-main">

    {{-- Left panel (same style as seller login) --}}
    <div class="auth-left-panel">
        <div class="auth-left-inner">

            <div class="auth-panel-logo">
                <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                     style="height:32px;filter:brightness(0) invert(1);" alt="Orderer">
                <span>Orderer</span>
            </div>

            <div class="auth-panel-tag">Seller Dashboard</div>

            <h1>One last step —<br>verify your email</h1>
            <p>We sent a verification link to your inbox. Click it to activate your seller account and start selling.</p>

            <div class="auth-panel-features">

                <div class="auth-feat-item">
                    <div class="auth-feat-icon"><i class="feather-shield"></i></div>
                    <div class="auth-feat-text">
                        <strong>Secure your account</strong>
                        <span>Email verification keeps your account protected</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon"><i class="feather-bell"></i></div>
                    <div class="auth-feat-text">
                        <strong>Get order alerts</strong>
                        <span>We'll notify you of new orders and payouts</span>
                    </div>
                </div>

                <div class="auth-feat-item">
                    <div class="auth-feat-icon"><i class="feather-check-circle"></i></div>
                    <div class="auth-feat-text">
                        <strong>Start selling immediately</strong>
                        <span>Once verified, your dashboard is ready to go</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Right panel --}}
    <div class="auth-right-panel">
        <div class="auth-form-box">

            <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                 class="auth-logo-img" alt="Orderer">

            {{-- Big envelope icon --}}
            <div style="text-align:center;margin-bottom:24px;">
                <div style="width:72px;height:72px;border-radius:50%;background:#e8faf2;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="feather-mail" style="font-size:32px;color:#2ECC71;"></i>
                </div>
                <h2 style="margin:0 0 8px;">Check your inbox</h2>
                <p class="subtitle" style="margin:0;">
                    We sent a verification link to<br>
                    <strong>{{ auth('seller')->user()->email }}</strong>
                </p>
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success" style="border-radius:8px;font-size:14px;">
                    <i class="feather-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info" style="border-radius:8px;font-size:14px;">
                    <i class="feather-info me-2"></i>{{ session('info') }}
                </div>
            @endif

            {{-- Instructions --}}
            <div style="background:#f8f9fa;border-radius:10px;padding:20px;margin-bottom:24px;font-size:14px;color:#555;">
                <p style="margin:0 0 10px;font-weight:600;color:#212529;">Didn't get the email?</p>
                <ul style="margin:0;padding-left:18px;line-height:1.8;">
                    <li>Check your <strong>spam or junk</strong> folder</li>
                    <li>Make sure <strong>{{ auth('seller')->user()->email }}</strong> is correct</li>
                    <li>Click the button below to resend</li>
                </ul>
            </div>

            {{-- Resend form --}}
            <form action="{{ route('seller.verification.resend') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="feather-send me-2"></i> Resend Verification Email
                </button>
            </form>

            {{-- Logout link --}}
            <form action="{{ route('seller.logout') }}" method="POST" class="text-center mt-3">
                @csrf
                <button type="submit"
                        style="background:none;border:none;color:#888;font-size:13px;cursor:pointer;text-decoration:underline;">
                    Sign out and use a different account
                </button>
            </form>

        </div>
    </div>

</div>
@endsection