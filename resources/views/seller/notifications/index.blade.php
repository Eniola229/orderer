@extends('layouts.seller')
@section('title', 'Notifications')
@section('page_title', 'Notifications')
@section('breadcrumb')
    <li class="breadcrumb-item active">Notifications</li>
@endsection
@section('page_actions')
    <form action="{{ route('seller.notifications.read') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-primary btn-sm">
            <i class="feather-check me-1"></i> Mark All Read
        </button>
    </form>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @forelse($notifications as $notif)
        <div class="d-flex align-items-start gap-3 p-3 border-bottom {{ $notif->isRead() ? '' : 'bg-light' }}">
            <div class="avatar-text avatar-md rounded {{ $notif->isRead() ? 'bg-light text-muted' : 'bg-primary text-white' }}">
                <i class="feather-bell"></i>
            </div>
            <div class="flex-grow-1">
                <p class="mb-1 fw-semibold fs-14">{{ $notif->title }}</p>
                <p class="mb-1 text-muted fs-13">{{ $notif->body }}</p>
                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(!$notif->isRead())
                <form action="{{ route('seller.notifications.single', $notif->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="feather-check"></i>
                    </button>
                </form>
                @endif
                @if($notif->action_url)
                <a href="{{ $notif->action_url }}" class="btn btn-sm btn-outline-primary">
                    View
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="feather-bell mb-2 d-block" style="font-size:36px;"></i>
            No notifications yet.
        </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div class="card-footer">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection