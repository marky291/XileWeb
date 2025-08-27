<?php

namespace App\Filament\Resources\DonationLogResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\DonationLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDonationLogs extends ListRecords
{
    protected static string $resource = DonationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
