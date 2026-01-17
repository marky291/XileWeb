<?php

namespace App\Filament\Resources\XileRetroCharResource\Pages;

use App\Filament\Resources\XileRetroCharResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListXileRetroChars extends ListRecords
{
    protected static string $resource = XileRetroCharResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
