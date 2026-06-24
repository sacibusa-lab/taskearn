<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::forUser(Auth::id())->latest()->paginate(15);
        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:general,account,payment,technical,other'],
            'priority' => ['required', 'in:low,medium,high'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'open',
            'messages' => [
                [
                    'sender_type' => 'user',
                    'message' => $request->message,
                    'created_at' => now()->toIso8601String(),
                ],
            ],
        ]);

        return redirect()->route('support.show', $ticket)
            ->with('success', 'Ticket created successfully. We will respond shortly.');
    }

    public function show(SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }
        return view('support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        if ($ticket->status === 'closed') {
            return back()->with('error', 'This ticket is closed.');
        }

        $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket->addMessage('user', $request->message);
        $ticket->update(['status' => 'open']);

        return back()->with('success', 'Reply sent.');
    }

    public function close(SupportTicket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Ticket closed.');
    }
}
