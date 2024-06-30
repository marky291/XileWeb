<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(array $array)
 * @method static whereIn(string $string, $pluck)
 * @property
 */
class GameWoeEvent extends RagnarokModel
{
    use HasFactory;

    public const BREAK = 'break';
    public const STARTED = 'start';
    public const ENDED = 'end';
    public const ATTENDED = 'attend';
    public const KILLED = 'killed';

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
        'season',
        'event',
        'guild_id',
        'player',
        'created_at'
    ];

    public function getGuildNameFromMessageAttribute()
    {
        $pattern1 = '/The \[.*\] castle is currently held by the \[(.*?)\] guild./';
        $pattern2 = '/Castle \[.*\] has been captured by \[.*\] for Guild \[(.*?)\]/';
        $pattern3 = '/The \[.*\] castle has been conquered by the \[(.*?)\] guild./';
        $pattern4 = '/Guild \[(.*?)\] has attended with member count greater than size \[\d+\]/';
        $pattern5 = '/\[.*\] of the \[(.*?)\] guild has conquered the \[.*\] stronghold of Hljod!/';

        if (preg_match($pattern1, $this->message, $matches) ||
            preg_match($pattern2, $this->message, $matches) ||
            preg_match($pattern3, $this->message, $matches) ||
            preg_match($pattern4, $this->message, $matches) ||
            preg_match($pattern5, $this->message, $matches)
        ) {
            return empty($matches[1]) ? 'Unknown Guild' : $matches[1];
        }

        return 'Unknown Guild';
    }


    public function getPlayerNameFromMessageAttribute()
    {
        $pattern1 = '/by \[(.*?)\]/';
        $pattern2 = '/\[(.*?)\] of the \[.*\] guild has conquered the \[.*\] stronghold of Hljod!/';

        if (preg_match($pattern1, $this->message, $matches) ||
            preg_match($pattern2, $this->message, $matches)) {
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

    // Accessor method to extract attendence member count
    public function attendenceEventMemberCount()
    {
        $pattern = '/member count \[(\d+)\]/';

        if (preg_match($pattern, $this->message, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
