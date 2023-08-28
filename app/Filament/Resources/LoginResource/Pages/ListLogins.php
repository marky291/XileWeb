<?php

namespace App\Filament\Resources\LoginResource\Pages;

use App\Filament\Resources\LoginResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogins extends ListRecords
{
    protected static string $resource = LoginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
