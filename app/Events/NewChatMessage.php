<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $ticketId;
    public $sender;
    public $senderType;
    public $timestamp;

    public function __construct($message, $ticketId, $sender, $senderType)
    {
        $this->message = $message;
        $this->ticketId = $ticketId;
        $this->sender = $sender;
        $this->senderType = $senderType;
        $this->timestamp = now()->toIso8601String();
    }

    public function broadcastOn()
    {
        return new Channel('ticket.' . $this->ticketId);
    }

    public function broadcastAs()
    {
        return 'chat-message';
    }

    public function broadcastWith()
{
    return [
        'message'    => $this->message,
        'ticketId'   => $this->ticketId,
        'sender'     => $this->sender,
        'senderType' => $this->senderType,
        'timestamp'  => $this->timestamp,
    ];
}
}
