<?php

namespace App\Filament\Resources\XileRetroInventoryResource\Pages;

use App\Filament\Resources\XileRetroInventoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListXileRetroInventories extends ListRecords
{
    protected static string $resource = XileRetroInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
