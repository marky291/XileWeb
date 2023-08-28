<?php

namespace App\Filament\Resources\DonationUberResource\Pages;

use App\Filament\Resources\DonationUberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDonationUber extends EditRecord
{
    protected static string $resource = DonationUberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }
}
