@extends('layouts.seller')
@section('title', 'Ticket #' . $ticket->ticket_number)
@section('page_title', 'Ticket #' . $ticket->ticket_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.support') }}">Support</a></li>
    <li class="breadcrumb-item active">#{{ $ticket->ticket_number }}</li>
@endsection

@section('content')

@php
function sellerTicketStatusBadge(string $status): string {
    return match($status) {
        'open'        => 'bg-success text-white',
        'waiting'     => 'bg-warning text-dark',
        'in_progress' => 'bg-primary text-white',
        'resolved'    => 'bg-secondary text-white',
        'closed'      => 'bg-dark text-white',
        default       => 'bg-secondary text-white',
    };
}
@endphp

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ $ticket->subject }}</h5>
                <span class="badge {{ sellerTicketStatusBadge($ticket->status) }}">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </span>
            </div>

            <div class="card-body" style="max-height:500px;overflow-y:auto;" id="msgBox">
                <div id="messageThread">
                    @foreach($ticket->messages->where('is_internal', false) as $msg)
                    @php $isSeller = $msg->sender_type === 'App\Models\Seller'; @endphp
                    <div class="d-flex gap-3 mb-4 {{ $isSeller ? 'flex-row-reverse' : '' }}" data-msg-id="{{ $msg->id }}">
                        <div class="avatar-text avatar-sm rounded flex-shrink-0
                             {{ $isSeller ? 'bg-primary text-white' : 'bg-secondary text-white' }}"
                             style="font-size:11px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                            {{ $isSeller ? 'Me' : 'CS' }}
                        </div>
                        <div style="max-width:75%;">
                            <div class="p-3 rounded {{ $isSeller ? 'bg-primary text-white' : 'bg-light' }}"
                                 style="{{ $isSeller ? 'border-radius:12px 12px 4px 12px !important' : 'border-radius:12px 12px 12px 4px !important' }}">
                                <p class="mb-0 fs-14">{{ $msg->message }}</p>
                            </div>
                            <small class="text-muted d-block mt-1 {{ $isSeller ? 'text-end' : '' }}">
                                {{ $msg->created_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if(!in_array($ticket->status, ['resolved', 'closed']))
            <div class="card-footer">
                <div id="replyError" class="alert alert-danger py-2 mb-2" style="display:none;"></div>
                <div class="d-flex gap-2">
                    <textarea id="replyMessage" class="form-control" rows="2"
                              placeholder="Type your reply..." required></textarea>
                    <button type="button" id="sendReplyBtn" class="btn btn-primary px-4">
                        <span id="sendBtnText"><i class="feather-send"></i></span>
                        <span id="sendBtnLoader" style="display:none;">
                            <span class="spinner-border spinner-border-sm"></span>
                        </span>
                    </button>
                </div>
            </div>
            @else
            <div class="card-footer">
                <div class="alert alert-secondary mb-0">
                    <i class="feather-lock me-2"></i>
                    This ticket is {{ $ticket->status }}.
                    <a href="{{ route('seller.support.create') }}" class="text-primary ms-2">Open a new ticket</a>
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
                    <span class="badge {{ sellerTicketStatusBadge($ticket->status) }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Category</small>
                    <strong>{{ ucfirst(str_replace('_', ' ', $ticket->category)) }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Priority</small>
                    @php
                    $priorityBadge = match($ticket->priority) {
                        'urgent' => 'bg-danger text-white',
                        'high'   => 'bg-warning text-dark',
                        'medium' => 'bg-info text-white',
                        default  => 'bg-secondary text-white',
                    };
                    @endphp
                    <span class="badge {{ $priorityBadge }}">
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
const ticketId    = '{{ $ticket->id }}';
const replyUrl    = '{{ route("seller.support.reply", $ticket->id) }}';
const messagesUrl = '{{ route("seller.support.messages", $ticket->id) }}';
const csrfToken   = document.querySelector('meta[name="csrf-token"]').content;

let lastMessageCount = {{ $ticket->messages->where('is_internal', false)->count() }};

function scrollToBottom() {
    const box = document.getElementById('msgBox');
    if (box) box.scrollTop = box.scrollHeight;
}

function buildBubble(msg) {
    const align     = msg.is_seller ? 'flex-row-reverse' : '';
    const avatar    = msg.is_seller ? 'bg-primary text-white' : 'bg-secondary text-white';
    const bubble    = msg.is_seller
        ? 'bg-primary text-white" style="border-radius:12px 12px 4px 12px !important'
        : 'bg-light" style="border-radius:12px 12px 12px 4px !important';
    const timeAlign = msg.is_seller ? 'text-end' : '';
    const label     = msg.is_seller ? 'Me' : 'CS';

    return `
        <div class="d-flex gap-3 mb-4 ${align}" data-msg-id="${msg.id}">
            <div class="avatar-text avatar-sm rounded flex-shrink-0 ${avatar}"
                 style="font-size:11px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                ${label}
            </div>
            <div style="max-width:75%;">
                <div class="p-3 rounded ${bubble}">
                    <p class="mb-0 fs-14">${msg.message}</p>
                </div>
                <small class="text-muted d-block mt-1 ${timeAlign}">
                    ${msg.created_at}
                </small>
            </div>
        </div>`;
}

function pollMessages() {
    fetch(messagesUrl, {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(messages => {
        if (messages.length > lastMessageCount) {
            const thread  = document.getElementById('messageThread');
            const newOnes = messages.slice(lastMessageCount);
            newOnes.forEach(msg => {
                thread.insertAdjacentHTML('beforeend', buildBubble(msg));
            });
            lastMessageCount = messages.length;
            scrollToBottom();
        }
    })
    .catch(() => {});
}

document.getElementById('sendReplyBtn')?.addEventListener('click', function () {
    const textarea  = document.getElementById('replyMessage');
    const message   = textarea.value.trim();
    const errorBox  = document.getElementById('replyError');
    const btnText   = document.getElementById('sendBtnText');
    const btnLoader = document.getElementById('sendBtnLoader');

    if (!message || message.length < 5) {
        errorBox.textContent = 'Please enter at least 5 characters.';
        errorBox.style.display = 'block';
        return;
    }

    errorBox.style.display  = 'none';
    btnText.style.display   = 'none';
    btnLoader.style.display = 'inline-block';
    this.disabled = true;

    fetch(replyUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ message })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            textarea.value = '';
            const thread = document.getElementById('messageThread');
            thread.insertAdjacentHTML('beforeend', buildBubble({
                id:         data.message.id,
                message:    data.message.message,
                is_seller:  true,
                created_at: data.message.created_at,
            }));
            lastMessageCount++;
            scrollToBottom();
        } else {
            errorBox.textContent = data.error ?? 'Failed to send. Please try again.';
            errorBox.style.display = 'block';
        }
    })
    .catch(() => {
        errorBox.textContent = 'Network error. Please try again.';
        errorBox.style.display = 'block';
    })
    .finally(() => {
        btnText.style.display   = 'inline-block';
        btnLoader.style.display = 'none';
        this.disabled = false;
    });
});

setInterval(pollMessages, 5000);
scrollToBottom();
</script>
@endpush

@endsection