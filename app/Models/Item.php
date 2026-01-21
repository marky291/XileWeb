<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

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
 * @property int $view_id
 * @property string|null $resource_name
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
        'view_id',
        'resource_name',
        'views',
        'data_patch_id',
        'sprite_patch_id',
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
     * Get the collection image URL for the item.
     */
    public function collection(): string
    {
        $path = ($this->is_xileretro ? 'retro' : 'xilero').'/collection/'.$this->item_id.'.png';

        return Storage::disk('public')->url($path);
    }

    /**
     * Get the icon image URL for the item.
     */
    public function icon(): string
    {
        $path = ($this->is_xileretro ? 'retro' : 'xilero').'/item/'.$this->item_id.'.png';

        return Storage::disk('public')->url($path);
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

    /**
     * Get the patch that last updated this item's data.
     */
    public function dataPatch(): BelongsTo
    {
        return $this->belongsTo(Patch::class, 'data_patch_id');
    }

    /**
     * Get the patch that last updated this item's sprites.
     */
    public function spritePatch(): BelongsTo
    {
        return $this->belongsTo(Patch::class, 'sprite_patch_id');
    }

    /**
     * Get the posts that feature this item.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)
            ->withPivot('sort_order')
            ->withTimestamps();
    }
}
