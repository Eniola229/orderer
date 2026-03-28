@extends('layouts.admin')
@section('title', 'Ticket #' . $ticket->ticket_number)
@section('page_title', 'Ticket #' . $ticket->ticket_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.support.index') }}">Support</a></li>
    <li class="breadcrumb-item active">#{{ $ticket->ticket_number }}</li>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ $ticket->subject }}</h5>
                <span class="badge orderer-badge badge-{{ $ticket->status }}">
                    {{ ucfirst(str_replace('_',' ',$ticket->status)) }}
                </span>
            </div>
            <div class="card-body" style="max-height:540px;overflow-y:auto;" id="msgBox">
                @foreach($ticket->messages->where('is_internal', false) as $msg)
                @php
                    $isAdmin = $msg->sender_type === 'App\Models\Admin';
                @endphp
                <div class="d-flex gap-3 mb-4 {{ $isAdmin ? 'flex-row-reverse' : '' }}">
                    <div class="avatar-text avatar-sm rounded flex-shrink-0
                         {{ $isAdmin ? 'bg-primary text-white' : 'bg-secondary text-white' }}"
                         style="font-size:11px;width:36px;height:36px;">
                        {{ $isAdmin ? 'CS' : (class_basename($msg->sender_type)[0] ?? 'U') }}
                    </div>
                    <div style="max-width:72%;">
                        <div class="p-3 rounded
                             {{ $isAdmin ? 'bg-primary text-white' : 'bg-light' }}"
                             style="{{ $isAdmin
                                 ? 'border-radius:12px 12px 4px 12px !important'
                                 : 'border-radius:12px 12px 12px 4px !important' }}">
                            <p class="mb-0 fs-14">{{ $msg->message }}</p>
                        </div>
                        <small class="text-muted d-block mt-1 {{ $isAdmin ? 'text-end' : '' }}">
                            {{ $msg->created_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>

            @if(!in_array($ticket->status, ['resolved','closed']) && auth('admin')->user()->canHandleSupport())
            <div class="card-footer">
                <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST">
                    @csrf
                    <div class="d-flex gap-2">
                        <textarea name="message" class="form-control" rows="2"
                                  placeholder="Type your reply to the customer..." required></textarea>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="feather-send"></i>
                        </button>
                    </div>
                </form>
            </div>
            @elseif(!auth('admin')->user()->canHandleSupport())
            <div class="card-footer">
                <div class="alert alert-warning mb-0">
                    <i class="feather-lock me-2"></i>
                    You don't have permission to reply to tickets.
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title mb-0">Ticket Info</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Ticket #</small>
                    <strong>{{ $ticket->ticket_number }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Requester type</small>
                    <span class="badge bg-light text-dark">
                        {{ class_basename($ticket->requester_type) }}
                    </span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Category</small>
                    <strong>{{ ucfirst(str_replace('_',' ',$ticket->category)) }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Priority</small>
                    <span class="badge bg-{{ $ticket->priority==='urgent'?'danger':
                        ($ticket->priority==='high'?'warning':'secondary') }} text-white">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Opened</small>
                    <strong>{{ $ticket->created_at->format('M d, Y H:i') }}</strong>
                </div>
                @if($ticket->resolved_at)
                <div class="mb-3">
                    <small class="text-muted d-block">Resolved</small>
                    <strong>{{ $ticket->resolved_at->format('M d, Y H:i') }}</strong>
                </div>
                @endif
            </div>
        </div>

        @if(auth('admin')->user()->canHandleSupport() && !in_array($ticket->status, ['resolved','closed']))
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Actions</h5></div>
            <div class="card-body d-grid gap-2">
                <form action="{{ route('admin.support.resolve', $ticket->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-success w-100">
                        <i class="feather-check-circle me-2"></i> Mark Resolved
                    </button>
                </form>
                <form action="{{ route('admin.support.close', $ticket->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="feather-x-circle me-2"></i> Close Ticket
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const b = document.getElementById('msgBox');
    if (b) b.scrollTop = b.scrollHeight;
</script>
@endpush

@endsection