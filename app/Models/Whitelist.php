<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Whitelist extends Model
{
    public $timestamps = false;

    protected $table = 'whitelist';

    protected $fillable = ['iin', 'role', 'added_by'];

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

    public static function isWhitelisted(string $iin): bool
    {
        return static::where('iin', $iin)->exists();
    }
}
