<?php

namespace App\Filament\Resources\UberShopCategoryResource\Pages;

use App\Filament\Resources\UberShopCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUberShopCategories extends ListRecords
{
    protected static string $resource = UberShopCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
