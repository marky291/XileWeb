<?php

namespace App\Http\Resources;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Item
 */
class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'aegis_name' => $this->aegis_name,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'subtype' => $this->subtype,
            'weight' => $this->weight,
            'buy' => $this->buy,
            'sell' => $this->sell,
            'attack' => $this->attack,
            'defense' => $this->defense,
            'slots' => $this->slots,
            'refineable' => $this->refineable,
            'jobs' => $this->jobs,
            'locations' => $this->locations,
            'flags' => $this->flags,
            'trade' => $this->trade,
            'script' => $this->script,
            'equip_script' => $this->equip_script,
            'unequip_script' => $this->unequip_script,
            'is_xileretro' => $this->is_xileretro,
            'view_id' => $this->view_id,
            'resource_name' => $this->resource_name,
            'icon_url' => $this->resource->icon(),
            'collection_url' => $this->resource->collection(),
        ];
    }
}
