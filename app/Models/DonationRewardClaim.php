<?php

namespace App\Models;

use Database\Factories\DonationRewardClaimFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $donation_log_id
 * @property int $donation_reward_tier_id
 * @property int $item_id
 * @property int $quantity
 * @property int $refine_level
 * @property string $status
 * @property \Carbon\Carbon|null $claimed_at
 * @property int|null $claimed_account_id
 * @property string|null $claimed_char_name
 * @property bool $is_xilero
 * @property bool $is_xileretro
 * @property \Carbon\Carbon|null $expires_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read User $user
 * @property-read DonationRewardTier $tier
 * @property-read DonationLog|null $donationLog
 * @property-read Item $item
 * @property-read bool $is_pending
 * @property-read bool $is_claimed
 * @property-read bool $is_expired
 */
class DonationRewardClaim extends Model
{
    /** @use HasFactory<DonationRewardClaimFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CLAIMED = 'claimed';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'donation_log_id',
        'donation_reward_tier_id',
        'item_id',
        'quantity',
        'refine_level',
        'status',
        'claimed_at',
        'claimed_account_id',
        'claimed_char_name',
        'is_xilero',
        'is_xileretro',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'claimed_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_xilero' => 'boolean',
            'is_xileretro' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<DonationRewardTier, $this>
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(DonationRewardTier::class, 'donation_reward_tier_id');
    }

    /**
     * @return BelongsTo<DonationLog, $this>
     */
    public function donationLog(): BelongsTo
    {
        return $this->belongsTo(DonationLog::class);
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * @param  Builder<DonationRewardClaim>  $query
     * @return Builder<DonationRewardClaim>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * @param  Builder<DonationRewardClaim>  $query
     * @return Builder<DonationRewardClaim>
     */
    public function scopeClaimed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CLAIMED);
    }

    /**
     * @param  Builder<DonationRewardClaim>  $query
     * @return Builder<DonationRewardClaim>
     */
    public function scopeForServer(Builder $query, bool $isXileRetro): Builder
    {
        return $query->where($isXileRetro ? 'is_xileretro' : 'is_xilero', true);
    }

    /**
     * @param  Builder<DonationRewardClaim>  $query
     * @return Builder<DonationRewardClaim>
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getIsClaimedAttribute(): bool
    {
        return $this->status === self::STATUS_CLAIMED;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === self::STATUS_EXPIRED ||
            ($this->expires_at !== null && $this->expires_at->isPast());
    }

    public function markAsClaimed(int $accountId): void
    {
        $this->update([
            'status' => self::STATUS_CLAIMED,
            'claimed_at' => now(),
            'claimed_account_id' => $accountId,
        ]);
    }

    public function canBeClaimedBy(GameAccount $account): bool
    {
        if (! $this->is_pending) {
            return false;
        }

        if ($this->is_expired) {
            return false;
        }

        $isRetroAccount = $account->server === GameAccount::SERVER_XILERETRO;

        if ($isRetroAccount && ! $this->is_xileretro) {
            return false;
        }

        if (! $isRetroAccount && ! $this->is_xilero) {
            return false;
        }

        return true;
    }
}
