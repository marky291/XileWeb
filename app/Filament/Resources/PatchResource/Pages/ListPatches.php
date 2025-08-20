<?php

namespace App\Filament\Resources\PatchResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPatches extends ListRecords
{
    protected static string $resource = PatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
