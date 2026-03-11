<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Whitelist extends Model
{
    public $timestamps = false;

    protected $table = 'whitelist';

    protected $fillable = ['phone', 'role', 'added_by'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public static function isWhitelisted(string $phone): bool
    {
        return static::where('phone', $phone)->exists();
    }
}
