<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncedCharacter extends Model
{
    /** @use HasFactory<\Database\Factories\SyncedCharacterFactory> */
    use HasFactory;

    protected $fillable = [
        'game_account_id',
        'char_id',
        'name',
        'class_name',
        'base_level',
        'job_level',
        'zeny',
        'last_map',
        'guild_name',
        'online',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'online' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function gameAccount(): BelongsTo
    {
        return $this->belongsTo(GameAccount::class);
    }
}
