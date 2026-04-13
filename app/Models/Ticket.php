<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'user_id',
        'client_name',
        'client_email',
        'category_id',
        'title',
        'description',
        'ai_summary',        // ✅ BARU
        'attachment',
        'status',
        'priority',
        'last_active_admin_id',
        'admin_last_active_at',
        'last_admin_read_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_code)) {
                $ticket->ticket_code = 'TKT'.str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }
            // ✅ Priority default null (bukan medium)
            if (is_null($ticket->priority)) {
                $ticket->priority = null;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function histories()
    {
        return $this->hasMany(TicketHistory::class)->orderBy('created_at', 'asc');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at', 'asc');
    }

    public function aiSuggestion()
    {
        return $this->hasOne(AiSuggestion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
