@extends('layouts.auth')
@section('title', 'Account Pending')

@section('content')
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;background:#f8f9fa;">
    <div style="max-width:520px;width:100%;text-align:center;">

        <img src="{{ asset('dashboard/assets/images/orderer-logo.png') }}"
             style="height:40px;margin-bottom:32px;" alt="Orderer">

        <div class="avatar-text avatar-xl rounded mx-auto mb-4"
             style="width:80px;height:80px;background:#D5F5E3;color:#2ECC71;font-size:32px;">
            <i class="feather-clock"></i>
        </div>

        <h3 class="fw-bold mb-3">Account under review</h3>

        <p class="text-muted mb-4" style="line-height:1.8;">
            Thank you for registering as a seller on Orderer.
            Our team is reviewing your details
            @if(auth('seller')->user()->is_verified_business)
                and the documents you submitted
            @endif
            . This usually takes <strong>within 24 hours</strong>.
        </p>

        <div class="card mb-4 text-start">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <i class="feather-check-circle text-success" style="font-size:20px;"></i>
                    <span class="fw-semibold">Account created</span>
                </div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <i class="feather-clock text-warning" style="font-size:20px;"></i>
                    <span class="fw-semibold text-muted">Documents under review</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="feather-circle text-muted" style="font-size:20px;"></i>
                    <span class="text-muted">Account approved — pending</span>
                </div>
            </div>
        </div>

        <p class="text-muted fs-13 mb-4">
            We'll email <strong>{{ auth('seller')->user()->email }}</strong> when approved.
        </p>

        <div class="d-flex gap-3 justify-content-center">
            <form action="{{ route('seller.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="feather-log-out me-1"></i> Sign Out
                </button>
            </form>
            <a href="{{ route('contact') }}" class="btn btn-outline-primary">
                <i class="feather-headphones me-1"></i> Contact Support
            </a>
        </div>

    </div>
</div>
@endsection