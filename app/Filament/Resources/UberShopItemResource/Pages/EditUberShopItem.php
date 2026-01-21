<?php

namespace App\Filament\Resources\UberShopItemResource\Pages;

use App\Filament\Resources\UberShopItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUberShopItem extends EditRecord
{
    protected static string $resource = UberShopItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
