@extends('layouts.admin')
@section('title', $newsletter->subject)
@section('page_title', 'Newsletter Detail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.newsletter.index') }}">Newsletter</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    {{-- Main detail --}}
    <div class="col-lg-8">

        {{-- Email Body --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="feather-mail me-1 text-muted"></i> {{ $newsletter->subject }}
                </h5>
                @php
                    $badgeMap = [
                        'draft'   => 'secondary',
                        'queued'  => 'warning',
                        'sending' => 'info',
                        'sent'    => 'success',
                        'failed'  => 'danger',
                    ];
                    $color = $badgeMap[$newsletter->status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $color }} fs-12">{{ ucfirst($newsletter->status) }}</span>
            </div>
            <div class="card-body">
                <div class="border rounded p-3 bg-light" style="min-height:200px;">
                    {!! $newsletter->body !!}
                </div>
            </div>
        </div>

        {{-- SMS Card (only shown if SMS was enabled) --}}
        @if($newsletter->send_sms)
        <div class="card mb-4 border-start border-4 border-primary">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">
                    <i class="feather-message-square me-1 text-primary"></i> SMS Message
                </h6>
                
            </div>
            <div class="card-body">

                {{-- SMS Audience --}}
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold fs-11">SMS Audience</small>
                    <div class="mt-1">
                        <span class="badge bg-secondary fs-12">
                            @php
                                $smsAudienceLabel = match($newsletter->sms_audience) {
                                    'users'   => 'Users (Buyers)',
                                    'sellers' => 'Sellers',
                                    'both'    => 'Both Users & Sellers',
                                    default   => ucfirst($newsletter->sms_audience ?? '—'),
                                };
                            @endphp
                            {{ $smsAudienceLabel }}
                        </span>
                    </div>
                </div>

                {{-- SMS Message --}}
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold fs-11">Message Text</small>
                    <div class="border rounded p-3 bg-light mt-1" style="white-space: pre-wrap; font-family: monospace; font-size: 14px;">{{ $newsletter->sms_message }}</div>
                    <small class="text-muted">
                        {{ strlen($newsletter->sms_message) }} characters
                        — approx. {{ ceil(strlen($newsletter->sms_message) / 160) }} SMS credit(s) per recipient
                    </small>
                </div>

                {{-- Extra Numbers --}}
                @if(!empty($newsletter->sms_extra_numbers))
                <div>
                    <small class="text-muted text-uppercase fw-semibold fs-11">Extra Numbers</small>
                    <div class="mt-1 d-flex flex-wrap gap-2">
                        @foreach($newsletter->sms_extra_numbers as $num)
                            <span class="badge bg-light text-dark border fs-12">
                                <i class="feather-phone me-1"></i>{{ $num }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endif

    </div>

    {{-- Sidebar meta + actions --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Details</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">

                    {{-- Email audience --}}
                    <dt class="col-5 text-muted fs-12">Email Audience</dt>
                    <dd class="col-7">
                        <span class="badge bg-secondary">{{ $newsletter->audience_label }}</span>
                    </dd>

                    {{-- SMS badge in sidebar --}}
                    <dt class="col-5 text-muted fs-12">SMS</dt>
                    <dd class="col-7">
                        @if($newsletter->send_sms)
                            <span class="badge bg-primary">Enabled</span>
                        @else
                            <span class="badge bg-light text-muted border">Not sent</span>
                        @endif
                    </dd>

                    <dt class="col-5 text-muted fs-12">Created by</dt>
                    <dd class="col-7 fs-13">{{ $newsletter->creator->full_name ?? '—' }}</dd>

                    <dt class="col-5 text-muted fs-12">Created</dt>
                    <dd class="col-7 fs-13">{{ $newsletter->created_at->format('M d, Y H:i') }}</dd>

                    @if($newsletter->sent_at)
                    <dt class="col-5 text-muted fs-12">Sent at</dt>
                    <dd class="col-7 fs-13">{{ $newsletter->sent_at->format('M d, Y H:i') }}</dd>
                    @endif

                    @if($newsletter->total_recipients)
                    <dt class="col-5 text-muted fs-12">Email Recipients</dt>
                    <dd class="col-7 fs-13">{{ number_format($newsletter->total_recipients) }}</dd>

                    <dt class="col-5 text-muted fs-12">Delivered</dt>
                    <dd class="col-7 fs-13 text-success fw-semibold">
                        {{ number_format($newsletter->sent_count) }}
                    </dd>

                    <dt class="col-5 text-muted fs-12">Failed</dt>
                    <dd class="col-7 fs-13 text-danger fw-semibold">
                        {{ number_format($newsletter->failed_count) }}
                    </dd>

                    <dt class="col-5 text-muted fs-12">Progress</dt>
                    <dd class="col-7">
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-success"
                                 style="width:{{ $newsletter->progress_percent }}%"></div>
                        </div>
                        <small class="text-muted">{{ $newsletter->progress_percent }}%</small>
                    </dd>
                    @endif

                </dl>
            </div>
        </div>

        {{-- Actions (draft only) --}}
        @if($newsletter->isDraft())
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Actions</h6>
            </div>
            <div class="card-body d-grid gap-2">

                {{-- Send --}}
                <form action="{{ route('admin.newsletter.send', $newsletter) }}" method="POST"
                      onsubmit="return confirm('Send this newsletter now? This cannot be undone.')">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="feather-send me-1"></i> Send Now
                        @if($newsletter->send_sms)
                            <span class="badge bg-white text-success ms-1 fs-11">+ SMS</span>
                        @endif
                    </button>
                </form>

                {{-- Edit --}}
                <a href="{{ route('admin.newsletter.edit', $newsletter) }}"
                   class="btn btn-outline-primary">
                    <i class="feather-edit-2 me-1"></i> Edit Draft
                </a>

                {{-- Delete --}}
                <form action="{{ route('admin.newsletter.destroy', $newsletter) }}" method="POST"
                      onsubmit="return confirm('Delete this draft? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="feather-trash-2 me-1"></i> Delete Draft
                    </button>
                </form>

            </div>
        </div>
        @endif

    </div>
</div>
@endsection