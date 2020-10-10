<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $guild_id
 * @property int $char_id
 * @property integer $exp
 * @property boolean $position
 */
class GuildMember extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guild_member';

    /**
     * @var array
     */
    protected $fillable = ['exp', 'position'];

}
