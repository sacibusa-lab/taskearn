<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with('user')->latest()->paginate(20);
        return view('admin.support.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('user');
        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket->addMessage('admin', $request->message);
        $ticket->update(['status' => 'replied']);

        // Notify the user
        NotificationService::send(
            $ticket->user_id,
            'withdrawal_status', // reuse a general notification icon
            'Support Ticket Updated',
            'Admin replied to your ticket: "' . $ticket->subject . '". Check your support tickets for the response.',
            route('support.show', $ticket)
        );

        return back()->with('success', 'Reply sent to user.');
    }

    public function close(SupportTicket $ticket)
    {
        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Ticket closed.');
    }

    public function reopen(SupportTicket $ticket)
    {
        $ticket->update([
            'status' => 'open',
            'closed_at' => null,
        ]);

        return back()->with('success', 'Ticket reopened.');
    }
}
