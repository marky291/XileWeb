<?php

namespace App\Filament\Resources\DonationRewardTierResource\Pages;

use App\Filament\Resources\DonationRewardTierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDonationRewardTiers extends ListRecords
{
    protected static string $resource = DonationRewardTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
