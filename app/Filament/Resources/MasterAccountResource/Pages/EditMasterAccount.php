<?php

namespace App\Filament\Resources\MasterAccountResource\Pages;

use App\Filament\Resources\MasterAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasterAccount extends EditRecord
{
    protected static string $resource = MasterAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
