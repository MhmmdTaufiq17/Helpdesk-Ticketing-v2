<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketId;
    public $status;

    public function __construct($ticketId, $status)
    {
        $this->ticketId = $ticketId;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new Channel('ticket.' . $this->ticketId);
    }

    public function broadcastAs()
    {
        return 'status-changed';
    }
}
