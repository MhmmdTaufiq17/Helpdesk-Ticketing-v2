<?php

namespace App\Models;

use App\Events\UserStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'is_online',
        'last_seen_at',
        'new_email',
        'email_change_token',
        'email_change_token_expires_at',
        'password_reset_token',  // ✅ Tambahkan
        'password_reset_token_expires_at',  // ✅ Tambahkan
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_online' => 'boolean',
            'last_seen_at' => 'datetime',
            'email_change_token_expires_at' => 'datetime',
        ];
    }

    public function ticketReplies()
    {
        return $this->hasMany(TicketReply::class);
    }

    // =============================================================
    // ONLINE STATUS METHODS (REALTIME)
    // =============================================================

    /**
     * Update online status user dan broadcast ke realtime
     */
    public function updateOnlineStatus($isOnline)
    {
        $this->update([
            'is_online' => $isOnline,
            'last_seen_at' => now(),
        ]);

        // Broadcast ke semua admin yang sedang online
        broadcast(new UserStatusChanged($this))->toOthers();
    }

    /**
     * Set user sebagai online
     */
    public function setOnline()
    {
        $this->updateOnlineStatus(true);
    }

    /**
     * Set user sebagai offline
     */
    public function setOffline()
    {
        $this->updateOnlineStatus(false);
    }

    /**
     * Check apakah user sedang online
     */
    public function isOnline()
    {
        return $this->is_online === true;
    }

    /**
     * Get last seen human readable
     */
    public function getLastSeenHumanAttribute()
    {
        if (! $this->last_seen_at) {
            return 'Tidak pernah aktif';
        }

        if ($this->is_online) {
            return 'Online';
        }

        return 'Terakhir aktif '.$this->last_seen_at->diffForHumans();
    }

    /**
     * Get status badge color untuk online/offline
     */
    public function getOnlineStatusBadgeAttribute()
    {
        if ($this->is_online) {
            return [
                'color' => 'bg-green-100 text-green-800',
                'dot' => 'bg-green-500',
                'text' => 'Online',
            ];
        }

        return [
            'color' => 'bg-gray-100 text-gray-500',
            'dot' => 'bg-gray-400',
            'text' => 'Offline',
        ];
    }

    // =============================================================
    // EMAIL CHANGE METHODS
    // =============================================================

    /**
     * Request email change with verification
     */
    public function requestEmailChange($newEmail, $token)
    {
        $this->update([
            'new_email' => $newEmail,
            'email_change_token' => $token,
            'email_change_token_expires_at' => now()->addMinutes(30),
        ]);
    }

    /**
     * Confirm email change with token
     */
    public function confirmEmailChange($token)
    {
        if ($this->email_change_token !== $token) {
            return false;
        }

        if ($this->email_change_token_expires_at < now()) {
            return false;
        }

        $this->update([
            'email' => $this->new_email,
            'new_email' => null,
            'email_change_token' => null,
            'email_change_token_expires_at' => null,
        ]);

        return true;
    }

    /**
     * Check if user has pending email change
     */
    public function hasPendingEmailChange()
    {
        return ! is_null($this->new_email) &&
               ! is_null($this->email_change_token) &&
               $this->email_change_token_expires_at > now();
    }

    // =============================================================
    // ROLE CHECK METHODS
    // =============================================================

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAnyAdmin()
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function canManageAdmins()
    {
        return $this->isSuperAdmin();
    }

    // =============================================================
    // AVATAR METHODS - WARNA FIX UNGU
    // =============================================================

    /**
     * Get user initial (2 huruf pertama)
     *
     * @return string
     */
    public function getInitial()
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    // =============================================================
    // ROLE LABEL METHODS
    // =============================================================

    public function getRoleLabelAttribute()
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            default => 'Unknown',
        };
    }

    public function getRoleBadgeColorAttribute()
    {
        return match ($this->role) {
            'super_admin' => 'bg-purple-100 text-purple-800',
            'admin' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function scopeAllAdmins($query)
    {
        return $query->whereIn('role', ['admin', 'super_admin']);
    }

    /**
     * Scope untuk user yang sedang online
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    /**
     * Scope untuk user yang offline
     */
    public function scopeOffline($query)
    {
        return $query->where('is_online', false);
    }
}
