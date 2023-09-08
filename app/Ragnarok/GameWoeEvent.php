<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(array $array)
 * @method static whereIn(string $string, $pluck)
 */
class GameWoeEvent extends RagnarokModel
{
    use HasFactory;

    public const BREAK = 'break';
    public const STARTED = 'start';
    public const ENDED = 'end';
    public const ATTENDED = 'attend';

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
    protected $table = 'game_woe_events';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $casts = [
        'discord_sent' => 'boolean',
        'processed' => 'boolean',
    ];

    protected $fillable = [
        'discord_sent',
        'processed',
        'message',
        'castle',
        'edition',
        'event',
        'guild_id',
        'player'
    ];

    public function getGuildNameFromMessageAttribute()
    {
        $pattern = '/Guild \[(.*?)\]/';

        if (preg_match($pattern, $this->message, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getPlayerNameFromMessageAttribute()
    {
        $pattern = '/by \[(.*?)\]/';

        if (preg_match($pattern, $this->message, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getCastleNameFromMessageAttribute()
    {
        $pattern = '/Castle \[(.*?)\]/';

        if (preg_match($pattern, $this->message, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
