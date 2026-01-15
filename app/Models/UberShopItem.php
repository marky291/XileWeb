<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $category_id
 * @property int|null $database_item_id
 * @property string|null $display_name
 * @property int $uber_cost
 * @property int $quantity
 * @property int $refine_level
 * @property int|null $stock
 * @property int $display_order
 * @property bool $enabled
 * @property bool $is_xilero
 * @property bool $is_xileretro
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read UberShopCategory|null $category
 * @property-read DatabaseItem|null $databaseItem
 * @property-read bool $is_available
 * @property-read string $formatted_display_name
 * @property-read string|null $exclusive_server
 */
class UberShopItem extends Model
{
    /** @use HasFactory<\Database\Factories\UberShopItemFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'database_item_id',
        'display_name',
        'uber_cost',
        'quantity',
        'refine_level',
        'stock',
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
     * @return BelongsTo<UberShopCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(UberShopCategory::class, 'category_id');
    }

    /**
     * @return BelongsTo<DatabaseItem, $this>
     */
    public function databaseItem(): BelongsTo
    {
        return $this->belongsTo(DatabaseItem::class, 'database_item_id');
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
     * Get the display name, with refine level prefix if applicable.
     */
    public function getDisplayNameAttribute(?string $value): string
    {
        $name = $value ?? $this->databaseItem?->name ?? 'Unknown Item';

        if ($this->refine_level > 0) {
            $name = '+'.$this->refine_level.' '.$name;
        }

        return $name;
    }

    /**
     * Get the raw item name without refine prefix.
     */
    public function getRawNameAttribute(): string
    {
        return $this->attributes['display_name'] ?? $this->databaseItem?->name ?? 'Unknown Item';
    }

    /**
     * Get the item_id from the linked DatabaseItem.
     */
    public function getItemIdAttribute(): ?int
    {
        return $this->databaseItem?->item_id;
    }

    /**
     * Get the item_name from the linked DatabaseItem.
     */
    public function getItemNameAttribute(): ?string
    {
        return $this->databaseItem?->name;
    }

    /**
     * Get the description from the linked DatabaseItem.
     */
    public function getDescriptionAttribute(): ?string
    {
        return $this->databaseItem?->description;
    }

    /**
     * Get the item_type from the linked DatabaseItem.
     */
    public function getItemTypeAttribute(): ?string
    {
        return $this->databaseItem?->item_type;
    }

    /**
     * Get the item_subtype from the linked DatabaseItem.
     */
    public function getItemSubtypeAttribute(): ?string
    {
        return $this->databaseItem?->item_subtype;
    }

    /**
     * Get the icon_path from the linked DatabaseItem.
     */
    public function getIconPathAttribute(): ?string
    {
        return $this->databaseItem?->icon_path;
    }

    /**
     * Get the collection_path from the linked DatabaseItem.
     */
    public function getCollectionPathAttribute(): ?string
    {
        return $this->databaseItem?->collection_path;
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
