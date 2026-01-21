<?php

namespace App\Filament\Resources\ApiTokenResource\Widgets;

use Filament\Widgets\Widget;

class ApiDocumentationWidget extends Widget
{
    protected string $view = 'filament.resources.api-token-resource.widgets.api-documentation-widget';

    protected int|string|array $columnSpan = 'full';

    public function getBaseUrl(): string
    {
        return rtrim(config('app.url'), '/');
    }
}
