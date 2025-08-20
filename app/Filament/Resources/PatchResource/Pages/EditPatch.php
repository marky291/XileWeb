<?php

namespace App\Filament\Resources\PatchResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\PatchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPatch extends EditRecord
{
    protected static string $resource = PatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
