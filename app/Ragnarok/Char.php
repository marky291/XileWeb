<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $char_id
 * @property int $account_id
 * @property bool $char_num
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
 * @property bool $karma
 * @property int $manner
 * @property int $party_id
 * @property int $guild_id
 * @property int $pet_id
 * @property int $homun_id
 * @property int $elemental_id
 * @property bool $hair
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
 * @property bool $online
 * @property int $father
 * @property int $mother
 * @property int $child
 * @property int $fame
 * @property int $rename
 * @property int $delete_date
 * @property int $moves
 * @property int $unban_time
 * @property bool $font
 * @property int $uniqueitem_counter
 * @property string $sex
 * @property bool $hotkey_rowshift
 * @property bool $hotkey_rowshift2
 * @property int $clan_id
 * @property string $last_login
 * @property int $title_id
 * @property bool $show_equip
 */
class Char extends RagnarokModel
{
    use HasFactory;

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
    protected $table = 'char';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'char_id';

    /**
     * @var array
     */
    protected $fillable = ['account_id', 'char_num', 'name', 'class', 'base_level', 'job_level', 'base_exp', 'job_exp', 'zeny', 'str', 'agi', 'vit', 'int', 'dex', 'luk', 'max_hp', 'hp', 'max_sp', 'sp', 'status_point', 'skill_point', 'option', 'karma', 'manner', 'party_id', 'guild_id', 'pet_id', 'homun_id', 'elemental_id', 'hair', 'hair_color', 'clothes_color', 'body', 'weapon', 'shield', 'head_top', 'head_mid', 'head_bottom', 'robe', 'last_map', 'last_x', 'last_y', 'save_map', 'save_x', 'save_y', 'partner_id', 'online', 'father', 'mother', 'child', 'fame', 'rename', 'delete_date', 'moves', 'unban_time', 'font', 'uniqueitem_counter', 'sex', 'hotkey_rowshift', 'hotkey_rowshift2', 'clan_id', 'last_login', 'title_id', 'show_equip'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Scope a query to only include popular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnline($query) : Builder
    {
        return $query->where('online', 1);
    }

    public function login()
    {
        return $this->belongsTo(Login::class, 'account_id', 'account_id');
    }
}
