<?php

namespace App\Filament\Resources\ApiTokenResource\Widgets;

use App\Models\Item;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class ApiDocumentationWidget extends Widget
{
    protected string $view = 'filament.resources.api-token-resource.widgets.api-documentation-widget';

    protected int|string|array $columnSpan = 'full';

    public function getBaseUrl(): string
    {
        return rtrim(config('app.url'), '/');
    }

    /**
     * @return array<string>
     */
    public function getItemTypes(): array
    {
        return Cache::remember('api_doc_item_types', now()->addHour(), function () {
            return Item::query()
                ->select('type')
                ->distinct()
                ->whereNotNull('type')
                ->orderBy('type')
                ->pluck('type')
                ->toArray();
        });
    }

    /**
     * @return array<string>
     */
    public function getItemSubtypes(): array
    {
        return Cache::remember('api_doc_item_subtypes', now()->addHour(), function () {
            return Item::query()
                ->select('subtype')
                ->distinct()
                ->whereNotNull('subtype')
                ->orderBy('subtype')
                ->pluck('subtype')
                ->toArray();
        });
    }

    public function getAiPrompt(): string
    {
        $baseUrl = $this->getBaseUrl();
        $types = implode('|', $this->getItemTypes());
        $subtypes = implode('|', $this->getItemSubtypes());

        return <<<PROMPT
XileRO Item API
Base: {$baseUrl}/api/v1
Auth: Header "Authorization: Bearer {TOKEN}"

GET /items
Params: search, ids (csv), type (csv), subtype (csv), is_xileretro (bool), refineable (bool), min_slots (int), job, per_page (max 100), page
Example: /items?type=Weapon&subtype=Dagger&min_slots=1&per_page=50

GET /items/{id}
Returns single item by database ID

POST /items/bulk
Body: ids=1101,1102,1103 (max 100)
Returns multiple items by item_id

Types: {$types}
Subtypes: {$subtypes}

Response fields: id, item_id, aegis_name, name, description, type, subtype, weight, buy, sell, attack, defense, slots, refineable, jobs[], locations, flags, trade, script, equip_script, unequip_script, is_xileretro, view_id, resource_name, icon_url, collection_url
PROMPT;
    }
}
