@extends('layouts.buyer')
@section('title', 'Referrals')
@section('page_title', 'Referral Program')
@section('breadcrumb')
    <li class="breadcrumb-item active">Referral</li>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-7">

        {{-- Referral card --}}
        <div class="card mb-4" style="background:linear-gradient(135deg,#27AE60,#2ECC71);color:#fff;">
            <div class="card-body p-4">
                <h4 style="color:#fff;font-weight:800;margin-bottom:8px;">Refer &amp; Earn</h4>
                <p style="color:rgba(255,255,255,.85);margin-bottom:20px;">
                    Share your referral link. When a friend signs up and places their first order,
                    you both earn a reward.
                </p>
                <div class="d-flex gap-2">
                    <input type="text"
                           class="form-control"
                           value="{{ url('/register?ref=' . auth('web')->user()->referral_code) }}"
                           id="refLink"
                           readonly
                           style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.4);color:#fff;">
                    <button type="button"
                            class="btn"
                            style="background:#fff;color:#27AE60;font-weight:700;white-space:nowrap;"
                            onclick="copyLink()">
                        <i class="feather-copy me-1"></i> Copy
                    </button>
                </div>
                <p style="color:rgba(255,255,255,.7);font-size:12px;margin-top:8px;">
                    Your code: <strong style="color:#fff;">{{ auth('web')->user()->referral_code }}</strong>
                </p>
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
                        <h3 class="fw-bold text-success mb-0">₦{{ number_format($stats['total_earned'], 2) }}</h3>
                        <small class="text-muted">Total Earned</small>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <h3 class="fw-bold text-warning mb-0">₦{{ number_format($stats['pending_earnings'], 2) }}</h3>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Referral history --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Referral History</h5>
            </div>
            <div class="card-body p-0">
                @if($referrals->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Friend</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Joined</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Earnings</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $ref)
                            <tr>
                                <td class="fw-semibold fs-13">
                                    {{ $ref->referred->first_name ?? 'User' }}
                                </td>
                                <td class="text-muted fs-12">
                                    {{ $ref->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    @foreach($ref->earnings as $earning)
                                    <span class="fw-bold text-success">
                                        ₦{{ number_format($earning->amount, 2) }}
                                    </span>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($ref->earnings as $earning)
                                    <span class="badge orderer-badge badge-{{ $earning->status === 'credited' ? 'approved' : 'pending' }}">
                                        {{ ucfirst($earning->status) }}
                                    </span>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="feather-users mb-2 d-block" style="font-size:32px;"></i>
                    No referrals yet. Share your link to start earning!
                </div>
                @endif
            </div>
        </div>

    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">How It Works</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-3 mb-4">
                    <div class="avatar-text avatar-md rounded bg-primary text-white flex-shrink-0">1</div>
                    <div>
                        <p class="mb-0 fw-semibold">Copy your referral link</p>
                        <small class="text-muted">Share it with friends via WhatsApp, social media or email</small>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-4">
                    <div class="avatar-text avatar-md rounded bg-primary text-white flex-shrink-0">2</div>
                    <div>
                        <p class="mb-0 fw-semibold">Friend signs up</p>
                        <small class="text-muted">They create an account using your link</small>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-4">
                    <div class="avatar-text avatar-md rounded bg-primary text-white flex-shrink-0">3</div>
                    <div>
                        <p class="mb-0 fw-semibold">Friend places first order</p>
                        <small class="text-muted">They complete their first purchase on Orderer</small>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="avatar-text avatar-md rounded" style="background:#D5F5E3;color:#2ECC71;flex-shrink:0;">
                        <i class="feather-dollar-sign"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold">You both earn!</p>
                        <small class="text-muted">Earnings are credited to your wallet automatically</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Share buttons --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Share Via</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="https://wa.me/?text=Join+Orderer+and+shop+amazing+products!+Use+my+referral+link:+{{ urlencode(url('/register?ref=' . auth('web')->user()->referral_code)) }}"
                       target="_blank"
                       class="btn btn-outline-success">
                        <i class="feather-message-circle me-2"></i> Share on WhatsApp
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=Shop+on+Orderer+using+my+link+and+get+a+reward!+{{ urlencode(url('/register?ref=' . auth('web')->user()->referral_code)) }}"
                       target="_blank"
                       class="btn btn-outline-info">
                        <i class="feather-twitter me-2"></i> Share on Twitter
                    </a>
                    <button type="button" onclick="copyLink()" class="btn btn-outline-primary">
                        <i class="feather-copy me-2"></i> Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function copyLink() {
    const input = document.getElementById('refLink');
    input.select();
    document.execCommand('copy');
    alert('Referral link copied!');
}
</script>
@endpush

@endsection
