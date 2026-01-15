<?php

namespace App\XileRetro;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $char_id
 * @property int $account_id
 * @property int $char_num
 * @property string $name
 * @property int $class
 * @property int $base_level
 * @property int $job_level
 * @property int $base_exp
 * @property int $job_exp
 * @property int $zeny
 * @property int $str
 * @property int $agi
 * @property int $vit
 * @property int $int
 * @property int $dex
 * @property int $luk
 * @property int $max_hp
 * @property int $hp
 * @property int $max_sp
 * @property int $sp
 * @property int $status_point
 * @property int $skill_point
 * @property int $option
 * @property int $karma
 * @property int $manner
 * @property int $party_id
 * @property int $guild_id
 * @property int $pet_id
 * @property int $homun_id
 * @property int $elemental_id
 * @property int $hair
 * @property int $hair_color
 * @property int $clothes_color
 * @property int $body
 * @property int $weapon
 * @property int $shield
 * @property int $head_top
 * @property int $head_mid
 * @property int $head_bottom
 * @property int $robe
 * @property string $last_map
 * @property int $last_x
 * @property int $last_y
 * @property string $save_map
 * @property int $save_x
 * @property int $save_y
 * @property int $partner_id
 * @property int $online
 * @property int $father
 * @property int $mother
 * @property int $child
 * @property int $fame
 * @property int $rename
 * @property int $delete_date
 * @property int $moves
 * @property int $unban_time
 * @property int $font
 * @property int $uniqueitem_counter
 * @property string $sex
 * @property int $hotkey_rowshift
 * @property int $hotkey_rowshift2
 * @property int $clan_id
 * @property string|null $last_login
 * @property int $title_id
 * @property int $show_equip
 * @property-read string $class_name
 * @property-read XileRetro_Login $login
 * @property-read XileRetro_Guild|null $guild
 */
class XileRetro_Char extends XileRetro_Model
{
    use HasFactory;

    protected $connection = 'xileretro_main';

    protected $table = 'char';

    protected $primaryKey = 'char_id';

    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'char_num',
        'name',
        'class',
        'base_level',
        'job_level',
        'base_exp',
        'job_exp',
        'zeny',
        'str',
        'agi',
        'vit',
        'int',
        'dex',
        'luk',
        'max_hp',
        'hp',
        'max_sp',
        'sp',
        'status_point',
        'skill_point',
        'option',
        'karma',
        'manner',
        'party_id',
        'guild_id',
        'pet_id',
        'homun_id',
        'elemental_id',
        'hair',
        'hair_color',
        'clothes_color',
        'body',
        'weapon',
        'shield',
        'head_top',
        'head_mid',
        'head_bottom',
        'robe',
        'last_map',
        'last_x',
        'last_y',
        'save_map',
        'save_x',
        'save_y',
        'partner_id',
        'online',
        'father',
        'mother',
        'child',
        'fame',
        'rename',
        'delete_date',
        'moves',
        'unban_time',
        'font',
        'uniqueitem_counter',
        'sex',
        'hotkey_rowshift',
        'hotkey_rowshift2',
        'clan_id',
        'last_login',
        'title_id',
        'show_equip',
    ];

    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('online', 1);
    }

    public function login(): BelongsTo
    {
        return $this->belongsTo(XileRetro_Login::class, 'account_id', 'account_id');
    }

    public function guild(): BelongsTo
    {
        return $this->belongsTo(XileRetro_Guild::class, 'guild_id', 'guild_id');
    }

    public function getClassNameAttribute(): string
    {
        return config('jobclasses')[$this->class] ?? 'Unknown';
    }
}
