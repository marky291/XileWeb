<?php

namespace App\Filament\Resources\CharResource\Pages;

use App\Filament\Resources\CharResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChars extends ListRecords
{
    protected static string $resource = CharResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
