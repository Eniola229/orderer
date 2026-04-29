@extends('layouts.seller')
@section('title', 'Request Withdrawal')
@section('page_title', 'Request Withdrawal')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.withdrawals.index') }}">Withdrawals</a></li>
    <li class="breadcrumb-item active">New Request</li>
@endsection

@section('content')

<div class="row">
 
    {{-- Left --}}
    <div class="col-lg-8">

        {{-- Balance card --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fs-12 mb-1">Available to withdraw</p>
                        <p class="fw-bold text-success mb-0" style="font-size:32px;">
                            ₦{{ number_format($wallet->balance, 2) }}
                        </p>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:56px;height:56px;background:#D5F5E3;">
                        <i class="feather-dollar-sign text-success" style="font-size:24px;"></i>
                    </div>
                </div>
                <p class="text-muted fs-13 mt-2">
                    Supported countries for direct bank payout: <strong>NGN</strong>.
                    If your country isn't listed, you can still withdraw using a USD-enabled account such as
                    <strong>Grey, Geegpay, Cleva, or Chipper Cash</strong> — select <em>United States (USD)</em>
                    as the country and choose <em>Yes — it accepts USD</em>.
                </p>
            </div>
        </div>

        <form action="{{ route('seller.withdrawals.store') }}" method="POST" id="withdrawalForm">
            @csrf

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Withdrawal Details</h5>
                </div>
                <div class="card-body">

                {{-- Amount --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Amount (NGN) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="number" name="amount"
                               class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount') }}" step="0.01" min="10"
                               {{-- Fix: Only add max if wallet balance exists and is greater than 0 --}}
                               @if(isset($wallet) && $wallet->balance > 0)
                               max="{{ $wallet->balance }}"
                               @endif
                               placeholder="Minimum ₦100.00" required>
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- Show wallet balance info --}}
                    @if(isset($wallet) && $wallet->balance > 0)
                    <small class="text-muted">Available balance: ₦{{ number_format($wallet->balance, 2) }}</small>
                    @else
                    <small class="text-danger">No funds available to withdraw</small>
                    @endif
                </div>

                    {{-- Payout type --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Payout Method <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 mt-1">
                            <label class="d-flex align-items-center gap-2 fs-13 fw-normal cursor-pointer">
                                <input type="radio" name="payout_type" value="bank_account"
                                       {{ old('payout_type', 'bank_account') === 'bank_account' ? 'checked' : '' }}
                                       id="type_bank">
                                Bank Account
                            </label>
                            <label class="d-flex align-items-center gap-2 fs-13 fw-normal cursor-pointer">
                                <input type="radio" name="payout_type" value="mobile_money"
                                       {{ old('payout_type') === 'mobile_money' ? 'checked' : '' }}
                                       id="type_momo">
                                Mobile Money
                            </label>
                        </div>
                    </div>

                    {{-- Bank account fields --}}
                    <div id="bank_fields">

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Bank Country <span class="text-danger">*</span></label>
                                <select name="bank_country" id="bank_country" class="form-select" required>
                                    <option value="">— Select country —</option>
                                    <option value="NG" {{ old('bank_country') === 'NG' ? 'selected' : '' }}>Nigeria (NGN)</option>
                                    <option value="GH" {{ old('bank_country') === 'GH' ? 'selected' : '' }}>Ghana (GHS)</option>
                                    <option value="KE" {{ old('bank_country') === 'KE' ? 'selected' : '' }}>Kenya (KES)</option>
                                    <option value="ZA" {{ old('bank_country') === 'ZA' ? 'selected' : '' }}>South Africa (ZAR)</option>
                                    <option value="US" {{ old('bank_country') === 'US' ? 'selected' : '' }}>United States (NGN)</option>
                                    <option value="GB" {{ old('bank_country') === 'GB' ? 'selected' : '' }}>United Kingdom (GBP)</option>
                                </select>
                                <input type="hidden" name="currency" id="currency" value="{{ old('currency', '') }}">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Account Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="account_number" id="account_number"
                                           class="form-control @error('account_number') is-invalid @enderror"
                                           value="{{ old('account_number') }}" placeholder="Account number"
                                           autocomplete="off">
                                    <span class="input-group-text" id="resolve_spinner" style="display:none;">
                                        <span class="spinner-border spinner-border-sm"></span>
                                    </span>
                                </div>
                                <div id="resolve_error" class="text-danger fs-12 mt-1" style="display:none;"></div>
                                @error('account_number')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Bank <span class="text-danger">*</span></label>
                                <select name="bank_code" id="bank_code" class="form-select" disabled>
                                    <option value="">— Select country first —</option>
                                </select>
                                <input type="hidden" name="bank_name" id="bank_name" value="{{ old('bank_name') }}">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Account Name</label>
                                <div class="input-group">
                                    <input type="text" id="account_name_display"
                                           class="form-control @error('account_name') is-invalid @enderror"
                                           value="{{ old('account_name') }}"
                                           placeholder="Auto-filled after verification"
                                           readonly>
                                    <span class="input-group-text" id="verify_badge" style="display:none;">
                                        <i class="feather-check-circle text-success"></i>
                                    </span>
                                </div>
                                <input type="hidden" name="account_name" id="account_name" value="{{ old('account_name') }}">
                                @error('account_name')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Can this account receive USD? <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-1">
                                <label class="d-flex align-items-center gap-2 fs-13 fw-normal cursor-pointer">
                                    <input type="radio" name="dollar_capable" value="yes"
                                           {{ old('dollar_capable', 'no') === 'yes' ? 'checked' : '' }}>
                                    Yes — it accepts USD
                                </label>
                                <label class="d-flex align-items-center gap-2 fs-13 fw-normal cursor-pointer">
                                    <input type="radio" name="dollar_capable" value="no"
                                           {{ old('dollar_capable', 'no') === 'no' ? 'checked' : '' }}>
                                    No — only (NGN)
                                </label>
                            </div>
                            @error('dollar_capable')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>

                    </div>
                    
                    {{-- Mobile money fields --}}
                    <div id="momo_fields" style="display:none;">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_number"
                                       class="form-control @error('mobile_number') is-invalid @enderror"
                                       value="{{ old('mobile_number') }}" placeholder="e.g. 254712345678">
                                @error('mobile_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Account Name <span class="text-danger">*</span></label>
                                <input type="text" name="momo_account_name"
                                       class="form-control @error('momo_account_name') is-invalid @enderror"
                                       value="{{ old('momo_account_name') }}" placeholder="Name on account">
                                @error('momo_account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Mobile Operator Slug <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_money_operator"
                                       class="form-control @error('mobile_money_operator') is-invalid @enderror"
                                       value="{{ old('mobile_money_operator') }}"
                                       placeholder="e.g. safaricom-ke, mtn-gh">
                                <div class="form-text">Use the operator slug from your country.</div>
                                @error('mobile_money_operator')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                                <select name="mobile_money_country" id="mobile_money_country" class="form-select">
                                    <option value="">— Select country —</option>
                                    <option value="KE" {{ old('mobile_money_country') === 'KE' ? 'selected' : '' }}>Kenya (KES)</option>
                                    <option value="GH" {{ old('mobile_money_country', 'GH') === 'GH' ? 'selected' : '' }}>Ghana (GHS)</option>
                                </select>
                                <input type="hidden" name="mobile_money_currency" id="mobile_money_currency">
                            </div>
                        </div>
                    </div>

                    {{-- Note --}}
                    <div class="mb-0">
                        <label class="form-label fw-bold">Note <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea name="note" class="form-control" rows="2"
                                  placeholder="Any additional info for the admin">{{ old('note') }}</textarea>
                    </div>

                </div>
            </div>
        </form>

    </div>

    {{-- Right --}}
    <div class="col-lg-4">

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Submit</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-3">
                    <i class="feather-info me-2"></i>
                    All withdrawals are processed in <strong>NGN</strong>. Processing takes 1–3 business days.
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" form="withdrawalForm" class="btn btn-primary">
                        <i class="feather-send me-2"></i> Submit Request
                    </button>
                    <a href="{{ route('seller.withdrawals.index') }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Important Notes</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled fs-13 text-muted">
                    <li class="mb-2">
                        <i class="feather-check-circle text-success me-2"></i>
                        Minimum withdrawal is <strong>₦100.00</strong>
                    </li>
                    <li class="mb-2">
                        <i class="feather-check-circle text-success me-2"></i>
                        Ensure account can receive NGN transfers
                    </li>
                    <li class="mb-2">
                        <i class="feather-check-circle text-success me-2"></i>
                        Double-check your account number
                    </li>
                    <li class="mb-0">
                        <i class="feather-check-circle text-success me-2"></i>
                        Processing takes within 2-3 hours
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// Currency mapping
const currencyMap = {
    'NG': 'NGN',
    'GH': 'GHS',
    'KE': 'KES',
    'ZA': 'ZAR',
    'US': 'NGN',
    'GB': 'GBP'
};

const bankCountryEl  = document.getElementById('bank_country');
const bankCodeEl     = document.getElementById('bank_code');
const bankNameEl     = document.getElementById('bank_name');
const accountNumEl   = document.getElementById('account_number');
const accountNameEl  = document.getElementById('account_name');
const resolveSpinner = document.getElementById('resolve_spinner');
const resolveError   = document.getElementById('resolve_error');
const verifyBadge    = document.getElementById('verify_badge');
const currencyHidden = document.getElementById('currency');

let resolveTimer = null;

// ── Step 0: Set currency when country changes ──────────────────────────────
bankCountryEl.addEventListener('change', function () {
    const country = this.value;
    if (country && currencyMap[country]) {
        currencyHidden.value = currencyMap[country];
    } else {
        currencyHidden.value = '';
    }
    
    // Clear account verification when country changes
    document.getElementById('account_name_display').value = '';
    document.getElementById('account_name').value = '';
    verifyBadge.style.display = 'none';
    resolveError.style.display = 'none';
    
    // Load banks
    loadBanks(country);
});

// ── Step 1: country selected → load banks ──────────────────────────────────
async function loadBanks(country) {
    if (!country || ['US', 'GB'].includes(country)) {
        bankCodeEl.innerHTML = '<option value="">— Manual entry for this country —</option>';
        bankCodeEl.disabled = true;
        return;
    }
    
    try {
        const res = await fetch('{{ route("seller.withdrawals.banks") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ country: country })
        });
        
        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }
        
        const data = await res.json();
        
        if (data.status === 'ok' && data.banks && data.banks.length) {
            bankCodeEl.innerHTML = '<option value="">— Select bank —</option>' +
                data.banks.map(b =>
                    `<option value="${b.code}" data-name="${b.name}">${b.name}</option>`
                ).join('');
            bankCodeEl.disabled = false;
        } else {
            bankCodeEl.innerHTML = '<option value="">— No banks found —</option>';
            bankCodeEl.disabled = true;
        }
    } catch (e) {
        console.error('Error loading banks:', e);
        bankCodeEl.innerHTML = '<option value="">— Failed to load banks —</option>';
        bankCodeEl.disabled = true;
    }
}

// ── Step 2: bank selected → store bank name ────────────────────────────────
bankCodeEl.addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    bankNameEl.value = selected?.dataset?.name ?? '';

    if (accountNumEl.value.length >= 8) {
        triggerResolve();
    }
});

// ── Step 3: account number entered → resolve after typing stops ────────────
accountNumEl.addEventListener('input', function () {
    clearTimeout(resolveTimer);
    document.getElementById('account_name_display').value = '';
    document.getElementById('account_name').value = '';
    verifyBadge.style.display = 'none';
    resolveError.style.display = 'none';

    const country = bankCountryEl.value;
    const bank    = bankCodeEl.value;

    if (!['NG', 'KE', 'ZA', 'GH'].includes(country)) return;
    if (!bank) return;
    if (this.value.length < 8) return;

    resolveTimer = setTimeout(triggerResolve, 800);
});

async function triggerResolve() {
    const country   = bankCountryEl.value;
    const bankCode  = bankCodeEl.value;
    const accountNo = accountNumEl.value;

    resolveSpinner.style.display = '';
    resolveError.style.display   = 'none';
    accountNameEl.value          = '';

    try {
        const res  = await fetch('{{ route('seller.withdrawals.resolve-account') }}', {
            method:  'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                bank_code:      bankCode,
                account_number: accountNo,
                country:        country,
            }),
        });

        const data = await res.json();

        if (data.status === 'ok') {
            document.getElementById('account_name_display').value = data.account_name;
            document.getElementById('account_name').value = data.account_name;
            verifyBadge.style.display = '';
            resolveError.style.display = 'none';
        } else {
            const msg = data.message === 'Invalid account.'
                ? 'Account not found. Please check the account number and selected bank.'
                : (data.message ?? 'Could not verify account. Try again.');
            resolveError.textContent = msg;
            resolveError.style.display = '';
        }
    } catch (e) {
        resolveError.textContent = 'Verification failed. Please try again.';
        resolveError.style.display = '';
    } finally {
        resolveSpinner.style.display = 'none';
    }
}

// ── Payout type toggle ─────────────────────────────────────────────────────
const bankFields = document.getElementById('bank_fields');
const momoFields = document.getElementById('momo_fields');
const mobileMoneyCountry = document.getElementById('mobile_money_country');
const mobileMoneyCurrency = document.getElementById('mobile_money_currency');

function togglePayoutType() {
    const isMomo = document.getElementById('type_momo').checked;
    bankFields.style.display = isMomo ? 'none' : 'block';
    momoFields.style.display = isMomo ? 'block' : 'none';
    
    // Remove required from bank fields when hidden
    const bankRequired = document.querySelectorAll('#bank_fields input, #bank_fields select');
    bankRequired.forEach(field => {
        if (field.hasAttribute('required')) {
            field.removeAttribute('required');
        }
    });
}

// Mobile money country currency update
if (mobileMoneyCountry) {
    mobileMoneyCountry.addEventListener('change', function() {
        const country = this.value;
        if (country && currencyMap[country]) {
            mobileMoneyCurrency.value = currencyMap[country];
        } else {
            mobileMoneyCurrency.value = '';
        }
    });
    
    // Trigger on load
    if (mobileMoneyCountry.value) {
        mobileMoneyCurrency.value = currencyMap[mobileMoneyCountry.value] || '';
    }
}

document.getElementById('type_bank').addEventListener('change', togglePayoutType);
document.getElementById('type_momo').addEventListener('change', togglePayoutType);
togglePayoutType();

// Set initial currency on page load
if (bankCountryEl.value && currencyMap[bankCountryEl.value]) {
    currencyHidden.value = currencyMap[bankCountryEl.value];
}

document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
    const country  = bankCountryEl.value;
    const isMomo   = document.getElementById('type_momo').checked;
    const acctName = document.getElementById('account_name').value;

    if (!isMomo && ['NG', 'KE', 'ZA', 'GH'].includes(country)) {
        if (!acctName) {
            e.preventDefault();
            resolveError.textContent = 'Please wait for account verification to complete before submitting.';
            resolveError.style.display = '';
            document.getElementById('account_number').focus();
            return false;
        }
    }
});
</script>
@endpush

@endsection