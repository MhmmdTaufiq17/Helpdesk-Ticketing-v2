<?php

namespace App\Livewire\User;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Events\NewChatMessage;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class TicketChat extends Component
{
    #[Locked]
    public $ticket;
    public $message = '';
    public $messages = [];

    // Untuk status admin online
    public $isAdminOnline = false;
    public $lastAdminName = null;

    protected $rules = [
        'message' => 'required|string|max:2000',
    ];

    public function mount($ticketId)
    {
        $this->ticket = Ticket::with(['replies' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }, 'replies.user'])->findOrFail($ticketId);

        if (session('tracked_ticket_code') !== $this->ticket->ticket_code) {
            abort(403);
        }

        $this->loadMessages();
        $this->loadAdminStatus();
    }

    public function loadMessages()
    {
        $this->messages = $this->ticket->replies->map(function ($reply) {
            return [
                'id'          => $reply->id,
                'message'     => $reply->message,
                'sender_type' => $reply->sender_type,
                'sender_name' => $reply->sender_type === 'admin'
                    ? ($reply->user->name ?? 'Admin')
                    : $this->ticket->client_name,
                'sender_initial' => $reply->sender_type === 'admin'
                    ? ($reply->user->getInitial() ?? 'AD')
                    : substr($this->ticket->client_name, 0, 2),
                'created_at'  => $reply->created_at->format('d M Y, H:i'),
                'timestamp'   => $reply->created_at->toIso8601String(),
            ];
        })->toArray();
    }

    /**
     * Load status admin - Cek admin yang sedang membuka tiket ini
     */
    public function loadAdminStatus()
    {
        // Cek apakah ada admin yang sedang membuka tiket ini
        $activeAdminId = $this->ticket->last_active_admin_id;
        $lastActiveAt = $this->ticket->admin_last_active_at;

        if ($activeAdminId && $lastActiveAt) {
            // Cek apakah masih aktif (last active kurang dari 2 menit)
            $isStillActive = now()->diffInMinutes($lastActiveAt) < 2;

            if ($isStillActive) {
                $admin = User::find($activeAdminId);
                $this->isAdminOnline = true;
                $this->lastAdminName = $admin?->name;
                return;
            }
        }

        // Jika tidak ada admin yang aktif di tiket ini
        $this->isAdminOnline = false;
        $this->lastAdminName = null;
    }

    public function sendMessage()
    {
        if ($this->ticket->status === 'closed') {
            $this->dispatch('error', 'Tiket sudah ditutup, tidak dapat mengirim pesan.');
            return;
        }

        $this->validate();

        $reply = TicketReply::create([
            'ticket_id'   => $this->ticket->id,
            'user_id'     => null,
            'message'     => $this->message,
            'sender_type' => 'user',
        ]);

        // Broadcast ke admin
        broadcast(new NewChatMessage(
            $this->message,
            $this->ticket->id,
            $this->ticket->client_name,
            'user'
        ))->toOthers();

        // Local append optimization
        $this->messages[] = [
            'id'          => $reply->id,
            'message'     => $reply->message,
            'sender_type' => $reply->sender_type,
            'sender_name' => $this->ticket->client_name,
            'sender_initial' => substr($this->ticket->client_name, 0, 2),
            'created_at'  => $reply->created_at->format('d M Y, H:i'),
            'timestamp'   => $reply->created_at->toIso8601String(),
        ];

        $this->message = '';
        $this->dispatch('scroll-to-bottom');
    }

    #[On('echo:ticket.{ticket.id},.chat-message')]
    public function handleNewMessage($payload)
    {
        $this->messages[] = [
            'id'          => uniqid(),
            'message'     => $payload['message'] ?? '',
            'sender_type' => $payload['senderType'] ?? 'admin',
            'sender_name' => $payload['sender'] ?? 'Admin',
            'sender_initial' => isset($payload['sender']) ? substr($payload['sender'], 0, 2) : 'AD',
            'created_at'  => \Carbon\Carbon::parse($payload['timestamp'] ?? now())->format('d M Y, H:i'),
            'timestamp'   => $payload['timestamp'] ?? now()->toIso8601String(),
        ];

        $this->dispatch('scroll-to-bottom');

        // Refresh status admin setiap ada pesan baru dari admin
        $this->loadAdminStatus();
        $this->dispatch('admin-status-updated', isOnline: $this->isAdminOnline, adminName: $this->lastAdminName);
    }

    /**
     * ✅ Listen ke broadcast event dari admin (online/offline)
     */
    #[On('echo:ticket.{ticket.id},.admin-status-changed')]
    public function handleAdminStatusChanged($payload)
    {
        $this->isAdminOnline = $payload['isOnline'] ?? false;
        $this->lastAdminName = $payload['adminName'] ?? null;

        // Update UI
        $this->dispatch('admin-status-updated', isOnline: $this->isAdminOnline, adminName: $this->lastAdminName);
    }

    public function render()
    {
        return view('livewire.user.ticket-chat');
    }

    public function dispatchScrollToBottom()
    {
        $this->dispatch('scroll-to-bottom');
    }
}
