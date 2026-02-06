<?php

namespace App\Models;

use Database\Factories\DonationRewardTierFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $minimum_amount
 * @property bool $is_cumulative
 * @property string|null $claim_reset_period
 * @property string $trigger_type
 * @property bool $is_xilero
 * @property bool $is_xileretro
 * @property bool $enabled
 * @property int $display_order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Collection<int, Item> $items
 * @property-read Collection<int, DonationRewardClaim> $claims
 */
class DonationRewardTier extends Model
{
    /** @use HasFactory<DonationRewardTierFactory> */
    use HasFactory;

    public const TRIGGER_PER_DONATION = 'per_donation';

    public const TRIGGER_LIFETIME = 'lifetime';

    public const RESET_PER_DONATION = 'per_donation';

    public const RESET_DAILY = 'daily';

    public const RESET_WEEKLY = 'weekly';

    public const RESET_MONTHLY = 'monthly';

    public const RESET_YEARLY = 'yearly';

    public const RESET_PERIODS = [
        self::RESET_PER_DONATION => 'Per Donation (Unlimited)',
        self::RESET_DAILY => 'Daily',
        self::RESET_WEEKLY => 'Weekly',
        self::RESET_MONTHLY => 'Monthly',
        self::RESET_YEARLY => 'Yearly',
    ];

    protected $fillable = [
        'name',
        'description',
        'minimum_amount',
        'is_cumulative',
        'claim_reset_period',
        'trigger_type',
        'is_xilero',
        'is_xileretro',
        'enabled',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'minimum_amount' => 'decimal:2',
            'is_cumulative' => 'boolean',
            'is_xilero' => 'boolean',
            'is_xileretro' => 'boolean',
            'enabled' => 'boolean',
        ];
    }

    /**
     * @return BelongsToMany<Item, $this>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'donation_reward_tier_items')
            ->withPivot(['quantity', 'refine_level'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<DonationRewardClaim, $this>
     */
    public function claims(): HasMany
    {
        return $this->hasMany(DonationRewardClaim::class);
    }

    /**
     * HasMany relationship for Filament Repeater (pivot model).
     *
     * @return HasMany<DonationRewardTierItem, $this>
     */
    public function tierItems(): HasMany
    {
        return $this->hasMany(DonationRewardTierItem::class);
    }

    /**
     * @param  Builder<DonationRewardTier>  $query
     * @return Builder<DonationRewardTier>
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * @param  Builder<DonationRewardTier>  $query
     * @return Builder<DonationRewardTier>
     */
    public function scopeForServer(Builder $query, bool $isXileRetro): Builder
    {
        return $query->where($isXileRetro ? 'is_xileretro' : 'is_xilero', true);
    }

    /**
     * @param  Builder<DonationRewardTier>  $query
     * @return Builder<DonationRewardTier>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order')->orderBy('minimum_amount');
    }

    /**
     * @param  Builder<DonationRewardTier>  $query
     * @return Builder<DonationRewardTier>
     */
    public function scopePerDonation(Builder $query): Builder
    {
        return $query->where('trigger_type', self::TRIGGER_PER_DONATION);
    }

    /**
     * @param  Builder<DonationRewardTier>  $query
     * @return Builder<DonationRewardTier>
     */
    public function scopeLifetime(Builder $query): Builder
    {
        return $query->where('trigger_type', self::TRIGGER_LIFETIME);
    }

    public function isPerDonation(): bool
    {
        return $this->trigger_type === self::TRIGGER_PER_DONATION;
    }

    public function isLifetime(): bool
    {
        return $this->trigger_type === self::TRIGGER_LIFETIME;
    }

    public function isOneTime(): bool
    {
        return $this->claim_reset_period === null;
    }

    public function hasResetPeriod(): bool
    {
        return $this->claim_reset_period !== null;
    }

    public function isPerDonationReset(): bool
    {
        return $this->claim_reset_period === self::RESET_PER_DONATION;
    }

    /**
     * Get the start of the current reset period.
     */
    public function getCurrentPeriodStart(): ?\Carbon\Carbon
    {
        if ($this->claim_reset_period === null) {
            return null;
        }

        return match ($this->claim_reset_period) {
            self::RESET_PER_DONATION => null, // No period for per-donation
            self::RESET_DAILY => now()->startOfDay(),
            self::RESET_WEEKLY => now()->startOfWeek(),
            self::RESET_MONTHLY => now()->startOfMonth(),
            self::RESET_YEARLY => now()->startOfYear(),
            default => null,
        };
    }
}
