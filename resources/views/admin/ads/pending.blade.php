@extends('layouts.admin')
@section('title', 'Pending Ads')
@section('page_title', 'Pending Ad Approvals')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.ads.index') }}">Ads</a></li>
    <li class="breadcrumb-item active">Pending</li>
@endsection

@section('content')

@if($ads->count())
<div class="row">
    @foreach($ads as $ad)
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold">{{ $ad->title }}</h6>
                    <small class="text-muted">
                        by <strong>{{ $ad->seller->business_name }}</strong>
                    </small>
                </div>
                <span class="badge orderer-badge badge-pending">Pending</span>
            </div>

            @if($ad->media_url)
            <div style="height:180px;overflow:hidden;background:#f5f5f5;">
                @if($ad->media_type === 'video')
                    <video style="width:100%;height:100%;object-fit:cover;" controls>
                        <source src="{{ $ad->media_url }}">
                    </video>
                @else
                    <img src="{{ $ad->media_url }}"
                         style="width:100%;height:100%;object-fit:cover;" alt="">
                @endif
            </div>
            @endif

            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Type</small>
                        <span class="fw-semibold fs-13">{{ $ad->adCategory->name ?? '—' }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Slot</small>
                        <span class="fw-semibold fs-13">{{ $ad->bannerSlot->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Budget</small>
                        <span class="fw-bold text-success">₦{{ number_format($ad->budget, 2) }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Seller Ads Balance</small>
                        <span class="fw-bold {{ $ad->seller->ads_balance >= $ad->budget ? 'text-success' : 'text-danger' }}">
                            ₦{{ number_format($ad->seller->ads_balance, 2) }}
                            @if($ad->seller->ads_balance < $ad->budget)
                                <i class="feather-alert-circle text-danger ms-1"></i>
                            @endif
                        </span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Period</small>
                        <span class="fs-13">
                            {{ $ad->start_date?->format('M d') }} –
                            {{ $ad->end_date?->format('M d, Y') }}
                        </span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Media</small>
                        <span class="fs-13 text-capitalize">{{ $ad->media_type }}</span>
                    </div>
                </div>

                @if($ad->seller->ads_balance < $ad->budget)
                <div class="alert alert-warning mb-3">
                    <i class="feather-alert-triangle me-2"></i>
                    Insufficient ads balance. Cannot approve until seller tops up.
                </div>
                @endif

                <div class="d-flex gap-2">
                    @if($ad->seller->ads_balance >= $ad->budget)
                    <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="feather-check me-1"></i> Approve & Activate
                        </button>
                    </form>
                    @endif

                    <button type="button"
                            class="btn btn-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal{{ $ad->id }}">
                        <i class="feather-x me-1"></i> Reject
                    </button>
                </div>
            </div>
        </div>

        {{-- Reject modal --}}
        <div class="modal fade" id="rejectModal{{ $ad->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Ad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.ads.reject', $ad->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <label class="form-label fw-bold">Reason for rejection</label>
                            <textarea name="rejection_reason"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Explain why this ad is being rejected..."
                                      required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject Ad</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="d-flex justify-content-center">{{ $ads->links() }}</div>

@else
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="feather-check-circle mb-3 d-block" style="font-size:40px;"></i>
        <p>No ads pending approval.</p>
    </div>
</div>
@endif

@endsection