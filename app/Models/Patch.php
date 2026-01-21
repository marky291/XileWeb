<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patch extends Model
{
    use HasFactory;

    const CLIENT_RETRO = 'retro';

    const CLIENT_XILERO = 'xilero';

    const CLIENTS = [
        self::CLIENT_RETRO => 'Retro (Classic)',
        self::CLIENT_XILERO => 'XileRO',
    ];

    protected $fillable = [
        'number',
        'type',
        'client',
        'patch_name',
        'file',
        'comments',
        'post_id',
        'is_compiling',
        'compiled_at',
    ];

    protected function casts(): array
    {
        return [
            'client' => 'string',
            'is_compiling' => 'boolean',
            'compiled_at' => 'datetime',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function getClientLabelAttribute(): string
    {
        return self::CLIENTS[$this->client] ?? $this->client;
    }

    /**
     * Get items whose data was last updated by this patch.
     */
    public function dataItems(): HasMany
    {
        return $this->hasMany(Item::class, 'data_patch_id');
    }

    /**
     * Get items whose sprites were last updated by this patch.
     */
    public function spriteItems(): HasMany
    {
        return $this->hasMany(Item::class, 'sprite_patch_id');
    }
}
