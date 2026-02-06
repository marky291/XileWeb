<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Post extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('homepage:latest-posts'));
        static::deleted(fn () => Cache::forget('homepage:latest-posts'));
    }

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
        'image',
        'user_id',
        'views',
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

    /**
     * Get the user who created this post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items featured in this post.
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)
            ->withPivot('sort_order')
            ->orderByPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Get the item posts pivot records for repeater.
     */
    public function itemPosts(): HasMany
    {
        return $this->hasMany(ItemPost::class)->orderBy('sort_order');
    }
}
