<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $account_id
 * @property string $username
 * @property int|null $current_ubers
 * @property int|null $pending_ubers
 * @property string|null $updated_at
 * @property string|null $created_at
 * @property-read Login $login
 */
class DonationUber extends RagnarokModel
{
    use HasFactory;

    protected $connection = 'main';

    protected $table = 'donation_ubers';

    protected $primaryKey = 'id';

    protected $fillable = [
        'account_id',
        'username',
        'current_ubers',
        'pending_ubers',
    ];

    public function login(): BelongsTo
    {
        return $this->belongsTo(Login::class, 'account_id', 'account_id');
    }
}
