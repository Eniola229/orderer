@extends('layouts.admin')
@section('title', 'Support Tickets')
@section('page_title', 'Support Tickets')
@section('breadcrumb')
    <li class="breadcrumb-item active">Support</li>
@endsection

@section('content')

<div class="row mb-4">
    <div class="col-md-4 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Open</p>
                <h2 class="fw-bold mb-0 text-danger">{{ $stats['open'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Waiting</p>
                <h2 class="fw-bold mb-0 text-warning">{{ $stats['waiting'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">In Progress</p>
                <h2 class="fw-bold mb-0 text-warning">{{ $stats['in_progress'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="card">
            <div class="card-body py-3">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Resolved</p>
                <h2 class="fw-bold mb-0 text-success">{{ $stats['resolved'] }}</h2>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.support.index') }}" method="GET"
      class="d-flex gap-2 mb-4 flex-wrap align-items-center">
    <div class="btn-group">
        @foreach(['all'=>'All','open'=>'Open','waiting'=>'Waiting','in_progress'=>'In Progress','resolved'=>'Resolved','closed'=>'Closed'] as $val=>$label)
        <a href="{{ route('admin.support.index', ['status'=>$val]) }}"
           class="btn btn-sm {{ request('status','all')===$val ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
    <div class="d-flex gap-2 ms-auto">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Subject or ticket #..." value="{{ request('search') }}"
               style="width:240px;">
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="feather-search"></i>
        </button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        @forelse($tickets as $ticket)
        <a href="{{ route('admin.support.show', $ticket->id) }}"
           class="d-flex align-items-center gap-3 p-3 border-bottom text-decoration-none text-dark">
            <div class="avatar-text avatar-md rounded flex-shrink-0
                 {{ in_array($ticket->status,['open','waiting']) ? 'bg-danger text-white' :
                    ($ticket->status==='in_progress' ? 'bg-warning text-white' :
                    'bg-success text-white') }}">
                <i class="feather-life-buoy" style="font-size:16px;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="mb-0 fw-semibold fs-14">{{ $ticket->subject }}</p>
                    <span class="badge orderer-badge badge-{{ $ticket->status }}">
                        {{ ucfirst(str_replace('_',' ',$ticket->status)) }}
                    </span>
                </div>
                <div class="d-flex gap-3 mt-1 flex-wrap">
                    <small class="text-muted">#{{ $ticket->ticket_number }}</small>
                    <small class="text-muted">{{ ucfirst($ticket->category) }}</small>
                    <small class="text-muted">
                        <span class="badge bg-light text-dark" style="font-size:10px;">
                            {{ class_basename($ticket->requester_type) }}
                        </span>
                    </small>
                    <small class="text-muted">
                        Priority:
                        <span class="{{ $ticket->priority==='urgent'?'text-danger fw-bold':
                            ($ticket->priority==='high'?'text-warning fw-bold':'') }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </small>
                    <small class="text-muted">{{ $ticket->messages->count() }} message(s)</small>
                    <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="feather-life-buoy mb-2 d-block" style="font-size:40px;"></i>
            <p>No tickets found.</p>
        </div>
        @endforelse
    </div>
    @if($tickets->hasPages())
    <div class="card-footer">{{ $tickets->links() }}</div>
    @endif
</div>

@endsection