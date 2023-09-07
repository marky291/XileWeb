<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int|mixed $guild_score
 * @property \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|mixed $season
 */
class GameWoeScore extends RagnarokModel
{
    use HasFactory;

    public const POINTS_LONGEST_HELD = 3;
    public const POINTS_FIRST_BREAK = 2;
    public const POINTS_ATTENDED = 1;

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
    protected $table = 'game_woe_scores';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'guild_id',
        'guild_score'
    ];

    public function Guild()
    {
        return $this->belongsTo(Guild::class, 'guild_id', 'guild_id');
    }
}
