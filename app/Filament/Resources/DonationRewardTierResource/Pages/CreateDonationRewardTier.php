<?php

namespace App\Filament\Resources\DonationRewardTierResource\Pages;

use App\Filament\Resources\DonationRewardTierResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDonationRewardTier extends CreateRecord
{
    protected static string $resource = DonationRewardTierResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $server = $data['server'] ?? 'xilero';
        $data['is_xilero'] = $server === 'xilero';
        $data['is_xileretro'] = $server === 'xileretro';
        unset($data['server']);

        return $data;
    }
}
