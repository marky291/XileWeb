<?php

namespace App\Filament\Resources\DonationLogResource\Pages;

use App\Filament\Resources\DonationLogResource;
use Filament\Actions\CreateAction;
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
