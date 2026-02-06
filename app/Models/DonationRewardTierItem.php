<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $donation_reward_tier_id
 * @property int $item_id
 * @property int $quantity
 * @property int $refine_level
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read DonationRewardTier $tier
 * @property-read Item $item
 */
class DonationRewardTierItem extends Model
{
    protected $fillable = [
        'donation_reward_tier_id',
        'item_id',
        'quantity',
        'refine_level',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'refine_level' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<DonationRewardTier, $this>
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(DonationRewardTier::class, 'donation_reward_tier_id');
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
