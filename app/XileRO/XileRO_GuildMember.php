<?php

namespace App\XileRO;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $guild_id
 * @property int $char_id
 * @property int $exp
 * @property int $position
 * @property-read Guild $guild
 * @property-read Char $char
 */
class XileRO_GuildMember extends XileRO_Model
{
    protected $connection = 'xilero_main';

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
        return $this->belongsTo(XileRO_Guild::class, 'guild_id', 'guild_id');
    }

    public function char(): BelongsTo
    {
        return $this->belongsTo(XileRO_Char::class, 'char_id', 'char_id');
    }
}
