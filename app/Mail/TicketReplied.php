<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketReplied extends Mailable
{
    use SerializesModels;

    public $ticket;
    public $reply;
    public $adminName;
    public $adminInitial;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, TicketReply $reply, $adminName)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
        $this->adminName = $adminName;

        // Ambil initial dari user admin berdasarkan nama
        $admin = User::where('name', $adminName)->first();
        $this->adminInitial = $admin ? $admin->getInitial() : strtoupper(substr($adminName, 0, 2));
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Balasan Baru untuk Tiket #' . $this->ticket->ticket_code;

        return $this->subject($subject)
                    ->view('emails.ticket_replied');
    }
}
