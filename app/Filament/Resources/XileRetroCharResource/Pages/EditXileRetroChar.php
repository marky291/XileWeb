<?php

namespace App\Filament\Resources\XileRetroCharResource\Pages;

use App\Filament\Resources\XileRetroCharResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditXileRetroChar extends EditRecord
{
    protected static string $resource = XileRetroCharResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
