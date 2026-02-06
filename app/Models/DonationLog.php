<?php

namespace App\Models;

use Database\Factories\DonationLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationLog extends Model
{
    /** @use HasFactory<DonationLogFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'amount',
        'payment_method',
        'base_ubers',
        'bonus_ubers',
        'total_ubers',
        'notes',
        'reverted_at',
        'reverted_by',
        'ubers_recovered',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'base_ubers' => 'integer',
            'bonus_ubers' => 'integer',
            'total_ubers' => 'integer',
            'reverted_at' => 'datetime',
            'ubers_recovered' => 'integer',
        ];
    }

    public function isReverted(): bool
    {
        return $this->reverted_at !== null;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function revertedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reverted_by');
    }

    public function paymentMethodName(): string
    {
        return config("donation.payment_methods.{$this->payment_method}.name", $this->payment_method);
    }

    /**
     * @return HasMany<DonationRewardClaim, $this>
     */
    public function rewardClaims(): HasMany
    {
        return $this->hasMany(DonationRewardClaim::class);
    }
}
