<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    const CLIENT_RETRO = 'retro';

    const CLIENT_XILERO = 'xilero';

    const CLIENTS = [
        self::CLIENT_RETRO => 'Retro (Classic)',
        self::CLIENT_XILERO => 'XileRO',
    ];

    protected $fillable = [
        'title',
        'slug',
        'client',
        'patcher_notice',
        'article_content',
    ];

    protected $casts = [
        'client' => 'string',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getClientLabelAttribute(): string
    {
        return self::CLIENTS[$this->client] ?? $this->client;
    }
}
