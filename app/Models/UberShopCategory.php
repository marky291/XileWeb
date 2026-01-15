<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string|null $tagline
 * @property string|null $uber_range
 * @property int $display_order
 * @property bool $enabled
 * @property bool $is_xilero
 * @property bool $is_xileretro
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read string $clean_display_name
 * @property-read string|null $exclusive_server
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UberShopItem> $items
 */
class UberShopCategory extends Model
{
    /** @use HasFactory<\Database\Factories\UberShopCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'tagline',
        'uber_range',
        'display_order',
        'enabled',
        'is_xilero',
        'is_xileretro',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'is_xilero' => 'boolean',
            'is_xileretro' => 'boolean',
        ];
    }

    /**
     * @return HasMany<UberShopItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(UberShopItem::class, 'category_id');
    }

    /**
     * Strip Ragnarok Online color codes (^RRGGBB) from display name.
     */
    public function getCleanDisplayNameAttribute(): string
    {
        // Strip RO color codes like ^FF0000 or ^000000
        $clean = preg_replace('/\^[0-9a-fA-F]{6}/', '', $this->display_name);

        return trim($clean);
    }

    /**
     * Check if the category is available for a specific server.
     */
    public function isAvailableForServer(string $server): bool
    {
        return match ($server) {
            'xilero' => $this->is_xilero,
            'xileretro' => $this->is_xileretro,
            default => false,
        };
    }

    /**
     * Get the exclusive server name if the category is only available on one server.
     * Returns null if the category is available on both servers.
     */
    public function getExclusiveServerAttribute(): ?string
    {
        if ($this->is_xilero && $this->is_xileretro) {
            return null;
        }

        if ($this->is_xilero) {
            return 'XileRO';
        }

        if ($this->is_xileretro) {
            return 'XileRetro';
        }

        return null;
    }
}
