<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class AdminOnlineStatus extends Component
{
    public $admins = [];
    public $onlineCount = 0;

    public function mount()
    {
        $this->loadAdmins();
    }

    public function loadAdmins()
    {
        // Ambil semua admin (bisa disesuaikan dengan role)
        $this->admins = User::where('is_admin', true) // atau sesuai role
            ->orderBy('is_online', 'desc')
            ->orderBy('last_seen_at', 'desc')
            ->get()
            ->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'is_online' => $admin->is_online,
                    'last_seen' => $admin->last_seen_at ? $admin->last_seen_at->diffForHumans() : 'Tidak pernah',
                    'avatar' => $admin->getInitial(),
                ];
            })
            ->toArray();

        $this->onlineCount = User::where('is_admin', true)->where('is_online', true)->count();
    }

    #[On('echo:admin-status,user-status-changed')]
    public function refreshStatus($payload)
    {
        $this->loadAdmins();
        $this->dispatch('status-refreshed');
    }

    public function render()
    {
        return view('livewire.admin.admin-online-status');
    }
}
