<?php

namespace App\Http\Resources;

use App\Models\Npc;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Npc
 */
class NpcResource extends JsonResource
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
            'npc_id' => $this->npc_id,
            'name' => $this->name,
            'sprite' => $this->sprite,
            'image_url' => $this->resource->image(),
        ];
    }
}
