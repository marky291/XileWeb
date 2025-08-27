<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'client' => 'string',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function getClientLabelAttribute(): string
    {
        return self::CLIENTS[$this->client] ?? $this->client;
    }
}
