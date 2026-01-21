<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UberShopItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $category_id
 * @property int $item_id
 * @property int $uber_cost
 * @property int $quantity
 * @property int $refine_level
 * @property int|null $stock
 * @property int $display_order
 * @property bool $enabled
 * @property int $views
 * @property bool $is_xilero
 * @property bool $is_xileretro
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read UberShopCategory|null $category
 * @property-read Item $item
 * @property-read bool $is_available
 * @property-read string $display_name
 * @property-read string|null $exclusive_server
 */
class UberShopItem extends Model
{
    /** @use HasFactory<UberShopItemFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'item_id',
        'uber_cost',
        'quantity',
        'refine_level',
        'stock',
        'display_order',
        'enabled',
        'views',
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
     * @return BelongsTo<UberShopCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(UberShopCategory::class, 'category_id');
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function getIsAvailableAttribute(): bool
    {
        if (! $this->enabled) {
            return false;
        }

        if ($this->stock !== null && $this->stock <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Get the display name with refine level prefix if applicable.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->item?->name ?? 'Unknown Item';

        if ($this->refine_level > 0) {
            $name = '+'.$this->refine_level.' '.$name;
        }

        return $name;
    }

    /**
     * Check if the item is available for a specific server.
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
     * Get the exclusive server name if the item is only available on one server.
     * Returns null if the item is available on both servers.
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
