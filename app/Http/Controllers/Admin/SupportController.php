<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        if (!auth('admin')->user()->canHandleSupport()) abort(403);

        $query = SupportTicket::with('messages');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('subject', 'like', "%{$request->search}%")
                  ->orWhere('ticket_number', 'like', "%{$request->search}%");
        }

        $tickets = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'open'       => SupportTicket::where('status', 'open')->count(),
            'in_progress'=> SupportTicket::where('status', 'in_progress')->count(),
            'resolved'   => SupportTicket::where('status', 'resolved')->count(),
        ];

        return view('admin.support.index', compact('tickets', 'stats'));
    }

    public function open(Request $request)
    {
        if (!auth('admin')->user()->canHandleSupport()) abort(403);

        $tickets = SupportTicket::whereIn('status', ['open', 'waiting'])
            ->latest()->paginate(20);

        $stats = [
            'open'        => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved'    => SupportTicket::where('status', 'resolved')->count(),
            'closed'      => SupportTicket::where('status', 'closed')->count(),
        ];

        return view('admin.support.index', compact('tickets', 'stats'));
    }
    public function show(SupportTicket $ticket)
    {
        if (!auth('admin')->user()->canHandleSupport()) abort(403);
        $ticket->load('messages');

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if (!auth('admin')->user()->canHandleSupport()) abort(403);

        $request->validate(['message' => ['required', 'string', 'min:5']]);

        TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_type'       => 'App\Models\Admin',
            'sender_id'         => auth('admin')->id(),
            'message'           => $request->message,
            'is_internal'       => false,
        ]);

        $ticket->update(['status' => 'in_progress']);

        \App\Models\Notification::create([
            'notifiable_type' => $ticket->requester_type,
            'notifiable_id'   => $ticket->requester_id,
            'type'            => 'ticket_reply',
            'title'           => 'Support Reply',
            'body'            => "Admin replied to your ticket #{$ticket->ticket_number}.",
            'action_url'      => $ticket->requester_type === 'App\Models\Seller'
                                    ? route('seller.support.show', $ticket->id)
                                    : route('buyer.support.show', $ticket->id),
        ]);

        return back()->with('success', 'Reply sent.');
    }

    public function resolve(SupportTicket $ticket)
    {
        if (!auth('admin')->user()->canHandleSupport()) abort(403);
        $ticket->update(['status' => 'resolved', 'resolved_at' => now()]);
        return back()->with('success', 'Ticket marked as resolved.');
    }

    public function close(SupportTicket $ticket)
    {
        if (!auth('admin')->user()->canHandleSupport()) abort(403);
        $ticket->update(['status' => 'closed']);
        return back()->with('success', 'Ticket closed.');
    }
}