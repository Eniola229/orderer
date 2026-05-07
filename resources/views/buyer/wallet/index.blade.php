@extends('layouts.buyer')
@section('title', 'My Wallet')
@section('page_title', 'My Wallet')
@section('breadcrumb')
    <li class="breadcrumb-item active">Wallet</li>
@endsection

@push('head')
<script type="text/javascript" src="https://sdk.monnify.com/plugin/monnify.js"></script>
@endpush

@section('content')

<div class="row mb-4">
    <div class="col-md-6 mx-auto">
        <div class="card" style="background: linear-gradient(135deg, #27AE60, #2ECC71); color: #fff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p style="color:rgba(255,255,255,.8);font-size:13px;margin-bottom:6px;text-transform:uppercase;font-weight:600;">
                            Available Balance
                        </p>
                        <h1 style="color:#fff;font-size:42px;font-weight:800;margin-bottom:4px;">
                            ₦{{ number_format($wallet->balance, 2) }}
                        </h1>
                        <small style="color:rgba(255,255,255,.7);">NGN Wallet</small>
                    </div>
                    <i class="feather-credit-card" style="font-size:48px;color:rgba(255,255,255,.3);"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    {{-- Top Up --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Up Wallet</h5>
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label fw-bold">Amount (NGN)</label>
                    <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="number" id="topup-amount" class="form-control"
                               min="1" step="0.01" placeholder="0.00">
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap mb-3">
                    @foreach([1000, 2000, 3000, 5000, 10000, 20000] as $amt)
                    <button type="button"
                            class="btn btn-sm btn-outline-primary"
                            onclick="document.getElementById('topup-amount').value='{{ $amt }}'">
                        ₦{{ number_format($amt) }}
                    </button>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Payment Method</label>
                    <div class="d-flex flex-column gap-2">

                        <div class="gateway-option active" id="opt-monnify" onclick="selectGateway('monnify')">
                            <div class="d-flex align-items-center gap-3">
                                <div class="gateway-icon" style="background:#1A73E8;">
                                    <i class="feather-zap"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:14px;">Monnify</div>
                                    <div class="text-muted" style="font-size:11px;">Card · Bank Transfer · USSD</div>
                                </div>
                                <i class="feather-check-circle ms-auto" id="check-monnify" style="color:#0d6efd;font-size:18px;"></i>
                            </div>
                        </div>

                        <div class="gateway-option" id="opt-korapay" onclick="selectGateway('korapay')">
                            <div class="d-flex align-items-center gap-3">
                                <div class="gateway-icon" style="background:#6C2BD9;">
                                    <i class="feather-credit-card"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:14px;">Korapay</div>
                                    <div class="text-muted" style="font-size:11px;">Card · Bank Transfer</div>
                                </div>
                                <i class="feather-circle ms-auto" id="check-korapay" style="color:#adb5bd;font-size:18px;"></i>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="pay-msg" class="alert d-none mb-3" style="font-size:13px;"></div>

                <button type="button" id="pay-btn" class="btn btn-primary w-100" onclick="proceedToPayment()">
                    <i class="feather-arrow-up-circle me-2"></i> Proceed to Payment
                </button>

                {{-- Hidden Korapay form --}}
                <form id="korapay-form" action="{{ route('buyer.wallet.topup') }}" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="amount" id="korapay-amount">
                </form>

            </div>
        </div>
    </div>

    {{-- Transactions --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaction History</h5>
            </div>
            <div class="card-body p-0">
                @if($transactions->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Reference</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Balance</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $txn)
                            @php $isCredit = in_array($txn->type, ['credit','escrow_refund','referral_credit']); @endphp
                            <tr>
                                <td><code class="fs-12">{{ Str::limit($txn->reference, 18) }}</code></td>
                                <td>
                                    <span class="badge orderer-badge {{ $isCredit ? 'badge-approved' : 'badge-pending' }}">
                                        {{ str_replace('_', ' ', ucfirst($txn->type)) }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                    {{ $isCredit ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="fw-semibold">₦{{ number_format($txn->balance_after, 2) }}</td>
                                <td class="text-muted fs-12">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $transactions->links() }}</div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="feather-activity mb-2 d-block" style="font-size:36px;"></i>
                    No transactions yet.
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ── Verification popup overlay ────────────────────────────────────────────── --}}
<div id="verify-overlay" style="
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.55);
    z-index: 9999;
    align-items: center;
    justify-content: center;
">
    <div style="
        background: #fff;
        border-radius: 16px;
        padding: 40px 36px;
        max-width: 380px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        animation: popIn .25s ease;
    ">

        {{-- Loading state --}}
        <div id="verify-state-loading">
            <div style="
                width: 64px; height: 64px;
                border: 5px solid #e9ecef;
                border-top-color: #0d6efd;
                border-radius: 50%;
                margin: 0 auto 20px;
                animation: spin 0.8s linear infinite;
            "></div>
            <h5 class="fw-bold mb-1">Verifying Payment</h5>
            <p class="text-muted mb-0" style="font-size:14px;">
                Please wait while we confirm your payment…
            </p>
        </div>

        {{-- Success state --}}
        <div id="verify-state-success" style="display:none;">
            <div style="
                width: 64px; height: 64px;
                background: #d1fae5;
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                margin: 0 auto 20px;
            ">
                <i class="feather-check" style="font-size:28px;color:#059669;"></i>
            </div>
            <h5 class="fw-bold mb-1 text-success">Payment Successful!</h5>
            <p class="text-muted mb-0" style="font-size:14px;" id="verify-success-msg"></p>
            <p class="text-muted mt-2 mb-0" style="font-size:12px;">Refreshing your balance…</p>
        </div>

        {{-- Error state --}}
        <div id="verify-state-error" style="display:none;">
            <div style="
                width: 64px; height: 64px;
                background: #fee2e2;
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                margin: 0 auto 20px;
            ">
                <i class="feather-x" style="font-size:28px;color:#dc2626;"></i>
            </div>
            <h5 class="fw-bold mb-1 text-danger">Verification Failed</h5>
            <p class="text-muted mb-3" style="font-size:14px;" id="verify-error-msg"></p>
            <button id="verify-close-btn"
                    class="btn btn-outline-secondary btn-sm"
                    onclick="closeVerifyPopup()">
                Close
            </button>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.gateway-option {
    padding: 12px 14px;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color .15s, background .15s;
}
.gateway-option:hover { border-color: #adb5bd; }
.gateway-option.active {
    border-color: #0d6efd;
    background: rgba(13,110,253,.04);
}
.gateway-icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.gateway-icon i { color:#fff; font-size:15px; }

@keyframes spin {
    to { transform: rotate(360deg); }
}
@keyframes popIn {
    from { transform: scale(.85); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
// ─── state ────────────────────────────────────────────────────────────────────
var _gateway = 'monnify';

// ─── gateway picker ───────────────────────────────────────────────────────────
function selectGateway(g) {
    _gateway = g;
    ['monnify','korapay'].forEach(function(id) {
        document.getElementById('opt-' + id).classList.remove('active');
        var chk = document.getElementById('check-' + id);
        chk.className = 'feather-circle ms-auto';
        chk.style.color = '#adb5bd';
    });
    document.getElementById('opt-' + g).classList.add('active');
    var sel = document.getElementById('check-' + g);
    sel.className = 'feather-check-circle ms-auto';
    sel.style.color = '#0d6efd';
    hideMsg();
}

// ─── UI helpers ───────────────────────────────────────────────────────────────
function showMsg(msg, type) {
    var box = document.getElementById('pay-msg');
    box.className = 'alert alert-' + type + ' mb-3';
    box.textContent = msg;
}
function hideMsg() {
    document.getElementById('pay-msg').className = 'alert d-none mb-3';
}
function setBusy(label) {
    var btn = document.getElementById('pay-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + label;
}
function setIdle() {
    var btn = document.getElementById('pay-btn');
    btn.disabled = false;
    btn.innerHTML = '<i class="feather-arrow-up-circle me-2"></i> Proceed to Payment';
}

// ─── Verify popup helpers ─────────────────────────────────────────────────────
function showVerifyPopup() {
    document.getElementById('verify-overlay').style.display      = 'flex';
    document.getElementById('verify-state-loading').style.display = 'block';
    document.getElementById('verify-state-success').style.display = 'none';
    document.getElementById('verify-state-error').style.display   = 'none';
    document.getElementById('verify-close-btn').style.display     = 'none';
}

function showVerifySuccess(message) {
    document.getElementById('verify-state-loading').style.display = 'none';
    document.getElementById('verify-state-success').style.display = 'block';
    document.getElementById('verify-state-error').style.display   = 'none';
    document.getElementById('verify-success-msg').textContent     = message;
    document.getElementById('verify-close-btn').style.display     = 'none';
    setTimeout(function() { window.location.reload(); }, 2500);
}

function showVerifyError(message) {
    document.getElementById('verify-state-loading').style.display = 'none';
    document.getElementById('verify-state-success').style.display = 'none';
    document.getElementById('verify-state-error').style.display   = 'block';
    document.getElementById('verify-error-msg').textContent       = message;
    document.getElementById('verify-close-btn').style.display     = 'inline-block';
}

function closeVerifyPopup() {
    document.getElementById('verify-overlay').style.display = 'none';
}

// ─── main ─────────────────────────────────────────────────────────────────────
function proceedToPayment() {
    hideMsg();

    var amount = parseFloat(document.getElementById('topup-amount').value);
    if (!amount || amount < 1) {
        showMsg('Please enter a valid amount (minimum ₦1).', 'danger');
        return;
    }

    if (_gateway === 'korapay') {
        document.getElementById('korapay-amount').value = amount;
        document.getElementById('korapay-form').submit();
        return;
    }

    if (typeof window.MonnifySDK === 'undefined') {
        showMsg('Payment SDK is still loading — please wait a moment and try again.', 'warning');
        return;
    }

    setBusy('Initializing…');

    fetch('{{ route('buyer.wallet.monnify.init') }}', {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept':       'application/json',
        },
        body: JSON.stringify({ amount: amount }),
    })
    .then(function(res) {
        return res.json().then(function(body) {
            if (!res.ok) {
                var msg = body.message
                    || (body.errors ? Object.values(body.errors).flat().join(' ') : null)
                    || ('Error ' + res.status);
                throw new Error(msg);
            }
            return body;
        });
    })
    .then(function(data) {
        setIdle();
        console.log('[Monnify init] server response:', data);

        if (!data.paymentReference) {
            showMsg('Server did not return a payment reference.', 'danger');
            return;
        }

        window.MonnifySDK.initialize({
            amount:             data.amount,
            currency:           'NGN',
            reference:          data.paymentReference,
            customerFullName:   data.customerName,
            customerEmail:      data.email,
            apiKey:             data.apiKey,
            contractCode:       data.contractCode,
            paymentDescription: 'Wallet Top-up',
            isTestMode:         {{ app()->environment('production') ? 'false' : 'true' }},

            onLoadStart:    function() { console.log('[Monnify] loading...'); },
            onLoadComplete: function() { console.log('[Monnify] ready'); },

            onComplete: function(response) {
                console.log('[Monnify] onComplete fired:', JSON.stringify(response));
                var ref = (response && response.paymentReference)
                    ? response.paymentReference
                    : data.paymentReference;
                verifyMonnifyPayment(ref);
            },

            onClose: function(closeData) {
                console.log('[Monnify] onClose fired:', JSON.stringify(closeData));
                if (closeData && closeData.paymentStatus === 'USER_CANCELLED') {
                    showMsg('Payment was cancelled.', 'warning');
                    return;
                }
                if (closeData && closeData.paymentReference) {
                    console.log('[Monnify] safety net verify from onClose');
                    verifyMonnifyPayment(closeData.paymentReference);
                }
            },
        });
    })
    .catch(function(err) {
        setIdle();
        showMsg(err.message || 'Could not initialize payment. Please try again.', 'danger');
        console.error('[Monnify init]', err);
    });
}

// ─── verify ───────────────────────────────────────────────────────────────────
function verifyMonnifyPayment(reference) {
    showVerifyPopup();  // ← open popup immediately
    console.log('[Monnify verify] ref:', reference);

    fetch('{{ route('buyer.wallet.monnify.verify') }}', {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept':       'application/json',
        },
        body: JSON.stringify({ reference: reference }),
    })
    .then(function(res) {
        return res.json().then(function(body) {
            console.log('[Monnify verify] response:', body);
            if (!res.ok) throw new Error(body.message || ('Error ' + res.status));
            return body;
        });
    })
    .then(function(data) {
        setIdle();
        if (data.success) {
            showVerifySuccess(data.message);
        } else {
            showVerifyError(data.message || 'Payment was not successful.');
        }
    })
    .catch(function(err) {
        setIdle();
        showVerifyError(err.message || 'Verification failed. Contact support if you were charged.');
        console.error('[Monnify verify]', err);
    });
}
</script>
@endpush