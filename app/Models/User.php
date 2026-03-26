<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'iin', 'email', 'password', 'telegram_id', 'avatar',
        'role', 'verification_status', 'verification_document',
        'subscription_status', 'subscription_expires_at',
        'is_blocked', 'del', 'city', 'specialization',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'subscription_expires_at' => 'datetime',
            'is_blocked' => 'boolean',
            'del' => 'boolean',
        ];
    }

    // --- Relationships ---

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function captainOfTeam(): HasOne
    {
        return $this->hasOne(Team::class, 'captain_id');
    }

    // --- Scopes ---

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'approved');
    }

    public function scopeActiveSubscription($query)
    {
        return $query->where('subscription_status', 'active')
                     ->where('subscription_expires_at', '>', now());
    }

    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }

    // --- Helpers ---

    public function isActive(): bool
    {
        return $this->verification_status === 'approved'
            && $this->subscription_status === 'active'
            && $this->subscription_expires_at
            && $this->subscription_expires_at->isFuture();
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'approved';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isCaptain(): bool
    {
        return $this->role === 'captain';
    }

    public function currentTeam()
    {
        $membership = $this->teamMemberships()
            ->where('status', 'approved')
            ->whereNull('left_at')
            ->with('team')
            ->first();

        return $membership?->team;
    }

    public function currentTeamMembership(): ?TeamMember
    {
        return $this->teamMemberships()
            ->where('status', 'approved')
            ->whereNull('left_at')
            ->first();
    }
}
