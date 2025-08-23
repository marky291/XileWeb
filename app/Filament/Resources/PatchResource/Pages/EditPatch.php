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
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure patches remain for XileRO (x9) client
        $data['client'] = 'x9';
        
        return $data;
    }
}
