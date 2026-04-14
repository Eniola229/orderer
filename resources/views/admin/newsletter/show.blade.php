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
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ $newsletter->subject }}</h5>
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
    </div>

    {{-- Sidebar meta + actions --}}
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Details</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fs-12">Audience</dt>
                    <dd class="col-7">
                        <span class="badge bg-secondary">{{ $newsletter->audience_label }}</span>
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
                    <dt class="col-5 text-muted fs-12">Recipients</dt>
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

        {{-- Actions --}}
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