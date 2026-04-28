@extends('layouts.marketer')
@section('title', 'My Profile')
@section('page_title', 'My Profile')
@section('breadcrumb')
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">

        {{-- ── Profile card ─────────────────────────────────────────────── --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Information</h5>
            </div>
            <div class="card-body">

                <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom">
                    <div class="avatar-text rounded-circle fw-bold"
                         style="width:72px;height:72px;font-size:26px;
                                background:#1a1f2e;color:#2ECC71;flex-shrink:0;">
                        {{ strtoupper(substr($marketer->first_name, 0, 1)) }}{{ strtoupper(substr($marketer->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">{{ $marketer->full_name }}</h5>
                        <p class="text-muted fs-13 mb-1">{{ $marketer->email }}</p>
                        <span class="badge bg-success-subtle text-success fw-semibold">Marketer</span>
                    </div>
                </div>

                <table class="table table-borderless fs-14 mb-0">
                    <tr>
                        <td class="text-muted fw-semibold ps-0" style="width:160px;">First Name</td>
                        <td>{{ $marketer->first_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold ps-0">Last Name</td>
                        <td>{{ $marketer->last_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold ps-0">Email</td>
                        <td>{{ $marketer->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold ps-0">Last Login</td>
                        <td>{{ $marketer->last_login_at ? $marketer->last_login_at->diffForHumans() : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold ps-0">Member Since</td>
                        <td>{{ $marketer->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold ps-0">Total Referrals</td>
                        <td>
                            <span class="fw-bold text-primary">
                                {{ $marketer->referredSellers()->count() }}
                            </span>
                            sellers referred
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ── Marketing code card ──────────────────────────────────────── --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Your Marketing Code</h5>
            </div>
            <div class="card-body text-center py-4">

                @if($marketer->marketing_code)
                <p class="text-muted fs-13 mb-3">
                    Share this code with sellers so they can enter it at registration.
                    All sellers who use it will be tracked under your account.
                </p>

                <div id="profileCode"
                     style="background:#1a1f2e;color:#2ECC71;font-family:'Courier New',monospace;
                            font-size:20px;font-weight:700;letter-spacing:3px;
                            padding:16px 28px;border-radius:10px;display:inline-block;
                            cursor:pointer;user-select:all;"
                     onclick="copyCode(this)"
                     title="Click to copy">
                    {{ $marketer->marketing_code }}
                </div>

                <p class="text-muted fs-11 mt-2 mb-3">Click to copy</p>

                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-outline-secondary btn-sm"
                            onclick="copyCode(document.getElementById('profileCode'))">
                        <i class="feather-copy me-1"></i> Copy Code
                    </button>
                    <form method="POST" action="{{ route('marketer.regenerate-code') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning btn-sm"
                                onclick="return confirm('Generate a brand new code? Your old one will stop working immediately.')">
                            <i class="feather-refresh-cw me-1"></i> Regenerate
                        </button>
                    </form>
                </div>

                <div class="alert alert-info mt-4 mb-0 text-start" style="font-size:13px;">
                    <i class="feather-info me-2"></i>
                    Codes starting with <strong>OR-MRT-</strong> are automatically recognised as marketer
                    codes when a seller registers. Seller referral codes are different and won't conflict.
                </div>

                @else
                <p class="text-muted fs-13 mb-4">You don't have a marketing code yet. Generate one to start tracking referrals.</p>
                <form method="POST" action="{{ route('marketer.generate-code') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-zap me-2"></i> Generate My Code
                    </button>
                </form>
                @endif

            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function copyCode(el) {
    const code = el.textContent.trim();
    navigator.clipboard.writeText(code).then(() => {
        el.style.color = '#fff';
        el.style.background = '#27AE60';
        setTimeout(() => {
            el.style.color = '#2ECC71';
            el.style.background = '#1a1f2e';
        }, 1500);
    });
}
</script>
@endpush