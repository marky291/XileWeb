<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $item_id
 * @property string $aegis_name
 * @property string $name
 * @property string|null $description
 * @property string $type
 * @property string|null $subtype
 * @property int $weight
 * @property int $buy
 * @property int $sell
 * @property int $attack
 * @property int $defense
 * @property int $slots
 * @property bool $refineable
 * @property array|null $jobs
 * @property array|null $locations
 * @property array|null $flags
 * @property array|null $trade
 * @property string|null $script
 * @property string|null $equip_script
 * @property string|null $unequip_script
 * @property bool $is_xileretro
 * @property int $views
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'aegis_name',
        'name',
        'description',
        'type',
        'subtype',
        'weight',
        'buy',
        'sell',
        'attack',
        'defense',
        'slots',
        'refineable',
        'jobs',
        'locations',
        'flags',
        'trade',
        'script',
        'equip_script',
        'unequip_script',
        'is_xileretro',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'refineable' => 'boolean',
            'jobs' => 'array',
            'locations' => 'array',
            'flags' => 'array',
            'trade' => 'array',
            'is_xileretro' => 'boolean',
        ];
    }

    /**
     * Get the collection URL for the item.
     *
     * This uses public/assets/{is_xileretro}/item_collection/{item_id}.png
     */
    public function collection(): string
    {
        return '/assets/'.($this->is_xileretro ? 'retro' : 'xilero').'/item_collection/'.$this->item_id.'.png';
    }

    /**
     * Get the icon URL for the item.
     *
     * This uses public/assets/{is_xileretro}/item_icons/{item_id}.png
     */
    public function icon(): string
    {
        return '/assets/'.($this->is_xileretro ? 'retro' : 'xilero').'/item_icons/'.$this->item_id.'.png';
    }

    /**
     * Get the formatted description with color codes and newlines.
     *
     * Descriptions may contain HTML span tags for colors.
     */
    public function formattedDescription(): ?string
    {
        if (! $this->description) {
            return null;
        }

        // Allow only span tags with style attribute for colors
        $description = strip_tags($this->description, '<span>');

        // Convert newlines to <br> tags
        $description = nl2br($description);

        return $description;
    }
}
