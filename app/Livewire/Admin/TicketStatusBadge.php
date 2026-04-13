<?php

namespace App\Livewire\Admin;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\Attributes\On;

class TicketStatusBadge extends Component
{
    public $ticket;
    public $status;
    public $statusLabel;
    public $statusColor;

    public function mount($ticketId)
    {
        $this->ticket = Ticket::findOrFail($ticketId);
        $this->updateStatus();
    }

    #[On('ticket-status-updated')]
    public function refreshStatus($ticketId)
    {
        if ($this->ticket->id == $ticketId) {
            $this->ticket->refresh();
            $this->updateStatus();
        }
    }

    #[On('echo:ticket.{ticket.id},.status-changed')]
    public function handleStatusChanged($payload)
    {
        $this->ticket->refresh();
        $this->updateStatus();
    }

    protected function updateStatus()
    {
        $this->status = $this->ticket->status;

        $labels = [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'closed' => 'Closed'
        ];
        $this->statusLabel = $labels[$this->status] ?? ucfirst($this->status);

        $colors = [
            'open' => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-yellow-100 text-yellow-700',
            'closed' => 'bg-green-100 text-green-700'
        ];
        $this->statusColor = $colors[$this->status] ?? 'bg-gray-100 text-gray-700';
    }

    public function render()
    {
        return view('livewire.admin.ticket-status-badge');
    }
}
