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
Params: search, ids (csv), type (csv), subtype (csv), is_xileretro (bool), refineable (bool), min_slots (int), job, per_page (max 100), page

GET /items/{id}

POST /items/bulk
Body: ids=1101,1102,1103 (max 100)

Types: {$types}
Subtypes: {$subtypes}

Fields: id, item_id, aegis_name, name, description, type, subtype, weight, buy, sell, attack, defense, slots, refineable, jobs[], is_xileretro, icon_url, collection_url
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
