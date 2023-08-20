<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $silver_count
 * @property int $gold_count
 * @property int $mithril_count
 * @property int $platinum_count
 * @property int $player_zeny
 * @property int $char_online
 * @property int $silver_zeny
 * @property int $gold_zeny
 * @property int $mithril_zeny
 * @property int $platinum_zeny
 * @property int $total_zeny
 * @property int $total_uber_cost
 * @property int $mithril_cost
 * @property int $platinum_cost
 * @property int $gold_cost
 * @property int $silver_cost
 * @property int $zeny_cost
 * @method static ServerZeny first()
 */
class ServerZeny extends Model
{
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
    protected $table = 'server_zeny';

    // Cast attributes to integer
    protected $casts = [
        'silver_count' => 'integer',
        'gold_count' => 'integer',
        'mithril_count' => 'integer',
        'platinum_count' => 'integer',
        'player_zeny' => 'integer',
        'char_online' => 'integer',
        'silver_zeny' => 'integer',
        'gold_zeny' => 'integer',
        'mithril_zeny' => 'integer',
        'platinum_zeny' => 'integer',
        'total_zeny' => 'integer',
        'total_uber_cost' => 'integer',
        'mithril_cost' => 'integer',
        'platinum_cost' => 'integer',
        'gold_cost' => 'integer',
        'silver_cost' => 'integer',
        'zeny_cost' => 'integer',
    ];

    // Default values for attributes
    protected $defaults = [
        'silver_count' => 0,
        'gold_count' => 0,
        'mithril_count' => 0,
        'platinum_count' => 0,
        'player_zeny' => 0,
        'char_online' => 0,
        'silver_zeny' => 0,
        'gold_zeny' => 0,
        'mithril_zeny' => 0,
        'platinum_zeny' => 0,
        'total_zeny' => 0,
        'total_uber_cost' => 0,
        'mithril_cost' => 0,
        'platinum_cost' => 0,
        'gold_cost' => 0,
        'silver_cost' => 0,
        'zeny_cost' => 0,
    ];

    /**
     * @var array
     */
    protected $fillable = [];

    // Since it's a view and typically read-only, disable Eloquent's timestamping
    public $timestamps = false;

    // If you don't plan on creating or updating records, disable the incrementing
    public $incrementing = false;
}
