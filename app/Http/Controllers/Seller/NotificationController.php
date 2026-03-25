<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('notifiable_type', 'App\Models\Seller')
            ->where('notifiable_id', auth('seller')->id())
            ->latest()
            ->paginate(20);

        return view('seller.notifications.index', compact('notifications'));
    }

    public function markRead()
    {
        Notification::where('notifiable_type', 'App\Models\Seller')
            ->where('notifiable_id', auth('seller')->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function markSingle(Notification $notification)
    {
        if ($notification->notifiable_id !== auth('seller')->id()) abort(403);
        $notification->update(['read_at' => now()]);
        return back();
    }
}