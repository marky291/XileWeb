<?php

namespace App\Filament\Resources\ApiTokenResource\Pages;

use App\Filament\Resources\ApiTokenResource;
use App\Models\Item;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Cache;

class ViewApiToken extends ViewRecord
{
    protected static string $resource = ApiTokenResource::class;

    protected string $view = 'filament.resources.api-token-resource.pages.view-api-token';

    public ?string $newToken = null;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->newToken = session('api_token_created');
        session()->forget('api_token_created');
    }

    public function getAiPrompt(string $token = '{TOKEN}'): string
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $types = implode('|', $this->getItemTypes());
        $subtypes = implode('|', $this->getItemSubtypes());

        return <<<PROMPT
XileRO Item API
Base: {$baseUrl}/api/v1
Auth: Header "Authorization: Bearer {$token}"

GET /items
Params:
- search: text search on name/aegis_name, or exact item_id
- ids: csv item_ids (1101,1102,1103)
- type: csv ({$types})
- subtype: csv ({$subtypes})
- is_xileretro: bool (true=XileRetro, false=XileRO)
- refineable: bool
- min_slots: int
- job: class name (Knight, Wizard, etc)
- per_page: int (max 100)
- page: int

GET /items/{id} - by database id

POST /items/bulk - body: ids=1101,1102 (max 100)

Examples:
/items?search=Excalibur
/items?type=Weapon&subtype=Dagger&min_slots=1
/items?type=Card&search=Hydra
/items?job=Knight&type=Weapon

Fields:
- id: database ID (use for /items/{id})
- item_id: game ID (use for bulk lookup)
- aegis_name: internal script name
- name: display name
- description: item description with effects
- type/subtype: classification
- weight: item weight (1 = 0.1 weight)
- buy/sell: NPC prices in zeny
- attack/defense: base stats
- slots: card slot count
- refineable: can be upgraded
- jobs[]: equippable classes
- is_xileretro: true=XileRetro server, false=XileRO server
- icon_url: small icon image (32x32)
- collection_url: large display image
PROMPT;
    }

    /**
     * @return array<string>
     */
    protected function getItemTypes(): array
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
    protected function getItemSubtypes(): array
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
}
