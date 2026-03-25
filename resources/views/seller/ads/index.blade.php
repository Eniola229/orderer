@extends('layouts.seller')
@section('title', 'Promotions')
@section('page_title', 'My Promotions')
@section('breadcrumb')
    <li class="breadcrumb-item active">Promotions</li>
@endsection
@section('page_actions')
    <a href="{{ route('seller.ads.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> Create Ad
    </a>
@endsection

@section('content')

<div class="card">
    <div class="card-body p-0">
        @if($ads->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Ad</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Slot</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Budget</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Spent</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Stats</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Period</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ads as $ad)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($ad->media_url && $ad->media_type === 'image')
                                    <img src="{{ $ad->media_url }}"
                                         style="width:46px;height:46px;object-fit:cover;border-radius:8px;"
                                         alt="">
                                @elseif($ad->media_url && $ad->media_type === 'video')
                                    <div style="width:46px;height:46px;background:#1a1a2e;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="feather-play text-white" style="font-size:18px;"></i>
                                    </div>
                                @else
                                    <div style="width:46px;height:46px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="feather-trending-up text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold fs-13">{{ Str::limit($ad->title, 30) }}</p>
                                    <small class="text-muted">{{ $ad->media_type === 'video' ? 'Video Ad' : 'Image Ad' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark fs-11">
                                {{ $ad->adCategory->name ?? '—' }}
                            </span>
                        </td>
                        <td class="fs-13 text-muted">
                            {{ $ad->bannerSlot->name ?? 'Top Listing / CPC' }}
                        </td>
                        <td class="fw-bold">${{ number_format($ad->budget, 2) }}</td>
                        <td class="text-danger fw-semibold">
                            ${{ number_format($ad->amount_spent, 2) }}
                        </td>
                        <td>
                            <div class="fs-12">
                                <span class="text-muted">
                                    <i class="feather-eye me-1"></i>{{ number_format($ad->total_impressions) }}
                                </span>
                                <span class="text-muted ms-2">
                                    <i class="feather-mouse-pointer me-1"></i>{{ number_format($ad->total_clicks) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge orderer-badge badge-{{ $ad->status }}">
                                {{ ucfirst($ad->status) }}
                            </span>
                            @if($ad->status === 'rejected' && $ad->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">
                                    {{ Str::limit($ad->rejection_reason, 35) }}
                                </p>
                            @endif
                        </td>
                        <td class="fs-12 text-muted">
                            {{ $ad->start_date?->format('M d') }} –
                            {{ $ad->end_date?->format('M d, Y') }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($ad->status === 'active')
                                <form action="{{ route('seller.ads.pause', $ad->id) }}"
                                      method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Pause">
                                        <i class="feather-pause"></i>
                                    </button>
                                </form>
                                @elseif($ad->status === 'paused')
                                <form action="{{ route('seller.ads.resume', $ad->id) }}"
                                      method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-success"
                                            title="Resume">
                                        <i class="feather-play"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('seller.ads.destroy', $ad->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this ad?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $ads->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-trending-up mb-3 d-block" style="font-size:40px;"></i>
            <p class="mb-3">No ads created yet.</p>
        </div>
        @endif
    </div>
</div>

@endsection