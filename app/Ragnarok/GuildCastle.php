<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $castle_id
 * @property int $guild_id
 * @property int $economy
 * @property int $defense
 * @property int $triggerE
 * @property int $triggerD
 * @property int $nextTime
 * @property int $payTime
 * @property int $createTime
 * @property int $visibleC
 * @property int $visibleG0
 * @property int $visibleG1
 * @property int $visibleG2
 * @property int $visibleG3
 * @property int $visibleG4
 * @property int $visibleG5
 * @property int $visibleG6
 * @property int $visibleG7
 * @property string $name
 *
 * @property GuildCastle kriemhild
 */
class GuildCastle extends RagnarokModel
{

    const KRIEMHILD = 'Kriemhild';
    const SWANHILD = 'Swanhild';
    const SKOEGUL = 'Skoegul';
    const GONDUL = 'Gondul';
    const FADHRINGH = 'Fadhringh';
    const HLJOD = 'Hljod';
    const CYR = 'Cyr';

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
    protected $table = 'guild_castle';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'castle_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['guild_id', 'economy', 'defense', 'triggerE', 'triggerD', 'nextTime', 'payTime', 'createTime', 'visibleC', 'visibleG0', 'visibleG1', 'visibleG2', 'visibleG3', 'visibleG4', 'visibleG5', 'visibleG6', 'visibleG7'];

    /**
     * Each castle belongs to one guild.
     */
    public function Guild()
    {
        return $this->belongsTo(Guild::class, 'guild_id', 'guild_id');
    }

    public function scopeProntera(Builder $query)
    {
        return $query->whereIn('castle_id', [15, 16, 17, 18, 19]);
    }

    public function getNameAttribute()
    {
        switch ($this->castle_id) {
            case 0: return 'Neuschwanstein';
            case 1: return 'Hohenschwangau';
            case 2: return 'Nuenberg';
            case 3: return 'Wuerzburg';
            case 4: return 'Rothenburg';
            case 5: return 'Repherion';
            case 6: return 'Eeyolbriggar';
            case 7: return 'Yesnelph';
            case 8: return 'Bergel';
            case 9: return 'Mersetzdeitz';
            case 10: return 'Bright Arbor';
            case 11: return 'Scarlet Palace';
            case 12: return 'Holy Shadow';
            case 13: return 'Sacred Altar';
            case 14: return 'Bamboo Grove Hill';
            case 15: return 'Kriemhild';
            case 16: return 'Swanhild';
            case 17: return 'Fadhgridh';
            case 18: return 'Skoegul';
            case 19: return 'Gondul';
            case 20: return 'Novice Castle 1';
            case 21: return 'Novice Castle 2';
            case 22: return 'Novice Castle 3';
            case 23: return 'Novice Castle 4';
            case 24: return 'Guild vs Guild';
            case 25: return 'Himinn';
            case 26: return 'Andlangr';
            case 27: return 'Viblainn';
            case 28: return 'Hljod';
            case 29: return 'Skidbladnir';
            case 30: return 'Mardol';
            case 31: return 'Cyr';
            case 32: return 'Horn';
            case 33: return 'Gefn';
            case 34: return 'Bandis';
        }
    }
}
