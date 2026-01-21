<?php

namespace App\XileRO;

use App\Actions\CreateEmblemFromData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $guild_id
 * @property string $name
 * @property int $char_id
 * @property string $master
 * @property int $guild_lv
 * @property int $connect_member
 * @property int $max_member
 * @property int $average_lv
 * @property int $exp
 * @property int $next_exp
 * @property int $skill_point
 * @property string $mes1
 * @property string $mes2
 * @property int $emblem_len
 * @property int $emblem_id
 * @property string|null $emblem_data
 * @property string|null $last_master_change
 * @property int $created_time
 * @property-read string $emblem
 * @property-read Collection<int, GuildCastle> $castles
 * @property-read Collection<int, GuildMember> $members
 */
class XileRO_Guild extends XileRO_Model
{
    use HasFactory;

    const GM_TEAM = '"XileRO Team"';

    protected $connection = 'xilero_main';

    protected $table = 'guild';

    protected $primaryKey = 'guild_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'char_id',
        'master',
        'guild_lv',
        'connect_member',
        'max_member',
        'average_lv',
        'exp',
        'next_exp',
        'skill_point',
        'mes1',
        'mes2',
        'emblem_len',
        'emblem_id',
        'emblem_data',
        'last_master_change',
        'created_time',
    ];

    public function castles(): HasMany
    {
        return $this->hasMany(XileRO_GuildCastle::class, 'guild_id', 'guild_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(XileRO_GuildMember::class, 'guild_id', 'guild_id');
    }

    public function hasEmblem(): bool
    {
        return $this->emblem_data && $this->emblem_len && $this->guild_id;
    }

    public function getEmblemAttribute(): string
    {
        return Cache::remember("emblem.{$this->guild_id}", now()->addHour()->addMinutes(5), function () {
            $asset = "assets/emblems/{$this->guild_id}.png";

            $data = @gzuncompress(pack('H*', $this->emblem_data));

            imagepng(CreateEmblemFromData::run($data), $asset);

            return url($asset);
        });
    }
}
