<?php

namespace App\XileRetro;

use Database\Factories\XileRetro\XileRetro_DonationUbersFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * Legacy donation_ubers table from XileRetro game database.
 * Contains pending and current uber balances from the old system.
 *
 * @property int $id
 * @property int $account_id
 * @property string $username
 * @property int $current_ubers
 * @property int $pending_ubers
 * @property Carbon|null $updated_at
 * @property Carbon|null $created_at
 */
class XileRetro_DonationUbers extends XileRetro_Model
{
    use HasFactory;

    protected static function newFactory(): XileRetro_DonationUbersFactory
    {
        return XileRetro_DonationUbersFactory::new();
    }

    protected $connection = 'xileretro_main';

    protected $table = 'donation_ubers';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'username',
        'current_ubers',
        'pending_ubers',
    ];

    protected function casts(): array
    {
        return [
            'current_ubers' => 'integer',
            'pending_ubers' => 'integer',
        ];
    }

    /**
     * Get the total legacy ubers (current + pending).
     */
    public function getTotalUbersAttribute(): int
    {
        return ($this->current_ubers ?? 0) + ($this->pending_ubers ?? 0);
    }

    /**
     * Find donation ubers record by account ID.
     */
    public static function findByAccountId(int $accountId): ?self
    {
        return static::where('account_id', $accountId)->first();
    }

    /**
     * Get total legacy ubers for an account ID.
     * Returns 0 if no record exists.
     */
    public static function getTotalUbersForAccount(int $accountId): int
    {
        $record = static::findByAccountId($accountId);

        return $record?->total_ubers ?? 0;
    }
}
