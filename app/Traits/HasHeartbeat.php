<?php

namespace App\Traits;

use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

trait HasHeartbeat
{
    public $isActive = true;
    public $lastHeartbeat = null;

    #[On('heartbeat')]
    public function handleHeartbeat($payload)
    {
        $isActive = $payload['active'] ?? true;
        $this->isActive = $isActive;

        if (Auth::check()) {
            Auth::user()->updateOnlineStatus($isActive);
        }
    }

    public function setUserOffline()
    {
        if (Auth::check()) {
            Auth::user()->updateOnlineStatus(false);
        }
    }
}
