<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $char_id
 * @property int $account_id
 * @property boolean $char_num
 * @property string $name
 * @property integer $class
 * @property integer $base_level
 * @property integer $job_level
 * @property integer $base_exp
 * @property integer $job_exp
 * @property int $zeny
 * @property integer $str
 * @property integer $agi
 * @property integer $vit
 * @property integer $int
 * @property integer $dex
 * @property integer $luk
 * @property int $max_hp
 * @property int $hp
 * @property int $max_sp
 * @property int $sp
 * @property int $status_point
 * @property int $skill_point
 * @property int $option
 * @property boolean $karma
 * @property integer $manner
 * @property int $party_id
 * @property int $guild_id
 * @property int $pet_id
 * @property int $homun_id
 * @property int $elemental_id
 * @property boolean $hair
 * @property integer $hair_color
 * @property integer $clothes_color
 * @property integer $body
 * @property integer $weapon
 * @property integer $shield
 * @property integer $head_top
 * @property integer $head_mid
 * @property integer $head_bottom
 * @property integer $robe
 * @property string $last_map
 * @property integer $last_x
 * @property integer $last_y
 * @property string $save_map
 * @property integer $save_x
 * @property integer $save_y
 * @property int $partner_id
 * @property boolean $online
 * @property int $father
 * @property int $mother
 * @property int $child
 * @property int $fame
 * @property integer $rename
 * @property int $delete_date
 * @property int $moves
 * @property int $unban_time
 * @property boolean $font
 * @property int $uniqueitem_counter
 * @property string $sex
 * @property boolean $hotkey_rowshift
 * @property boolean $hotkey_rowshift2
 * @property int $clan_id
 * @property string $last_login
 * @property int $title_id
 * @property boolean $show_equip
 */
class Char extends Model
{
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
     * Scope a query to only include popular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnline($query) : Builder
    {
        return $query->where('online', 1);
    }
}
