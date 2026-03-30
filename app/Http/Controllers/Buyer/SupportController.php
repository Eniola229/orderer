<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index() 
    {
        $tickets = SupportTicket::where('requester_type', 'App\Models\User')
            ->where('requester_id', auth('web')->id())
            ->with('messages')
            ->latest()
            ->paginate(15);

        return view('buyer.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('buyer.support.create');
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
            'requester_type' => 'App\Models\User',
            'requester_id'   => auth('web')->id(),
            'category'       => $request->category,
            'priority'       => $request->priority,
            'status'         => 'open',
        ]);

        TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_type'       => 'App\Models\User',
            'sender_id'         => auth('web')->id(),
            'message'           => $request->message,
            'is_internal'       => false,
        ]);

        return redirect()->route('buyer.support.show', $ticket->id)
            ->with('success', "Ticket #{$ticket->ticket_number} created. We'll respond within 24 hours.");
    }

    public function show(SupportTicket $ticket)
    {
        if ($ticket->requester_id !== auth('web')->id()) abort(403);

        $ticket->load('messages');

        TicketMessage::where('support_ticket_id', $ticket->id)
            ->where('sender_type', 'App\Models\Admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('buyer.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->requester_id !== auth('web')->id()) abort(403);

        if (in_array($ticket->status, ['resolved', 'closed'])) {
            return response()->json(['success' => false, 'error' => 'This ticket is closed.'], 403);
        }

        $request->validate(['message' => ['required', 'string', 'min:5']]);

        $msg = TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_type'       => 'App\Models\User',
            'sender_id'         => auth('web')->id(),
            'message'           => $request->message,
            'is_internal'       => false,
        ]);

        $ticket->update(['status' => 'waiting']);

        return response()->json([
            'success' => true,
            'message' => [
                'id'         => $msg->id,
                'message'    => $msg->message,
                'is_buyer'   => true,
                'created_at' => $msg->created_at->format('M d, Y H:i'),
            ]
        ]);
    }

    public function messages(SupportTicket $ticket)
    {
        if ($ticket->requester_id !== auth('web')->id()) abort(403);
        
        $messages = $ticket->messages()
            ->where('is_internal', false)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'          => $msg->id,
                    'message'     => $msg->message,
                    'is_buyer'    => $msg->sender_type === 'App\Models\User',
                    'created_at'  => $msg->created_at->format('M d, Y H:i'),
                ];
            });

        return response()->json($messages);
    }
}
