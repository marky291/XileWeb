<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $account_id
 * @property string $username
 * @property int $current_ubers
 * @property int $pending_ubers
 */
class DonationUber extends RagnarokModel
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'main';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donation_ubers';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'account_id',
        'username',
        'pending_ubers'
    ];

    public function login()
    {
        return $this->belongsTo(Login::class, 'account_id', 'account_id');
    }
}
