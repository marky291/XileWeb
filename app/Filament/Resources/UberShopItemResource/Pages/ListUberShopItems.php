<?php

namespace App\Filament\Resources\UberShopItemResource\Pages;

use App\Filament\Resources\UberShopItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUberShopItems extends ListRecords
{
    protected static string $resource = UberShopItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
