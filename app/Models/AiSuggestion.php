<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'ai_summary',
        'ai_suggested_category',
        'ai_suggested_priority'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
