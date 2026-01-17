<?php

namespace App\XileRetro;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $guild_id
 * @property int $char_id
 * @property int $exp
 * @property int $position
 * @property-read XileRetro_Guild $guild
 * @property-read XileRetro_Char $char
 */
class XileRetro_GuildMember extends XileRetro_Model
{
    protected $connection = 'xileretro_main';

    protected $table = 'guild_member';

    protected $primaryKey = ['guild_id', 'char_id'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'guild_id',
        'char_id',
        'exp',
        'position',
    ];

    public function guild(): BelongsTo
    {
        return $this->belongsTo(XileRetro_Guild::class, 'guild_id', 'guild_id');
    }

    public function char(): BelongsTo
    {
        return $this->belongsTo(XileRetro_Char::class, 'char_id', 'char_id');
    }
}
