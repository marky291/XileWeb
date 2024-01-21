<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'char_id',
        'nameid',
        'amount',
        'equip',
        'identify',
        'refine',
        'attribute',
        'card0',
        'card1',
        'card2',
        'card3',
        'option_id0',
        'option_val0',
        'option_parm0',
        'option_id1',
        'option_val1',
        'option_parm1',
        'option_id2',
        'option_val2',
        'option_parm2',
        'option_id3',
        'option_val3',
        'option_parm3',
        'option_id4',
        'option_val4',
        'option_parm4',
        'expire_time',
        'favorite',
        'bound',
        'unique_id',
        'equip_switch',
        'enchantgrade',
    ];
}
