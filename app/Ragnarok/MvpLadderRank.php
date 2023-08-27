<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property int $day_kills
 * @property int $week_kills
 * @property int $month_kills
 * @property int $all_kills
 * @property int $char_id
 */
class MvpLadderRank extends RagnarokModel
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
    protected $table = 'mvp_ladder_rank';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'char_id';
}
