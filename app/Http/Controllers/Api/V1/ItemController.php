<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ItemController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Item::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('aegis_name', 'like', "%{$search}%")
                    ->orWhere('item_id', $search);
            });
        }

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('is_xileretro')) {
            $query->where('is_xileretro', filter_var($request->input('is_xileretro'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = min($request->input('per_page', 15), 100);

        return ItemResource::collection($query->paginate($perPage));
    }

    public function show(Item $item): ItemResource
    {
        return new ItemResource($item);
    }
}
