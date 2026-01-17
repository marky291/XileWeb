<?php

namespace App\Filament\Resources\DonationLogResource\Pages;

use App\Filament\Resources\DonationLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDonationLog extends ViewRecord
{
    protected static string $resource = DonationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
