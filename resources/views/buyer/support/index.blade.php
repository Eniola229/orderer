@extends('layouts.buyer')
@section('title', 'Support')
@section('page_title', 'Support Tickets')
@section('breadcrumb')
    <li class="breadcrumb-item active">Support</li>
@endsection
@section('page_actions')
    <a href="{{ route('buyer.support.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> New Ticket
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @forelse($tickets as $ticket)
        <a href="{{ route('buyer.support.show', $ticket->id) }}"
           class="d-flex align-items-center gap-3 p-3 border-bottom text-decoration-none text-dark">
            <div class="avatar-text avatar-md rounded
                {{ $ticket->status === 'open' || $ticket->status === 'in_progress' ? 'bg-primary text-white' :
                   ($ticket->status === 'resolved' ? 'bg-success text-white' : 'bg-secondary text-white') }}">
                <i class="feather-life-buoy"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="mb-0 fw-semibold fs-14">{{ $ticket->subject }}</p>
                    <span class="badge orderer-badge badge-{{ $ticket->status }}">
                        {{ ucfirst(str_replace('_',' ',$ticket->status)) }}
                    </span>
                </div>
                <div class="d-flex gap-3 mt-1">
                    <small class="text-muted">#{{ $ticket->ticket_number }}</small>
                    <small class="text-muted">{{ ucfirst($ticket->category) }}</small>
                    <small class="text-muted">{{ $ticket->messages->count() }} message(s)</small>
                    <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="feather-life-buoy mb-2 d-block" style="font-size:40px;"></i>
            No tickets yet.
            <a href="{{ route('buyer.support.create') }}" class="text-primary d-block mt-2">
                Open your first ticket
            </a>
        </div>
        @endforelse
    </div>
    @if($tickets->hasPages())
    <div class="card-footer">{{ $tickets->links() }}</div>
    @endif
</div>
@endsection
