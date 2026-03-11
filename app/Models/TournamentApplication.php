<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TournamentApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id', 'team_id', 'applied_by', 'status', 'rejected_reason',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tournament_application_players', 'application_id', 'user_id')
                     ->withTimestamps();
    }
}
