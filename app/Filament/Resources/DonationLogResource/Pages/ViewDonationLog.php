<?php

namespace App\Filament\Resources\DonationLogResource\Pages;

use App\Filament\Resources\DonationLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDonationLog extends ViewRecord
{
    protected static string $resource = DonationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
