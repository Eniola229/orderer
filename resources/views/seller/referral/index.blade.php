@extends('layouts.seller')
@section('title', 'Referrals')
@section('page_title', 'Referral Program')
@section('breadcrumb')
    <li class="breadcrumb-item active">Referrals</li>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-7">

        {{-- Hero card --}}
        <div class="card mb-4"
             style="background:linear-gradient(135deg,#27AE60,#2ECC71);color:#fff;">
            <div class="card-body p-4">
                <h4 style="color:#fff;font-weight:800;margin-bottom:8px;">
                    <i class="feather-gift me-2"></i>Refer &amp; Earn
                </h4>
                <p style="color:rgba(255,255,255,.85);margin-bottom:20px;">
                    Invite other sellers to join Orderer using your unique link.
                    When they sign up and start selling, you both benefit.
                </p>

                {{-- Referral code --}}
                <label style="color:rgba(255,255,255,.7);font-size:12px;margin-bottom:4px;display:block;">
                    YOUR REFERRAL CODE
                </label>
                <div class="d-flex gap-2 mb-3">
                    <input type="text"
                           class="form-control fw-bold"
                           id="sellerRefCode"
                           value="{{ $seller->referral_code }}"
                           readonly
                           style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.4);
                                  color:#fff;letter-spacing:2px;max-width:200px;">
                    <button type="button"
                            class="btn btn-sm"
                            style="background:#fff;color:#27AE60;font-weight:700;white-space:nowrap;"
                            onclick="sellerCopy('sellerRefCode', this, 'code')">
                        <i class="feather-copy me-1"></i> Copy Code
                    </button>
                </div>

                {{-- Full link --}}
                <label style="color:rgba(255,255,255,.7);font-size:12px;margin-bottom:4px;display:block;">
                    YOUR REFERRAL LINK
                </label>
                <div class="d-flex gap-2">
                    <input type="text"
                           class="form-control"
                           id="sellerRefLink"
                           value="https://ordererweb.com/seller/register?ref={{ $seller->referral_code }}"
                           readonly
                           style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.4);color:#fff;">
                    <button type="button"
                            class="btn btn-sm"
                            style="background:#fff;color:#27AE60;font-weight:700;white-space:nowrap;"
                            onclick="sellerCopy('sellerRefLink', this, 'link')">
                        <i class="feather-copy me-1"></i> Copy Link
                    </button>
                </div>

                <div id="sellerCopyFeedback"
                     style="color:rgba(255,255,255,.9);font-size:12px;margin-top:8px;display:none;">
                    <i class="feather-check-circle me-1"></i>
                    <span id="sellerCopyMsg">Copied!</span>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total_referrals'] }}</h3>
                        <small class="text-muted">Total Referrals</small>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <h3 class="fw-bold text-success mb-0">
                            ₦{{ number_format($stats['total_earned'], 2) }}
                        </h3>
                        <small class="text-muted">Total Earned</small>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <h3 class="fw-bold text-warning mb-0">
                            ₦{{ number_format($stats['pending_earnings'], 2) }}
                        </h3>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Referral history --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Sellers You've Referred</h5>
            </div>
            <div class="card-body p-0">
                @if($referrals->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                            <th class="fs-11 text-uppercase text-muted fw-semibold">Business</th>
                            <th class="fs-11 text-uppercase text-muted fw-semibold">Joined</th>
                            <th class="fs-11 text-uppercase text-muted fw-semibold">Earnings</th>
                            <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        </tr>
                    </thead>
                        <tbody>
                            @foreach($referrals as $ref)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px;height:32px;border-radius:50%;background:#2ECC71;
                                                    display:flex;align-items:center;justify-content:center;
                                                    color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">
                                            {{ strtoupper(substr($ref->referred->first_name ?? 'S', 0, 1)) }}
                                        </div>
                                        <span class="fw-semibold fs-13">
                                            {{ $ref->referred->full_name ?? 'Seller' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-muted fs-13">
                                    {{ $ref->referred->business_name ?? '—' }}
                                </td>
                                <td class="text-muted fs-12">
                                    {{ $ref->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    @foreach($ref->earnings as $earning)
                                        <span class="fw-bold text-success d-block">
                                            ₦{{ number_format($earning->amount, 2) }}
                                        </span>
                                    @endforeach
                                    @if($ref->earnings->isEmpty()) <span class="text-muted">—</span> @endif
                                </td>
                                <td>
                                    @foreach($ref->earnings as $earning)
                                        <span class="badge orderer-badge badge-{{ $earning->status === 'credited' ? 'approved' : 'pending' }}">
                                            {{ ucfirst($earning->status) }}
                                        </span>
                                    @endforeach
                                    @if($ref->earnings->isEmpty())
                                        <span class="badge orderer-badge badge-pending">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="feather-users d-block mb-2" style="font-size:36px;"></i>
                    <p class="mb-0">No referrals yet. Share your link to start!</p>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Right panel --}}
    <div class="col-lg-5">

        {{-- How it works --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">How It Works</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-3 mb-4">
                    <div class="avatar-text avatar-md rounded bg-primary text-white flex-shrink-0">1</div>
                    <div>
                        <p class="mb-0 fw-semibold">Share your referral link</p>
                        <small class="text-muted">Send it to friends who want to sell on Orderer</small>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-4">
                    <div class="avatar-text avatar-md rounded bg-primary text-white flex-shrink-0">2</div>
                    <div>
                        <p class="mb-0 fw-semibold">They register as a seller</p>
                        <small class="text-muted">They sign up using your unique referral link</small>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-4">
                    <div class="avatar-text avatar-md rounded bg-primary text-white flex-shrink-0">3</div>
                    <div>
                        <p class="mb-0 fw-semibold">They get approved &amp; start selling</p>
                        <small class="text-muted">Their account is verified and their first sale goes through</small>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="avatar-text avatar-md rounded flex-shrink-0"
                         style="background:#D5F5E3;color:#2ECC71;">
                        <i class="feather-dollar-sign"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold">You both earn!</p>
                        <small class="text-muted">Rewards are credited to your wallet automatically</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Share buttons --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Share Via</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="https://wa.me/?text=Join+me+on+Orderer+and+start+selling!+Sign+up+with+my+link:+{{ urlencode('https://ordererweb.com/seller/register?ref=' . $seller->referral_code) }}"
                       target="_blank"
                       class="btn btn-outline-success">
                        <i class="feather-message-circle me-2"></i> Share on WhatsApp
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=Start+selling+on+Orderer+using+my+referral+link!+{{ urlencode('https://ordererweb.com/seller/register?ref=' . $seller->referral_code) }}"
                       target="_blank"
                       class="btn btn-outline-info">
                        <i class="feather-twitter me-2"></i> Share on Twitter
                    </a>
                    <button type="button"
                            onclick="sellerCopy('sellerRefLink', this, 'link')"
                            class="btn btn-outline-primary">
                        <i class="feather-copy me-2"></i> Copy Link
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function sellerCopy(inputId, btn, type) {
    const val = document.getElementById(inputId).value;

    navigator.clipboard.writeText(val).then(() => {
        showCopySuccess(btn, type);
    }).catch(() => {
        // Fallback
        const el = document.getElementById(inputId);
        el.select();
        document.execCommand('copy');
        showCopySuccess(btn, type);
    });
}

function showCopySuccess(btn, type) {
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i class="feather-check me-1"></i> Copied!';
    btn.style.background = '#fff';
    btn.style.color       = '#27AE60';

    const feedback = document.getElementById('sellerCopyFeedback');
    const msg      = document.getElementById('sellerCopyMsg');
    if (feedback && msg) {
        msg.textContent     = type === 'code' ? 'Code copied to clipboard!' : 'Link copied to clipboard!';
        feedback.style.display = 'block';
    }

    setTimeout(() => {
        btn.innerHTML = origHtml;
        if (feedback) feedback.style.display = 'none';
    }, 2000);
}
</script>
@endpush

@endsection