@extends('layouts.auth')
@section('title', 'Admin Login')

@section('content')
<div style="min-height:100vh;display:flex;align-items:stretch;">

    <div style="width:45%;background:#1a1a2e;display:flex;align-items:center;justify-content:center;padding:60px 48px;" class="d-none d-lg-flex">
        <div style="color:#fff;max-width:400px;">
            <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
                 style="height:40px;margin-bottom:32px;filter:brightness(0) invert(1);" alt="">
            <h1 style="font-size:30px;font-weight:800;color:#fff;margin-bottom:12px;">
                Admin Panel
            </h1>
            <p style="color:rgba(255,255,255,.7);font-size:14px;line-height:1.8;">
                Orderer administration portal. Authorised access only.
                All activity is logged and monitored.
            </p>
            <div style="margin-top:32px;padding:16px;background:rgba(255,255,255,.05);border-radius:8px;border-left:3px solid #2ECC71;">
                <p style="color:rgba(255,255,255,.6);font-size:12px;margin:0;">
                    <i class="feather-shield" style="color:#2ECC71;margin-right:8px;"></i>
                    Secured with role-based access control
                </p>
            </div>
        </div>
    </div>

    <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:48px 40px;background:#f8f9fa;">
        <div style="width:100%;max-width:420px;">

            <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
                 style="height:36px;margin-bottom:32px;display:block;" alt="Orderer">

            <h2 style="font-size:24px;font-weight:800;color:#1a1a1a;margin-bottom:6px;">
                Admin Sign In
            </h2>
            <p style="color:#888;font-size:14px;margin-bottom:28px;">
                Enter your admin credentials to continue
            </p>

            @include('layouts.partials.alerts')

            <form action="{{ route('admin.login') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold">Email address</label>
                    <input type="email" name="email"
                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="admin@orderer.com" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                           placeholder="Your password" required>
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

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="feather-log-in me-2"></i> Sign In
                </button>
            </form>

        </div>
    </div>

</div>
@endsection