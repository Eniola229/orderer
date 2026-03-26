@extends('layouts.buyer')
@section('title', 'Ticket #' . $ticket->ticket_number)
@section('page_title', 'Ticket #' . $ticket->ticket_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('buyer.support') }}">Support</a></li>
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
            <div class="card-body" style="max-height:500px;overflow-y:auto;" id="msgBox">
                @foreach($ticket->messages->where('is_internal', false) as $msg)
                @php $isBuyer = $msg->sender_type === 'App\Models\User'; @endphp
                <div class="d-flex gap-3 mb-4 {{ $isBuyer ? 'flex-row-reverse' : '' }}">
                    <div class="avatar-text avatar-sm rounded flex-shrink-0
                         {{ $isBuyer ? 'bg-primary text-white' : 'bg-secondary text-white' }}"
                         style="font-size:11px;">
                        {{ $isBuyer ? 'Me' : 'CS' }}
                    </div>
                    <div style="max-width:75%;">
                        <div class="p-3 rounded
                             {{ $isBuyer ? 'bg-primary text-white' : 'bg-light' }}"
                             style="{{ $isBuyer ? 'border-radius:12px 12px 4px 12px !important' : 'border-radius:12px 12px 12px 4px !important' }}">
                            <p class="mb-0 fs-14">{{ $msg->message }}</p>
                        </div>
                        <small class="text-muted d-block mt-1 {{ $isBuyer ? 'text-end' : '' }}">
                            {{ $msg->created_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>
            @if(!in_array($ticket->status, ['resolved','closed']))
            <div class="card-footer">
                <form action="{{ route('buyer.support.reply', $ticket->id) }}" method="POST">
                    @csrf
                    <div class="d-flex gap-2">
                        <textarea name="message" class="form-control" rows="2"
                                  placeholder="Type your reply..." required></textarea>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="feather-send"></i>
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="card-footer">
                <div class="alert alert-secondary mb-0">
                    <i class="feather-lock me-2"></i>
                    Ticket is {{ $ticket->status }}.
                    <a href="{{ route('buyer.support.create') }}" class="text-primary ms-2">Open a new ticket</a>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Ticket Info</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Ticket #</small>
                    <strong>{{ $ticket->ticket_number }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge orderer-badge badge-{{ $ticket->status }}">
                        {{ ucfirst(str_replace('_',' ',$ticket->status)) }}
                    </span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Category</small>
                    <strong>{{ ucfirst(str_replace('_',' ',$ticket->category)) }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Priority</small>
                    <span class="badge bg-{{ $ticket->priority === 'urgent' ? 'danger' : ($ticket->priority === 'high' ? 'warning' : 'secondary') }} text-white">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </div>
                <div>
                    <small class="text-muted d-block">Opened</small>
                    <strong>{{ $ticket->created_at->format('M d, Y') }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    const b = document.getElementById('msgBox');
    if (b) b.scrollTop = b.scrollHeight;
</script>
@endpush
@endsection
