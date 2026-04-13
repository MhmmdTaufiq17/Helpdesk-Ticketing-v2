<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $isOnline;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->isOnline = $user->is_online;
    }

    public function broadcastOn()
    {
        return new Channel('admin-status');
    }

    public function broadcastAs()
    {
        return 'user-status-changed';
    }
}
