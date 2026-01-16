<?php

namespace App\Filament\Resources\XileRetroInventoryResource\Pages;

use App\Filament\Resources\XileRetroInventoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditXileRetroInventory extends EditRecord
{
    protected static string $resource = XileRetroInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
