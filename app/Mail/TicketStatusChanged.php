<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $oldStatus;
    public $newStatus;
    public $note;
    public $timeline;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, $oldStatus, $newStatus, $note = null)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->note = $note;

        // Ambil riwayat status untuk ditampilkan di timeline
        $this->timeline = $ticket->histories()->latest()->take(5)->get()->map(function($history) {
            return [
                'status' => $history->status,
                'date' => $history->created_at->format('d F Y, H:i'),
                'note' => $history->note
            ];
        })->reverse()->values();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Update Status Tiket #' . $this->ticket->ticket_code . ' - ' . ucfirst($this->newStatus);

        return $this->subject($subject)
                    ->view('emails.ticket_status_changed');
    }
}
