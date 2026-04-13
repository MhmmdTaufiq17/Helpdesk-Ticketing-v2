<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketTrackController extends Controller
{
    public function showTrackForm()
    {
        return view('user.tickets.track');
    }

    public function track(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string|max:20'
        ], [
            'ticket_code.required' => 'Kode tiket wajib diisi'
        ]);

        $ticketCode = strtoupper(trim($request->ticket_code));
        $ticket = Ticket::where('ticket_code', $ticketCode)->first();

        if (!$ticket) {
            return back()->withErrors([
                'ticket_code' => 'Tiket tidak ditemukan. Pastikan kode tiket sudah benar.'
            ])->withInput();
        }

        return redirect()->route('user.tickets.track.result', $ticket->ticket_code);
    }

public function showTrackResult($ticket_code)
{
    $ticketCode = strtoupper(trim($ticket_code));

    $ticket = Ticket::with(['category', 'histories', 'replies' => function($query) {
                        $query->where('sender_type', 'admin');
                    }, 'replies.user'])
                    ->where('ticket_code', $ticketCode)
                    ->firstOrFail();

    // Simpan ke session sebagai bukti akses
    session(['tracked_ticket_code' => $ticket->ticket_code]);

    return view('user.tickets.track-result', compact('ticket'));
}

    public function trackAjax(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string|max:20'
        ], [
            'ticket_code.required' => 'Kode tiket wajib diisi'
        ]);

        $ticketCode = strtoupper(trim($request->ticket_code));
        $ticket = Ticket::where('ticket_code', $ticketCode)->first();

        if (!$ticket) {
            return response()->json([
                'message' => 'Tiket tidak ditemukan. Pastikan kode tiket sudah benar.'
            ], 404);
        }

        return response()->json([
            'ticket'   => $ticket,
            'redirect' => route('user.tickets.track.result', $ticket->ticket_code),
        ]);
    }
}
