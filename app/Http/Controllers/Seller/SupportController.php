<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('requester_type', 'App\Models\Seller')
            ->where('requester_id', auth('seller')->id())
            ->with('messages')
            ->latest()
            ->paginate(15);

        return view('seller.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('seller.support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'  => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:order_issue,payment,account,product,shipping,other'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'message'  => ['required', 'string', 'min:20'],
        ]);

        $ticket = SupportTicket::create([
            'subject'        => $request->subject,
            'requester_type' => 'App\Models\Seller',
            'requester_id'   => auth('seller')->id(),
            'category'       => $request->category,
            'priority'       => $request->priority,
            'status'         => 'open',
        ]);

        TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_type'       => 'App\Models\Seller',
            'sender_id'         => auth('seller')->id(),
            'message'           => $request->message,
            'is_internal'       => false,
        ]);

        return redirect()->route('seller.support.show', $ticket->id)
            ->with('success', "Ticket #{$ticket->ticket_number} created. We'll respond within 24 hours.");
    }

    public function show(SupportTicket $ticket)
    {
        if ($ticket->requester_id !== auth('seller')->id()) abort(403);

        $ticket->load('messages');

        // Mark admin messages as read
        TicketMessage::where('support_ticket_id', $ticket->id)
            ->where('sender_type', 'App\Models\Admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('seller.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->requester_id !== auth('seller')->id()) abort(403);

        if (in_array($ticket->status, ['resolved', 'closed'])) {
            return back()->with('error', 'This ticket is closed. Open a new one if needed.');
        }

        $request->validate([
            'message' => ['required', 'string', 'min:5'],
        ]);

        TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_type'       => 'App\Models\Seller',
            'sender_id'         => auth('seller')->id(),
            'message'           => $request->message,
            'is_internal'       => false,
        ]);

        $ticket->update(['status' => 'waiting']);

        return back()->with('success', 'Reply sent.');
    }
}