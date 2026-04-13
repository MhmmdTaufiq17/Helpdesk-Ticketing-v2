<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketId;
    public $adminName;
    public $isOnline;

    public function __construct($ticketId, $adminName, $isOnline)
    {
        $this->ticketId = $ticketId;
        $this->adminName = $adminName;
        $this->isOnline = $isOnline;
    }

    public function broadcastOn()
    {
        return new Channel('ticket.' . $this->ticketId);
    }

    public function broadcastAs()
    {
        return 'admin-status-changed';
    }
}
