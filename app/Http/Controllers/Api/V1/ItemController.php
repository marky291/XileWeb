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

        if ($request->has('ids')) {
            $ids = array_filter(array_map('intval', explode(',', $request->input('ids'))));
            if (! empty($ids)) {
                $query->whereIn('item_id', $ids);
            }
        }

        if ($request->has('type')) {
            $types = array_filter(explode(',', $request->input('type')));
            if (count($types) === 1) {
                $query->where('type', $types[0]);
            } elseif (count($types) > 1) {
                $query->whereIn('type', $types);
            }
        }

        if ($request->has('subtype')) {
            $subtypes = array_filter(explode(',', $request->input('subtype')));
            if (count($subtypes) === 1) {
                $query->where('subtype', $subtypes[0]);
            } elseif (count($subtypes) > 1) {
                $query->whereIn('subtype', $subtypes);
            }
        }

        if ($request->has('is_xileretro')) {
            $query->where('is_xileretro', filter_var($request->input('is_xileretro'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->boolean('refineable')) {
            $query->where('refineable', true);
        }

        if ($request->has('min_slots')) {
            $query->where('slots', '>=', (int) $request->input('min_slots'));
        }

        if ($request->has('job')) {
            $job = $request->input('job');
            $query->whereJsonContains('jobs', $job);
        }

        $perPage = min($request->input('per_page', 15), 100);

        return ItemResource::collection($query->paginate($perPage));
    }

    public function show(Item $item): ItemResource
    {
        return new ItemResource($item);
    }

    public function bulk(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = array_filter(array_map('intval', explode(',', $request->input('ids'))));

        if (empty($ids)) {
            return ItemResource::collection(collect());
        }

        $ids = array_slice($ids, 0, 100);

        $items = Item::whereIn('item_id', $ids)->get();

        return ItemResource::collection($items);
    }
}
