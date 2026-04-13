<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/TicketHistory.php
class TicketHistory extends Model
{
    protected $fillable = ['ticket_id', 'status', 'changed_by', 'note'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
