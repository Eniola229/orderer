@extends('layouts.seller')
@section('title', 'Wallet')
@section('page_title', 'Wallet & Earnings')
@section('breadcrumb')
    <li class="breadcrumb-item active">Wallet</li>
@endsection

@push('head')
<script type="text/javascript" src="https://sdk.monnify.com/plugin/monnify.js"></script>
@endpush

@section('content')

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Available Balance</p>
                        <h2 class="fw-bold mb-0 text-success">₦{{ number_format($wallet->balance, 2) }}</h2>
                        <small class="text-muted">Ready to withdraw</small>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#D5F5E3;color:#2ECC71;">
                        <i class="feather-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">In Escrow</p>
                        <h2 class="fw-bold mb-0 text-warning">₦{{ number_format($escrowBalance, 2) }}</h2>
                        <small class="text-muted">Pending delivery</small>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#FEF9E7;color:#F39C12;">
                        <i class="feather-lock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Ads Balance</p>
                        <h2 class="fw-bold mb-0 text-primary">₦{{ number_format($wallet->ads_balance, 2) }}</h2>
                        <small class="text-muted">For promotions</small>
                    </div>
                    <div class="avatar-text avatar-lg rounded" style="background:#EBF5FB;color:#2980B9;">
                        <i class="feather-trending-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    {{-- Actions --}}
    <div class="col-lg-4">

        {{-- Top Up Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Up</h5>
            </div>
            <div class="card-body">

                {{-- Top-up type tabs --}}
                <div class="d-flex gap-2 mb-3">
                    <button type="button" id="tab-wallet"
                            class="btn btn-primary btn-sm flex-fill"
                            onclick="selectTopupType('wallet')">
                        Wallet
                    </button>
                    <button type="button" id="tab-ads"
                            class="btn btn-outline-primary btn-sm flex-fill"
                            onclick="selectTopupType('ads')">
                        Ads Balance
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Amount (NGN)</label>
                    <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="number" id="topup-amount" class="form-control"
                               min="1" step="0.01" placeholder="0.00">
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap mb-3">
                    @foreach([1000, 2000, 5000, 10000, 20000, 50000] as $amt)
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            onclick="document.getElementById('topup-amount').value='{{ $amt }}'">
                        ₦{{ number_format($amt) }}
                    </button>
                    @endforeach
                </div>

                {{-- Gateway picker --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Payment Method</label>
                    <div class="d-flex flex-column gap-2">

<!--                     <div class="gateway-option" id="opt-monnify" onclick="selectGateway('monnify')">
                            <div class="d-flex align-items-center gap-3">
                                <div class="gateway-icon" style="background:#1A73E8;">
                                    <i class="feather-zap"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:14px;">Monnify</div>
                                    <div class="text-muted" style="font-size:11px;">Card · Bank Transfer · USSD</div>
                                </div>
                                <i class="feather-check-circle ms-auto" id="check-monnify"
                                   style="color:#0d6efd;font-size:18px;"></i>
                            </div>
                        </div> -->

                        <div class="gateway-option active" id="opt-korapay" onclick="selectGateway('korapay')">
                            <div class="d-flex align-items-center gap-3">
                                <div class="gateway-icon" style="background:#6C2BD9;">
                                    <i class="feather-credit-card"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:14px;">Korapay</div>
                                    <div class="text-muted" style="font-size:11px;">Card · Bank Transfer</div>
                                </div>
                                <i class="feather-check-circle ms-auto" id="check-korapay"
                                   style="color:#adb5bd;font-size:18px;"></i>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="pay-msg" class="alert d-none mb-3" style="font-size:13px;"></div>

                <button type="button" id="pay-btn" class="btn btn-primary w-100"
                        onclick="proceedToPayment()">
                    <i class="feather-arrow-up-circle me-2"></i> Proceed to Payment
                </button>

                {{-- Hidden Korapay forms --}}
                <form id="korapay-wallet-form" action="{{ route('seller.wallet.topup') }}"
                      method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="amount" id="korapay-wallet-amount">
                </form>
                <form id="korapay-ads-form" action="{{ route('seller.wallet.topup.ads') }}"
                      method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="amount" id="korapay-ads-amount">
                </form>

            </div>
        </div>

        {{-- Withdraw --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Withdraw Funds</h5>
            </div>
            <div class="card-body">
                @if($wallet->balance >= 10)
                    <a href="{{ route('seller.withdrawals.create') }}" class="btn btn-primary w-100">
                        <i class="feather-arrow-up-right me-2"></i> Request Withdrawal
                    </a>
                    <p class="text-muted fs-12 text-center mt-2 mb-0">Min withdrawal: ₦1000.00</p>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="feather-info me-2"></i>
                        Minimum ₦1000.00 required to withdraw.<br>
                        Current: <strong>₦{{ number_format($wallet->balance, 2) }}</strong>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Transaction History (unchanged) --}}
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
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Balance After</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $txn)
                            @php
                                $isCredit    = in_array($txn->type, ['credit','escrow_release','referral_credit','escrow_refund']);
                                $statusColors = [
                                    'completed' => '#28a745',
                                    'pending'   => '#ffc107',
                                    'failed'    => '#dc3545',
                                    'reversed'  => '#6c757d',
                                ];
                                $statusColor = $statusColors[$txn->status] ?? '#6c757d';
                            @endphp
                            <tr class="transaction-row" data-id="{{ $txn->id }}" style="cursor:pointer;">
                                <td><code class="fs-12">{{ Str::limit($txn->reference, 20) }}</code></td>
                                <td>
                                    <span class="badge orderer-badge {{ $isCredit ? 'badge-approved' : 'badge-pending' }}">
                                        {{ str_replace('_', ' ', ucfirst($txn->type)) }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                    {{ $isCredit ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="fw-semibold">₦{{ number_format($txn->balance_after, 2) }}</td>
                                <td>
                                    <span class="badge" style="
                                        background-color:{{ $statusColor }};
                                        color:{{ $txn->status === 'pending' ? '#212529' : '#ffffff' }};
                                        padding:5px 10px;border-radius:4px;font-size:11px;font-weight:600;">
                                        {{ ucfirst($txn->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <i class="feather-chevron-down" style="font-size:16px;color:#6c757d;"></i>
                                </td>
                            </tr>
                            <tr class="transaction-details" id="details-{{ $txn->id }}" style="display:none;">
                                <td colspan="7" style="background:#f8f9fa;">
                                    <div style="padding:15px;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>Transaction Details</strong></p>
                                                <table style="width:100%;font-size:13px;">
                                                    <tr>
                                                        <td style="padding:5px 0;color:#6c757d;">Reference:</td>
                                                        <td><code>{{ $txn->reference }}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px 0;color:#6c757d;">Type:</td>
                                                        <td>{{ str_replace('_', ' ', ucfirst($txn->type)) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px 0;color:#6c757d;">Amount:</td>
                                                        <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                                            {{ $isCredit ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px 0;color:#6c757d;">Balance Before:</td>
                                                        <td>₦{{ number_format($txn->balance_before, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px 0;color:#6c757d;">Balance After:</td>
                                                        <td>₦{{ number_format($txn->balance_after, 2) }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>Additional Information</strong></p>
                                                <table style="width:100%;font-size:13px;">
                                                    <tr>
                                                        <td style="padding:5px 0;color:#6c757d;">Description:</td>
                                                        <td>{{ $txn->description ?? '—' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px 0;color:#6c757d;">Transaction ID:</td>
                                                        <td><code>{{ $txn->id }}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:5px 0;color:#6c757d;">Created At:</td>
                                                        <td>{{ $txn->created_at->format('M d, Y H:i:s') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $transactions->links() }}</div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="feather-activity mb-2 d-block" style="font-size:32px;"></i>
                    No transactions yet.
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ── Verification popup overlay ─────────────────────────────────────────── --}}
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
            <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                Refreshing your balance…
            </p>
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

@push('styles')
<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
@keyframes popIn {
    from { transform: scale(.85); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
</style>
@endpush
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
.transaction-row:hover { background-color: #f8f9fa; }
.feather-chevron-down { transition: transform 0.3s ease; }
.transaction-details td { border-top: none !important; }
</style>
@endpush

@push('scripts')
@push('scripts')
<script>
// ─── state ────────────────────────────────────────────────────────────────────
var _gateway    = 'korapay';
var _topupType  = 'wallet';

// ─── top-up type tabs ─────────────────────────────────────────────────────────
function selectTopupType(type) {
    _topupType = type;
    document.getElementById('tab-wallet').className =
        'btn btn-sm flex-fill ' + (type === 'wallet' ? 'btn-primary' : 'btn-outline-primary');
    document.getElementById('tab-ads').className =
        'btn btn-sm flex-fill ' + (type === 'ads' ? 'btn-primary' : 'btn-outline-primary');
    hideMsg();
}

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
    document.getElementById('verify-overlay').style.display = 'flex';
    document.getElementById('verify-state-loading').style.display = 'block';
    document.getElementById('verify-state-success').style.display = 'none';
    document.getElementById('verify-state-error').style.display   = 'none';
    document.getElementById('verify-close-btn').style.display      = 'none';
}

function showVerifySuccess(message) {
    document.getElementById('verify-state-loading').style.display = 'none';
    document.getElementById('verify-state-success').style.display = 'block';
    document.getElementById('verify-state-error').style.display   = 'none';
    document.getElementById('verify-success-msg').textContent      = message;
    document.getElementById('verify-close-btn').style.display      = 'none';
    // Auto-reload after 2.5s
    setTimeout(function() { window.location.reload(); }, 2500);
}

function showVerifyError(message) {
    document.getElementById('verify-state-loading').style.display = 'none';
    document.getElementById('verify-state-success').style.display = 'none';
    document.getElementById('verify-state-error').style.display   = 'block';
    document.getElementById('verify-error-msg').textContent        = message;
    document.getElementById('verify-close-btn').style.display      = 'inline-block';
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
        if (_topupType === 'ads') {
            document.getElementById('korapay-ads-amount').value = amount;
            document.getElementById('korapay-ads-form').submit();
        } else {
            document.getElementById('korapay-wallet-amount').value = amount;
            document.getElementById('korapay-wallet-form').submit();
        }
        return;
    }

    if (typeof window.MonnifySDK === 'undefined') {
        showMsg('Payment SDK is still loading — please wait a moment and try again.', 'warning');
        return;
    }

    setBusy('Initializing…');

    fetch('{{ route('seller.wallet.monnify.init') }}', {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept':       'application/json',
        },
        body: JSON.stringify({
            amount: amount,
            type:   _topupType === 'ads' ? 'ads_topup' : 'wallet_topup'
        }),
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
        console.log('[Monnify init] response:', data);

        if (!data.paymentReference) {
            showMsg('Server did not return a payment reference.', 'danger');
            return;
        }

        var description = _topupType === 'ads' ? 'Ads Balance Top-up' : 'Wallet Top-up';

        window.MonnifySDK.initialize({
            amount:             data.amount,
            currency:           'NGN',
            reference:          data.paymentReference,
            customerFullName:   data.customerName,
            customerEmail:      data.email,
            apiKey:             data.apiKey,
            contractCode:       data.contractCode,
            paymentDescription: description,
            isTestMode:         {{ app()->environment('production') ? 'false' : 'true' }},

            onLoadStart:    function() { console.log('[Monnify] loading...'); },
            onLoadComplete: function() { console.log('[Monnify] ready'); },

            onComplete: function(response) {
                console.log('[Monnify] onComplete:', JSON.stringify(response));
                var ref = (response && response.paymentReference)
                    ? response.paymentReference
                    : data.paymentReference;
                verifyMonnifyPayment(ref);
            },

            onClose: function(closeData) {
                console.log('[Monnify] onClose:', JSON.stringify(closeData));
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
    showVerifyPopup();   // ← open popup immediately
    console.log('[Monnify verify] ref:', reference);

    fetch('{{ route('seller.wallet.monnify.verify') }}', {
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

// ─── expandable transaction rows ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.transaction-row').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') return;
            var id         = this.getAttribute('data-id');
            var detailsRow = document.getElementById('details-' + id);
            var chevron    = this.querySelector('.feather-chevron-down');
            var isOpen     = detailsRow.style.display === 'table-row';

            document.querySelectorAll('.transaction-details').forEach(function(d) {
                d.style.display = 'none';
            });
            document.querySelectorAll('.feather-chevron-down').forEach(function(i) {
                i.style.transform = 'rotate(0deg)';
            });

            if (!isOpen) {
                detailsRow.style.display = 'table-row';
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }
        });
    });
});
</script>
@endpush
@endpush