<?php

namespace App\Filament\Resources\AtcommandResource\Pages;

use App\Filament\Resources\AtcommandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAtcommands extends ListRecords
{
    protected static string $resource = AtcommandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
