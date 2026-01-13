<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $guild_id
 * @property int $char_id
 * @property int $exp
 * @property int $position
 * @property-read Guild $guild
 * @property-read Char $char
 */
class GuildMember extends RagnarokModel
{
    protected $connection = 'main';

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
        return $this->belongsTo(Guild::class, 'guild_id', 'guild_id');
    }

    public function char(): BelongsTo
    {
        return $this->belongsTo(Char::class, 'char_id', 'char_id');
    }
}
