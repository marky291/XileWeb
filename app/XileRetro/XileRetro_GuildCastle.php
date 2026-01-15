<?php

namespace App\XileRetro;

use Database\Factories\XileRetro\XileRetro_GuildCastleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @property-read string $name
 * @property-read XileRetro_Guild|null $guild
 */
class XileRetro_GuildCastle extends XileRetro_Model
{
    /** @use HasFactory<XileRetro_GuildCastleFactory> */
    use HasFactory;

    protected static function newFactory(): XileRetro_GuildCastleFactory
    {
        return XileRetro_GuildCastleFactory::new();
    }

    const KRIEMHILD = 'Kriemhild';

    const SWANHILD = 'Swanhild';

    const SKOEGUL = 'Skoegul';

    const GONDUL = 'Gondul';

    const FADHRINGH = 'Fadhringh';

    const HLJOD = 'Hljod';

    const CYR = 'Cyr';

    protected $connection = 'xileretro_main';

    protected $table = 'guild_castle';

    protected $primaryKey = 'castle_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'castle_id',
        'guild_id',
        'economy',
        'defense',
        'triggerE',
        'triggerD',
        'nextTime',
        'payTime',
        'createTime',
        'visibleC',
        'visibleG0',
        'visibleG1',
        'visibleG2',
        'visibleG3',
        'visibleG4',
        'visibleG5',
        'visibleG6',
        'visibleG7',
    ];

    public function guild(): BelongsTo
    {
        return $this->belongsTo(XileRetro_Guild::class, 'guild_id', 'guild_id');
    }

    public function scopeProntera(Builder $query): Builder
    {
        return $query->whereIn('castle_id', [15, 16, 17, 18, 19]);
    }

    public function getNameAttribute(): string
    {
        return CastleName::getNameById($this->castle_id) ?? 'Unknown Castle';
    }

    public static function getCastleNameById(int $id): ?string
    {
        return CastleName::getNameById($id);
    }

    public static function getCastleIdByName(string $name): ?int
    {
        return CastleName::getIdByName($name);
    }
}

enum CastleName: int
{
    case Neuschwanstein = 0;
    case Hohenschwangau = 1;
    case Nuernberg = 2;
    case Wuerzburg = 3;
    case Rothenburg = 4;
    case Repherion = 5;
    case Eeyolbriggar = 6;
    case Yesnelph = 7;
    case Bergel = 8;
    case Mersetzdeitz = 9;
    case BrightArbor = 10;
    case ScarletPalace = 11;
    case HolyShadow = 12;
    case SacredAltar = 13;
    case BambooGroveHill = 14;
    case Kriemhild = 15;
    case Swanhild = 16;
    case Fadhgridh = 17;
    case Skoegul = 18;
    case Gondul = 19;
    case NoviceCastle1 = 20;
    case NoviceCastle2 = 21;
    case NoviceCastle3 = 22;
    case NoviceCastle4 = 23;
    case GuildVsGuild = 24;
    case Himinn = 25;
    case Andlangr = 26;
    case Viblainn = 27;
    case Hljod = 28;
    case Skidbladnir = 29;
    case Mardol = 30;
    case Cyr = 31;
    case Horn = 32;
    case Gefn = 33;
    case Bandis = 34;

    public function getName(): string
    {
        return match ($this) {
            self::Neuschwanstein => 'Neuschwanstein',
            self::Hohenschwangau => 'Hohenschwangau',
            self::Nuernberg => 'Nuernberg',
            self::Wuerzburg => 'Wuerzburg',
            self::Rothenburg => 'Rothenburg',
            self::Repherion => 'Repherion',
            self::Eeyolbriggar => 'Eeyolbriggar',
            self::Yesnelph => 'Yesnelph',
            self::Bergel => 'Bergel',
            self::Mersetzdeitz => 'Mersetzdeitz',
            self::BrightArbor => 'Bright Arbor',
            self::ScarletPalace => 'Scarlet Palace',
            self::HolyShadow => 'Holy Shadow',
            self::SacredAltar => 'Sacred Altar',
            self::BambooGroveHill => 'Bamboo Grove Hill',
            self::Kriemhild => 'Kriemhild',
            self::Swanhild => 'Swanhild',
            self::Fadhgridh => 'Fadhgridh',
            self::Skoegul => 'Skoegul',
            self::Gondul => 'Gondul',
            self::Himinn => 'Himinn',
            self::Andlangr => 'Andlangr',
            self::Viblainn => 'Viblainn',
            self::Hljod => 'Hljod',
            self::Skidbladnir => 'Skidbladnir',
            self::Mardol => 'Mardol',
            self::Cyr => 'Cyr',
            self::Horn => 'Horn',
            self::Gefn => 'Gefn',
            self::Bandis => 'Bandis',
            default => 'Unknown Castle'
        };
    }

    public function getEdition(): int
    {
        return match ($this) {
            self::Neuschwanstein, self::Hohenschwangau, self::Nuernberg, self::Wuerzburg,
            self::Rothenburg, self::Repherion, self::Eeyolbriggar, self::Yesnelph, self::Bergel,
            self::Mersetzdeitz, self::BrightArbor, self::ScarletPalace, self::HolyShadow, self::SacredAltar,
            self::BambooGroveHill, self::Kriemhild, self::Swanhild, self::Fadhgridh, self::Skoegul,
            self::Gondul => 1,
            self::Himinn, self::Andlangr, self::Viblainn, self::Hljod, self::Skidbladnir,
            self::Mardol, self::Cyr, self::Horn, self::Gefn, self::Bandis => 2,
            default => 0
        };
    }

    public static function getNameById(int $id): ?string
    {
        return self::tryFrom($id)?->getName();
    }

    public static function getIdByName(string $name): ?int
    {
        foreach (self::cases() as $case) {
            if ($case->getName() === $name) {
                return $case->value;
            }
        }

        return null;
    }
}
