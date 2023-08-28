<?php

namespace App\Filament\Resources\CharResource\Pages;

use App\Filament\Resources\CharResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChar extends EditRecord
{
    protected static string $resource = CharResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }
}
