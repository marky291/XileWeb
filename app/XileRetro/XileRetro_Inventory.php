<?php

namespace App\XileRetro;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $char_id
 * @property int $nameid
 * @property int $amount
 * @property int $equip
 * @property int $identify
 * @property int $refine
 * @property int $attribute
 * @property int $card0
 * @property int $card1
 * @property int $card2
 * @property int $card3
 * @property int $option_id0
 * @property int $option_val0
 * @property int $option_parm0
 * @property int $option_id1
 * @property int $option_val1
 * @property int $option_parm1
 * @property int $option_id2
 * @property int $option_val2
 * @property int $option_parm2
 * @property int $option_id3
 * @property int $option_val3
 * @property int $option_parm3
 * @property int $option_id4
 * @property int $option_val4
 * @property int $option_parm4
 * @property int $expire_time
 * @property int $favorite
 * @property int $bound
 * @property int $unique_id
 * @property int $equip_switch
 * @property int $enchantgrade
 */
class XileRetro_Inventory extends XileRetro_Model
{
    use HasFactory;

    protected $connection = 'xileretro_main';

    protected $table = 'inventory';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'char_id',
        'nameid',
        'amount',
        'equip',
        'identify',
        'refine',
        'attribute',
        'card0',
        'card1',
        'card2',
        'card3',
        'option_id0',
        'option_val0',
        'option_parm0',
        'option_id1',
        'option_val1',
        'option_parm1',
        'option_id2',
        'option_val2',
        'option_parm2',
        'option_id3',
        'option_val3',
        'option_parm3',
        'option_id4',
        'option_val4',
        'option_parm4',
        'expire_time',
        'favorite',
        'bound',
        'unique_id',
        'equip_switch',
        'enchantgrade',
    ];

    /**
     * @return BelongsTo<XileRetro_Char, $this>
     */
    public function character(): BelongsTo
    {
        return $this->belongsTo(XileRetro_Char::class, 'char_id', 'char_id');
    }

    public ?Item $cachedItem = null;

    public bool $itemLoaded = false;

    /**
     * Get the item from the main database.
     */
    public function getItemAttribute(): ?Item
    {
        if (! $this->itemLoaded) {
            $this->cachedItem = Item::where('item_id', $this->nameid)
                ->where('is_xileretro', true)
                ->first();
            $this->itemLoaded = true;
        }

        return $this->cachedItem;
    }
}
