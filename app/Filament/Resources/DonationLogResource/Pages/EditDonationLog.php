<?php

namespace App\Filament\Resources\DonationLogResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\DonationLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDonationLog extends EditRecord
{
    protected static string $resource = DonationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
