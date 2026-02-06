<?php

namespace App\Models;

use Database\Factories\UberShopPurchaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property string $account_name
 * @property int|null $shop_item_id
 * @property int $item_id
 * @property string $item_name
 * @property int $refine_level
 * @property int $quantity
 * @property int $uber_cost
 * @property int $uber_balance_after
 * @property string $status
 * @property Carbon $purchased_at
 * @property Carbon|null $claimed_at
 * @property int|null $claimed_by_char_id
 * @property string|null $claimed_by_char_name
 * @property bool $is_xileretro
 * @property bool $is_bonus_reward
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read UberShopItem|null $shopItem
 * @property-read bool $is_pending
 * @property-read bool $is_claimed
 */
class UberShopPurchase extends Model
{
    /** @use HasFactory<UberShopPurchaseFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CLAIMED = 'claimed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'account_id',
        'account_name',
        'shop_item_id',
        'item_id',
        'item_name',
        'refine_level',
        'quantity',
        'uber_cost',
        'uber_balance_after',
        'status',
        'purchased_at',
        'claimed_at',
        'claimed_by_char_id',
        'claimed_by_char_name',
        'is_xileretro',
        'is_bonus_reward',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchased_at' => 'datetime',
            'claimed_at' => 'datetime',
            'is_xileretro' => 'boolean',
            'is_bonus_reward' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<UberShopItem, $this>
     */
    public function shopItem(): BelongsTo
    {
        return $this->belongsTo(UberShopItem::class, 'shop_item_id');
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getIsClaimedAttribute(): bool
    {
        return $this->status === self::STATUS_CLAIMED;
    }
}
