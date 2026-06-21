@extends('layouts.auth')
@section('title', 'Verify Your Phone')
@section('content')
<div class="auth-main">

    <div class="auth-left-panel">
        <div class="auth-left-inner">
            <div class="auth-panel-logo">
                <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                     style="height:32px;filter:brightness(0) invert(1);" alt="Orderer">
                <span>Orderer</span>
            </div>
            <div class="auth-panel-tag">Seller Dashboard</div>
            <h1>Almost there —<br>verify your phone</h1>
            <p>We sent a code via SMS to your phone number. Enter it to finish securing your account.</p>

            <div class="auth-panel-features">
                <div class="auth-feat-item">
                    <div class="auth-feat-icon"><i class="feather-shield"></i></div>
                    <div class="auth-feat-text">
                        <strong>Extra account security</strong>
                        <span>Phone verification helps prevent fraud</span>
                    </div>
                </div>
                <div class="auth-feat-item">
                    <div class="auth-feat-icon"><i class="feather-message-square"></i></div>
                    <div class="auth-feat-text">
                        <strong>SMS order alerts</strong>
                        <span>We'll text you when new orders come in</span>
                    </div>
                </div>
                <div class="auth-feat-item">
                    <div class="auth-feat-icon"><i class="feather-check-circle"></i></div>
                    <div class="auth-feat-text">
                        <strong>One step away</strong>
                        <span>Verify your phone and your dashboard unlocks</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-right-panel">
        <div class="auth-form-box">

            <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
                 class="auth-logo-img" alt="Orderer">

            <div style="text-align:center;margin-bottom:24px;">
                <div style="width:72px;height:72px;border-radius:50%;background:#e8faf2;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="feather-message-square" style="font-size:32px;color:#2ECC71;"></i>
                </div>
                <h2 style="margin:0 0 8px;">Enter your code</h2>
                <p class="subtitle" style="margin:0;">
                    We sent a 6-digit code via SMS to<br>
                    <strong>{{ $phone }}</strong>
                </p>
            </div>

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
            @if(session('error'))
                <div class="alert alert-danger" style="border-radius:8px;font-size:14px;">
                    <i class="feather-alert-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            <form action="{{ route('seller.phone-verification.verify') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Verification Code</label>
                    <input type="text" name="code" class="form-control form-control-lg text-center"
                           style="letter-spacing:6px;font-size:22px;" maxlength="8"
                           placeholder="------" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="feather-check me-2"></i> Verify Phone
                </button>
            </form>

        <form action="{{ route('seller.phone-verification.send') }}" method="POST" class="text-center mb-3">
            @csrf
            <button type="submit"
                    style="background:none;border:none;color:#2ECC71;font-size:14px;cursor:pointer;text-decoration:underline;">
                Didn't get a code? Resend
            </button>
        </form>

        {{-- Change phone button --}}
        <button type="button" id="changePhoneBtn" class="btn btn-outline-secondary btn-lg w-100 mb-3">
            <i class="feather-edit-2 me-2"></i> Change Phone Number
        </button>

        {{-- Change phone modal --}}
        <div id="changePhoneModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
            <div style="background:#fff;border-radius:12px;max-width:380px;width:90%;padding:24px;">
                <h5 class="fw-bold mb-3">Change Phone Number</h5>
                <form action="{{ route('seller.phone-verification.update-phone') }}" method="POST" id="changePhoneForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text fw-semibold">+234</span>
                            <input type="tel" id="phoneLocalInput" class="form-control"
                                   placeholder="8012345678" maxlength="10"
                                   pattern="[0-9]*" inputmode="numeric" required>
                        </div>
                        <small class="text-muted">Enter your number without the leading 0</small>
                        <input type="hidden" name="phone" id="phoneHiddenInput">
                        @error('phone')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" id="cancelChangePhoneBtn" class="btn btn-light w-100">Cancel</button>
                        <button type="submit" class="btn btn-primary w-100">Update</button>
                    </div>
                </form>
            </div>
        </div>

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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const changePhoneBtn = document.getElementById('changePhoneBtn');
    const changePhoneModal = document.getElementById('changePhoneModal');
    const cancelChangePhoneBtn = document.getElementById('cancelChangePhoneBtn');
    const phoneLocalInput = document.getElementById('phoneLocalInput');
    const phoneHiddenInput = document.getElementById('phoneHiddenInput');
    const changePhoneForm = document.getElementById('changePhoneForm');

    if (changePhoneBtn && changePhoneModal) {
        changePhoneBtn.addEventListener('click', () => changePhoneModal.style.display = 'flex');
        cancelChangePhoneBtn.addEventListener('click', () => changePhoneModal.style.display = 'none');
        changePhoneModal.addEventListener('click', (e) => {
            if (e.target === changePhoneModal) changePhoneModal.style.display = 'none';
        });
    }

    if (phoneLocalInput) {
        phoneLocalInput.addEventListener('input', () => {
            let digits = phoneLocalInput.value.replace(/\D/g, '');
            if (digits.startsWith('0')) digits = digits.substring(1);
            phoneLocalInput.value = digits;
        });
    }

    if (changePhoneForm) {
        changePhoneForm.addEventListener('submit', (e) => {
            let digits = phoneLocalInput.value.replace(/\D/g, '');
            if (digits.startsWith('0')) digits = digits.substring(1);

            if (digits.length !== 10) {
                e.preventDefault();
                alert('Please enter a valid 10-digit phone number (without the leading 0).');
                return;
            }

            phoneHiddenInput.value = '+234' + digits;
        });
    }

    @if ($errors->has('phone'))
        if (changePhoneModal) changePhoneModal.style.display = 'flex';
    @endif
});
</script>
@endsection