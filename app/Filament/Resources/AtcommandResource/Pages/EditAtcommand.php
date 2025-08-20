<?php

namespace App\Filament\Resources\AtcommandResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\AtcommandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAtcommand extends EditRecord
{
    protected static string $resource = AtcommandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
