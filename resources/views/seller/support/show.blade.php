@extends('layouts.seller')
@section('title', 'Ticket #{{ $ticket->ticket_number }}')
@section('page_title', 'Ticket #{{ $ticket->ticket_number }}')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.support') }}">Support</a></li>
    <li class="breadcrumb-item active">#{{ $ticket->ticket_number }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">

        {{-- Messages --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ $ticket->subject }}</h5>
                <span class="badge orderer-badge badge-{{ $ticket->status }}">
                    {{ ucfirst($ticket->status) }}
                </span>
            </div>
            <div class="card-body" style="max-height:500px;overflow-y:auto;" id="messageContainer">
                @foreach($ticket->messages->where('is_internal', false) as $msg)
                @php $isSeller = $msg->sender_type === 'App\Models\Seller'; @endphp
                <div class="d-flex gap-3 mb-4 {{ $isSeller ? 'flex-row-reverse' : '' }}">
                    <div class="avatar-text avatar-sm rounded flex-shrink-0
                         {{ $isSeller ? 'bg-primary text-white' : 'bg-secondary text-white' }}">
                        {{ $isSeller ? 'Me' : 'CS' }}
                    </div>
                    <div style="max-width:75%;">
                        <div class="p-3 rounded {{ $isSeller ? 'bg-primary text-white' : 'bg-light' }}"
                             style="{{ $isSeller ? 'border-radius: 12px 12px 4px 12px !important;' : 'border-radius: 12px 12px 12px 4px !important;' }}">
                            <p class="mb-0 fs-14">{{ $msg->message }}</p>
                        </div>
                        <small class="text-muted d-block mt-1 {{ $isSeller ? 'text-end' : '' }}">
                            {{ $msg->created_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>

            @if(!in_array($ticket->status, ['resolved', 'closed']))
            <div class="card-footer">
                <form action="{{ route('seller.support.reply', $ticket->id) }}" method="POST">
                    @csrf
                    <div class="d-flex gap-2">
                        <textarea name="message"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Type your reply..."
                                  required></textarea>
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
                    This ticket is {{ $ticket->status }}.
                    <a href="{{ route('seller.support.create') }}" class="text-primary ms-2">
                        Open a new ticket
                    </a>
                </div>
            </div>
            @endif
        </div>

    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ticket Info</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Ticket #</small>
                    <strong>{{ $ticket->ticket_number }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge orderer-badge badge-{{ $ticket->status }}">
                        {{ ucfirst($ticket->status) }}
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
                <div class="mb-3">
                    <small class="text-muted d-block">Opened</small>
                    <strong>{{ $ticket->created_at->format('M d, Y H:i') }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const c = document.getElementById('messageContainer');
    if (c) c.scrollTop = c.scrollHeight;
</script>
@endpush
@endsection