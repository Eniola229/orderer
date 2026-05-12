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
                                   value="{{ old('amount') }}" step="0.01" min="1000"
                                   @if(isset($wallet) && $wallet->balance > 0)
                                   max="{{ $wallet->balance }}"
                                   @endif
                                   placeholder="Minimum ₦1,000.00" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @if(isset($wallet) && $wallet->balance > 0)
                            <small class="text-muted">Available balance: ₦{{ number_format($wallet->balance, 2) }}</small>
                        @else
                            <small class="text-danger">No funds available to withdraw</small>
                        @endif
                    </div>

                    {{-- Bank + Account Number --}}
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Bank <span class="text-danger">*</span></label>

                            {{-- Hidden inputs the backend reads --}}
                            <input type="hidden" name="bank_code"     id="bank_code"    value="{{ old('bank_code') }}">
                            <input type="hidden" name="bank_name"     id="bank_name"    value="{{ old('bank_name') }}">
                            <input type="hidden" name="bank_country"  value="NG">
                            <input type="hidden" name="currency"      value="NGN">
                            <input type="hidden" name="payout_type"   value="bank_account">
                            <input type="hidden" name="dollar_capable" value="no">

                            {{-- Custom searchable bank picker --}}
                            <div id="bank-picker" class="bank-picker">
                                <button type="button" id="bank-trigger" class="bank-trigger" disabled>
                                    <span id="bank-trigger-label" class="bank-trigger-label text-muted">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Loading banks…
                                    </span>
                                    <i class="feather-chevron-down bank-trigger-icon"></i>
                                </button>

                                <div id="bank-dropdown" class="bank-dropdown" hidden>
                                    <div class="bank-search-wrap">
                                        <i class="feather-search bank-search-icon"></i>
                                        <input type="text" id="bank-search" class="bank-search-input"
                                               placeholder="Search bank…" autocomplete="off" spellcheck="false">
                                    </div>
                                    <ul id="bank-list" class="bank-list" role="listbox"></ul>
                                    <p id="bank-empty" class="bank-empty" hidden>No banks match your search.</p>
                                </div>
                            </div>

                            @error('bank_code')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Account Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="account_number" id="account_number"
                                       class="form-control @error('account_number') is-invalid @enderror"
                                       value="{{ old('account_number') }}"
                                       placeholder="10-digit account number"
                                       maxlength="10"
                                       autocomplete="off" required>
                                <span class="input-group-text" id="resolve_spinner" style="display:none;">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                            </div>
                            <div id="resolve_error" class="text-danger fs-12 mt-1" style="display:none;"></div>
                            @error('account_number')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Account Name (auto-filled) --}}
                    <div class="mb-4">
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
                    All withdrawals are processed in <strong>NGN</strong>. Processing takes 1–3 business hours.
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
                        Minimum withdrawal is <strong>₦1,000.00</strong>
                    </li>
                    <li class="mb-2">
                        <i class="feather-check-circle text-success me-2"></i>
                        Nigerian bank accounts only
                    </li>
                    <li class="mb-2">
                        <i class="feather-check-circle text-success me-2"></i>
                        Double-check your account number
                    </li>
                    <li class="mb-0">
                        <i class="feather-check-circle text-success me-2"></i>
                        Processing takes within 2–3 hours
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<style>
/* ── Searchable Bank Picker ────────────────────────────────────────────── */
.bank-picker {
    position: relative;
}

/* Trigger button mimics .form-select */
.bank-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: .375rem .75rem;
    font-size: .875rem;
    font-weight: 400;
    line-height: 1.5;
    color: var(--bs-body-color, #212529);
    background-color: var(--bs-body-bg, #fff);
    background-clip: padding-box;
    border: 1px solid var(--bs-border-color, #ced4da);
    border-radius: .375rem;
    cursor: pointer;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    text-align: left;
    gap: .5rem;
}
.bank-trigger:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 .25rem rgba(13,110,253,.25);
}
.bank-trigger:disabled {
    background-color: var(--bs-secondary-bg, #e9ecef);
    opacity: 1;
    cursor: not-allowed;
}
.bank-trigger.is-invalid {
    border-color: #dc3545;
}
.bank-trigger-label {
    flex: 1;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    font-size: .875rem;
}
.bank-trigger-label.selected {
    color: var(--bs-body-color, #212529);
}
.bank-trigger-icon {
    flex-shrink: 0;
    color: #6c757d;
    transition: transform .2s ease;
    font-size: .8rem;
}
.bank-picker.open .bank-trigger-icon {
    transform: rotate(180deg);
}

/* Dropdown panel */
.bank-dropdown {
    position: absolute;
    z-index: 1055;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--bs-body-bg, #fff);
    border: 1px solid var(--bs-border-color, #ced4da);
    border-radius: .375rem;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    overflow: hidden;
    animation: bankDropIn .15s ease;
}
@keyframes bankDropIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Search input inside dropdown */
.bank-search-wrap {
    position: relative;
    padding: .5rem .75rem;
    border-bottom: 1px solid var(--bs-border-color, #e9ecef);
}
.bank-search-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: .8rem;
    pointer-events: none;
}
.bank-search-input {
    width: 100%;
    padding: .3rem .5rem .3rem 1.9rem;
    font-size: .825rem;
    border: 1px solid var(--bs-border-color, #ced4da);
    border-radius: .25rem;
    outline: none;
    background: var(--bs-tertiary-bg, #f8f9fa);
    color: var(--bs-body-color, #212529);
}
.bank-search-input:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
    background: #fff;
}

/* Bank list */
.bank-list {
    list-style: none;
    margin: 0;
    padding: .25rem 0;
    max-height: 220px;
    overflow-y: auto;
    scrollbar-width: thin;
}
.bank-list::-webkit-scrollbar { width: 4px; }
.bank-list::-webkit-scrollbar-thumb { background: #ced4da; border-radius: 2px; }

.bank-item {
    padding: .45rem 1rem;
    font-size: .845rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: .5rem;
    transition: background .1s;
    color: var(--bs-body-color, #212529);
}
.bank-item:hover,
.bank-item.focused {
    background: var(--bs-tertiary-bg, #f0f4ff);
    color: #0d6efd;
}
.bank-item.selected-item {
    font-weight: 600;
    color: #0d6efd;
}
.bank-item .bank-check {
    margin-left: auto;
    font-size: .75rem;
    color: #0d6efd;
    display: none;
}
.bank-item.selected-item .bank-check {
    display: inline;
}

/* highlight matched text */
.bank-item mark {
    background: #fff3cd;
    padding: 0;
    border-radius: 2px;
    font-weight: 600;
}

.bank-empty {
    padding: .75rem 1rem;
    font-size: .825rem;
    color: #6c757d;
    margin: 0;
    text-align: center;
}
</style>

<script>
// ── DOM refs ──────────────────────────────────────────────────────────────
const bankCodeHidden = document.getElementById('bank_code');
const bankNameHidden = document.getElementById('bank_name');
const accountNumEl   = document.getElementById('account_number');
const resolveSpinner = document.getElementById('resolve_spinner');
const resolveError   = document.getElementById('resolve_error');
const verifyBadge    = document.getElementById('verify_badge');

// Bank picker elements
const picker       = document.getElementById('bank-picker');
const trigger      = document.getElementById('bank-trigger');
const triggerLabel = document.getElementById('bank-trigger-label');
const dropdown     = document.getElementById('bank-dropdown');
const searchInput  = document.getElementById('bank-search');
const bankList     = document.getElementById('bank-list');
const bankEmpty    = document.getElementById('bank-empty');

let allBanks     = [];   // [{ code, name }, …]
let selectedCode = '{{ old('bank_code') }}';
let selectedName = '{{ old('bank_name') }}';
let focusedIndex = -1;
let resolveTimer = null;

// ── Render list ───────────────────────────────────────────────────────────
function renderBankList(query = '') {
    const q = query.trim().toLowerCase();
    const filtered = q
        ? allBanks.filter(b => b.name.toLowerCase().includes(q))
        : allBanks;

    bankList.innerHTML = '';
    focusedIndex = -1;

    if (!filtered.length) {
        bankEmpty.hidden = false;
        return;
    }
    bankEmpty.hidden = true;

    filtered.forEach((bank, idx) => {
        const li = document.createElement('li');
        li.className = 'bank-item' +
            (bank.code === selectedCode ? ' selected-item' : '');
        li.setAttribute('role', 'option');
        li.dataset.code = bank.code;
        li.dataset.name = bank.name;
        li.dataset.idx  = idx;

        // Highlight matched characters
        let label = bank.name;
        if (q) {
            const re = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            label = bank.name.replace(re, '<mark>$1</mark>');
        }

        li.innerHTML = `${label}<i class="feather-check bank-check"></i>`;
        li.addEventListener('click', () => selectBank(bank.code, bank.name));
        li.addEventListener('mouseenter', () => setFocused(idx));
        bankList.appendChild(li);
    });
}

function setFocused(idx) {
    const items = bankList.querySelectorAll('.bank-item');
    items.forEach(el => el.classList.remove('focused'));
    focusedIndex = idx;
    if (items[idx]) {
        items[idx].classList.add('focused');
        items[idx].scrollIntoView({ block: 'nearest' });
    }
}

// ── Select a bank ─────────────────────────────────────────────────────────
function selectBank(code, name) {
    selectedCode = code;
    selectedName = name;

    bankCodeHidden.value = code;
    bankNameHidden.value = name;

    triggerLabel.textContent = name;
    triggerLabel.classList.add('selected');
    triggerLabel.classList.remove('text-muted');

    closeDropdown();
    clearAccountVerification();

    if (accountNumEl.value.length === 10) {
        triggerResolve();
    }
}

// ── Open / close ──────────────────────────────────────────────────────────
function openDropdown() {
    if (trigger.disabled) return;
    dropdown.hidden = false;
    picker.classList.add('open');
    searchInput.value = '';
    renderBankList('');
    searchInput.focus();

    // Scroll selected item into view
    const sel = bankList.querySelector('.selected-item');
    if (sel) sel.scrollIntoView({ block: 'nearest' });
}

function closeDropdown() {
    dropdown.hidden = true;
    picker.classList.remove('open');
}

trigger.addEventListener('click', () => {
    dropdown.hidden ? openDropdown() : closeDropdown();
});

// Close on outside click
document.addEventListener('click', e => {
    if (!picker.contains(e.target)) closeDropdown();
});

// ── Keyboard navigation ───────────────────────────────────────────────────
searchInput.addEventListener('keydown', e => {
    const items = [...bankList.querySelectorAll('.bank-item')];
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        setFocused(Math.min(focusedIndex + 1, items.length - 1));
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        setFocused(Math.max(focusedIndex - 1, 0));
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (focusedIndex >= 0 && items[focusedIndex]) {
            selectBank(items[focusedIndex].dataset.code, items[focusedIndex].dataset.name);
        }
    } else if (e.key === 'Escape') {
        closeDropdown();
        trigger.focus();
    }
});

searchInput.addEventListener('input', () => renderBankList(searchInput.value));

// ── Load Nigerian banks on page load ──────────────────────────────────────
(async function loadBanks() {
    try {
        const res = await fetch('{{ route("seller.withdrawals.banks") }}', {
            method: 'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
            },
            body: JSON.stringify({ country: 'NG' }),
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();

        if (data.status === 'ok' && data.banks && data.banks.length) {
            allBanks = data.banks;
            trigger.disabled = false;

            // Restore old value after validation error
            if (selectedCode) {
                const old = allBanks.find(b => b.code === selectedCode);
                if (old) {
                    triggerLabel.textContent = old.name;
                    triggerLabel.classList.add('selected');
                    triggerLabel.classList.remove('text-muted');
                    bankNameHidden.value = old.name;
                }
            } else {
                triggerLabel.innerHTML = '— Select bank —';
                triggerLabel.classList.add('text-muted');
            }
        } else {
            triggerLabel.textContent = '— No banks found —';
        }
    } catch (e) {
        console.error('Error loading banks:', e);
        triggerLabel.textContent = '— Failed to load. Refresh page —';
    }
})();

// ── Account number → resolve ──────────────────────────────────────────────
accountNumEl.addEventListener('input', function () {
    clearTimeout(resolveTimer);
    clearAccountVerification();

    if (!bankCodeHidden.value) return;
    if (this.value.length < 10) return;

    resolveTimer = setTimeout(triggerResolve, 800);
});

function clearAccountVerification() {
    document.getElementById('account_name_display').value = '';
    document.getElementById('account_name').value = '';
    verifyBadge.style.display  = 'none';
    resolveError.style.display = 'none';
}

async function triggerResolve() {
    const bankCode  = bankCodeHidden.value;
    const accountNo = accountNumEl.value;

    if (!bankCode || accountNo.length < 10) return;

    resolveSpinner.style.display = '';
    resolveError.style.display   = 'none';

    try {
        const res = await fetch('{{ route('seller.withdrawals.resolve-account') }}', {
            method: 'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ bank_code: bankCode, account_number: accountNo }),
        });

        const data = await res.json();

        if (data.status === 'ok') {
            document.getElementById('account_name_display').value = data.account_name;
            document.getElementById('account_name').value         = data.account_name;
            verifyBadge.style.display  = '';
            resolveError.style.display = 'none';
        } else {
            const msg = data.message === 'Invalid account.'
                ? 'Account not found. Please check the account number and selected bank.'
                : (data.message ?? 'Could not verify account. Try again.');
            resolveError.textContent   = msg;
            resolveError.style.display = '';
        }
    } catch (e) {
        resolveError.textContent   = 'Verification failed. Please try again.';
        resolveError.style.display = '';
    } finally {
        resolveSpinner.style.display = 'none';
    }
}

// ── Form submit guard ─────────────────────────────────────────────────────
document.getElementById('withdrawalForm').addEventListener('submit', function (e) {
    if (!bankCodeHidden.value) {
        e.preventDefault();
        trigger.classList.add('is-invalid');
        trigger.focus();
        return false;
    }
    const acctName = document.getElementById('account_name').value;
    if (!acctName) {
        e.preventDefault();
        resolveError.textContent   = 'Please wait for account verification to complete before submitting.';
        resolveError.style.display = '';
        accountNumEl.focus();
        return false;
    }
    trigger.classList.remove('is-invalid');
});
</script>
@endpush

@endsection