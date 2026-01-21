<?php

namespace App\Filament\Resources\ApiTokenResource\Pages;

use App\Filament\Resources\ApiTokenResource;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ApiTokenSuccess extends Page
{
    protected static string $resource = ApiTokenResource::class;

    protected string $view = 'filament.resources.api-token-resource.pages.api-token-success';

    public string $plainTextToken = '';

    public string $tokenName = '';

    public function mount(): void
    {
        $this->plainTextToken = session('api_token_plain_text', '');
        $this->tokenName = session('api_token_name', '');

        if (empty($this->plainTextToken)) {
            $this->redirect(ApiTokenResource::getUrl('index'));
        }

        session()->forget(['api_token_plain_text', 'api_token_name']);
    }

    public function getTitle(): string|Htmlable
    {
        return 'API Token Created';
    }
}
