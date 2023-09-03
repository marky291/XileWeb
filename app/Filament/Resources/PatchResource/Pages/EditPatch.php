<?php

namespace App\Filament\Resources\PatchResource\Pages;

use App\Filament\Resources\PatchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPatch extends EditRecord
{
    protected static string $resource = PatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
