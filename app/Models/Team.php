<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'logo', 'city', 'description', 'captain_id', 'captain_phone',
    ];

    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function activeMembers()
    {
        return $this->members()->where('status', 'approved')->whereNull('left_at');
    }

    public function activeMembersUsers()
    {
        return $this->activeMembers()->with('user');
    }

    public function scopeWithActiveMembers($query)
    {
        return $query->with(['members' => function ($q) {
            $q->where('status', 'approved')->whereNull('left_at')->with('user');
        }]);
    }
}
