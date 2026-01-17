<?php

namespace App\Filament\Resources\XileRetroLoginResource\Pages;

use App\Filament\Resources\XileRetroLoginResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListXileRetroLogins extends ListRecords
{
    protected static string $resource = XileRetroLoginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
