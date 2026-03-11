<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'start_date', 'end_date',
        'venue', 'max_teams', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(TournamentApplication::class);
    }

    public function approvedApplications(): HasMany
    {
        return $this->applications()->where('status', 'approved');
    }

    public function activeApplications(): HasMany
    {
        return $this->applications()->whereIn('status', ['pending', 'approved']);
    }

    public function scopeVisible($query)
    {
        return $query->where('status', '!=', 'draft');
    }

    public function scopeOpenForRegistration($query)
    {
        return $query->where('status', 'registration');
    }

    public function isFull(): bool
    {
        if (!$this->max_teams) {
            return false;
        }
        return $this->activeApplications()->count() >= $this->max_teams;
    }

    public function isOpenForRegistration(): bool
    {
        return $this->status === 'registration';
    }
}
