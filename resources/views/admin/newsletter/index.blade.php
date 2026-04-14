@extends('layouts.admin')
@section('title', 'Newsletter')
@section('page_title', 'Newsletter')
@section('breadcrumb')
    <li class="breadcrumb-item active">Newsletter</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('admin.newsletter.create') }}" class="btn btn-primary">
            <i class="feather-plus me-1"></i> Compose Newsletter
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">All Newsletters</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Subject</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Audience</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Recipients</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Sent By</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newsletters as $nl)
                    <tr>
                        <td class="fw-semibold">{{ $nl->subject }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $nl->audience_label }}</span>
                        </td>
                        <td>
                            @php
                                $badgeMap = [
                                    'draft'   => 'secondary',
                                    'queued'  => 'warning',
                                    'sending' => 'info',
                                    'sent'    => 'success',
                                    'failed'  => 'danger',
                                ];
                                $color = $badgeMap[$nl->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ ucfirst($nl->status) }}</span>
                        </td>
                        <td>
                            @if($nl->total_recipients)
                                {{ number_format($nl->sent_count) }} / {{ number_format($nl->total_recipients) }}
                                <div class="progress mt-1" style="height:4px;width:80px;">
                                    <div class="progress-bar bg-success"
                                         style="width:{{ $nl->progress_percent }}%"></div>
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td class="fs-12 text-muted">{{ $nl->creator->full_name ?? '—' }}</td>
                        <td class="fs-12 text-muted">{{ $nl->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.newsletter.show', $nl) }}"
                               class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No newsletters yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($newsletters->hasPages())
    <div class="card-footer">
        {{ $newsletters->links() }}
    </div>
    @endif
</div>
@endsection