<?php

namespace App\Filament\Resources\PatchResource\Pages;

use App\Filament\Resources\PatchResource;
use Filament\Actions\DeleteAction;
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Allow user to change client selection
        return $data;
    }
}
