<?php

namespace App\Models;

use Database\Factories\NpcFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $npc_id
 * @property string $name
 * @property string $sprite
 */
class Npc extends Model
{
    /** @use HasFactory<NpcFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'npc_id',
        'name',
        'sprite',
    ];

    /**
     * Get the image URL for the NPC.
     */
    public function image(): string
    {
        return Storage::disk('public')->url('npc/'.$this->npc_id.'.png');
    }
}
