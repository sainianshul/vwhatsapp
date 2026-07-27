<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(\App\DataTables\TicketsDataTable $dataTable)
    {
        return $dataTable->render('tickets.index');
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Ticket::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'open',
        ]);

        return redirect()->route('tickets.index')->with('success', 'Support ticket created successfully. We will get back to you soon.');
    }

    public function show(Ticket $ticket)
    {
        $user = auth()->user();

        // Ensure user can only view their own tickets (admin can view all)
        if ($user->role !== 'admin' && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        // Only admin can close/reopen
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['status' => 'required|in:open,closed']);
        
        $ticket->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Ticket status updated.');
    }
}
