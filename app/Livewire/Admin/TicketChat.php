<?php

namespace App\Livewire\Admin;

use App\Events\NewChatMessage;
use App\Events\TicketStatusChanged;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Mail\TicketReplied;
use App\Mail\TicketStatusChanged as MailTicketStatusChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Services\GroqAIService;

class TicketChat extends Component
{
    #[Locked]
    public $ticket;
    public $message = '';
    public $messages = [];
    public $isGeneratingAI = false;  // ✅ BARU: untuk loading state

    protected $rules = [
        'message' => 'required|string|max:2000',
    ];

    public function mount($ticketId)
    {
        $this->ticket = Ticket::with(['replies' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])->findOrFail($ticketId);

        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->ticket->load(['replies' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        $this->messages = $this->ticket->replies->map(function ($reply) {
            return [
                'id'          => $reply->id,
                'message'     => $reply->message,
                'sender_type' => $reply->sender_type,
                'sender_name' => $reply->sender_type === 'admin'
                    ? ($reply->user->name ?? 'Admin')
                    : $this->ticket->client_name,
                'created_at'  => $reply->created_at->format('d M Y, H:i'),
                'timestamp'   => $reply->created_at->toIso8601String(),
            ];
        })->toArray();
    }

    /**
     * ✅ BARU: Generate balasan menggunakan AI
     */
    public function generateAIReply()
    {
        if ($this->ticket->status === 'closed') {
            $this->dispatch('error', 'Tiket sudah ditutup, tidak bisa generate balasan AI.');
            return;
        }

        $this->isGeneratingAI = true;

        try {
            $aiService = new GroqAIService();

            // Ambil riwayat chat terakhir (max 10 pesan)
            $recentMessages = array_slice($this->messages, -10);

            // Generate balasan dari AI
            $reply = $aiService->generateReply($this->ticket, $recentMessages);

            // Isi ke form message
            $this->message = $reply;

            $this->dispatch('ai-reply-generated', '🤖 Balasan AI berhasil dibuat, silakan edit jika perlu sebelum mengirim.');

        } catch (\Exception $e) {
            \Log::error('AI generate reply error: ' . $e->getMessage());
            $this->dispatch('error', 'Gagal generate balasan AI: ' . $e->getMessage());
        }

        $this->isGeneratingAI = false;
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
            'user_id'     => Auth::id(),
            'message'     => $this->message,
            'sender_type' => 'admin',
        ]);

        // Hitung jumlah balasan admin
        $adminRepliesCount = TicketReply::where('ticket_id', $this->ticket->id)
            ->where('sender_type', 'admin')
            ->count();

        // Kirim email untuk balasan pertama dari admin
        if ($adminRepliesCount === 1) {
            try {
                $adminName = Auth::user()->name ?? 'Admin';
                Mail::to($this->ticket->client_email)->send(new TicketReplied($this->ticket, $reply, $adminName));
                \Log::info('Email balasan terkirim ke ' . $this->ticket->client_email . ' untuk tiket #' . $this->ticket->ticket_code);
            } catch (\Exception $e) {
                \Log::error('Gagal kirim email balasan: ' . $e->getMessage());
            }
        }

        // Update status jika masih open
        $oldStatus = $this->ticket->status;
        if ($this->ticket->status === 'open') {
            $this->ticket->update(['status' => 'in_progress']);

            // Kirim email notifikasi perubahan status
            try {
                Mail::to($this->ticket->client_email)->send(new MailTicketStatusChanged(
                    $this->ticket,
                    $oldStatus,
                    'in_progress',
                    'Status otomatis diperbarui karena admin membalas tiket.'
                ));
                \Log::info('Email perubahan status terkirim ke ' . $this->ticket->client_email);
            } catch (\Exception $e) {
                \Log::error('Gagal kirim email perubahan status: ' . $e->getMessage());
            }

            // BROADCAST EVENT untuk update status badge real-time
            broadcast(new TicketStatusChanged($this->ticket->id, 'in_progress'))->toOthers();

            // Juga dispatch ke component yang sama (untuk update lokal)
            $this->dispatch('ticket-status-updated', ticketId: $this->ticket->id);
        }

        broadcast(new NewChatMessage(
            $this->message,
            $this->ticket->id,
            Auth::user()->name,
            'admin'
        ))->toOthers();

        $this->messages[] = [
            'id'          => $reply->id,
            'message'     => $reply->message,
            'sender_type' => $reply->sender_type,
            'sender_name' => Auth::user()->name,
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
            'sender_type' => $payload['senderType'] ?? 'user',
            'sender_name' => $payload['sender'] ?? 'User',
            'created_at'  => \Carbon\Carbon::parse($payload['timestamp'] ?? now())->format('d M Y, H:i'),
            'timestamp'   => $payload['timestamp'] ?? now()->toIso8601String(),
        ];

        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.admin.ticket-chat');
    }
}
