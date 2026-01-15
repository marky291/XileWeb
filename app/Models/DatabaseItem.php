<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $item_id
 * @property string $name
 * @property string|null $description
 * @property string|null $aegis_name
 * @property string|null $item_type
 * @property string|null $item_subtype
 * @property int $slots
 * @property int $weight
 * @property int $attack
 * @property int $defense
 * @property int $equip_level_min
 * @property int $weapon_level
 * @property array|null $equip_locations
 * @property array|null $jobs
 * @property int $buy_price
 * @property int $sell_price
 * @property string|null $icon_path
 * @property string|null $collection_path
 * @property string|null $client_icon
 * @property string|null $client_collection
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class DatabaseItem extends Model
{
    /** @use HasFactory<\Database\Factories\DatabaseItemFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'name',
        'description',
        'aegis_name',
        'item_type',
        'item_subtype',
        'slots',
        'weight',
        'attack',
        'defense',
        'equip_level_min',
        'weapon_level',
        'equip_locations',
        'jobs',
        'buy_price',
        'sell_price',
        'icon_path',
        'collection_path',
        'client_icon',
        'client_collection',
    ];

    protected function casts(): array
    {
        return [
            'equip_locations' => 'array',
            'jobs' => 'array',
        ];
    }
}
