<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NpcResource;
use App\Models\Npc;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NpcController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Npc::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('npc_id', $search);
            });
        }

        if ($request->has('ids')) {
            $ids = array_filter(array_map('intval', explode(',', $request->input('ids'))));
            if (! empty($ids)) {
                $query->whereIn('npc_id', $ids);
            }
        }

        if ($request->has('sprite')) {
            $sprite = $request->input('sprite');
            $query->where('sprite', 'like', "%{$sprite}%");
        }

        $perPage = min($request->input('per_page', 15), 100);

        return NpcResource::collection($query->paginate($perPage));
    }

    public function show(Npc $npc): NpcResource
    {
        return new NpcResource($npc);
    }

    public function bulk(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = array_filter(array_map('intval', explode(',', $request->input('ids'))));

        if (empty($ids)) {
            return NpcResource::collection(collect());
        }

        $ids = array_slice($ids, 0, 100);

        $npcs = Npc::whereIn('npc_id', $ids)->get();

        return NpcResource::collection($npcs);
    }
}
