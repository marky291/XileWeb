<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $guild_id
 * @property int $char_id
 * @property int $exp
 * @property bool $position
 */
class GuildMember extends Model
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
    protected $table = 'guild_member';

    /**
     * @var array
     */
    protected $fillable = ['exp', 'position'];
}
