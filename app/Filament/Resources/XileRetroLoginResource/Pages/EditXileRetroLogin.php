<?php

namespace App\Filament\Resources\XileRetroLoginResource\Pages;

use App\Filament\Resources\XileRetroLoginResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditXileRetroLogin extends EditRecord
{
    protected static string $resource = XileRetroLoginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
