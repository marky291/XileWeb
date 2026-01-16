<?php

namespace App\Filament\Resources\MasterAccountResource\Pages;

use App\Filament\Resources\MasterAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMasterAccounts extends ListRecords
{
    protected static string $resource = MasterAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
