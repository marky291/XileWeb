<?php

namespace App\Filament\Resources\DonationRewardTierResource\Pages;

use App\Filament\Resources\DonationRewardTierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDonationRewardTier extends EditRecord
{
    protected static string $resource = DonationRewardTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $server = $data['server'] ?? 'xilero';
        $data['is_xilero'] = $server === 'xilero';
        $data['is_xileretro'] = $server === 'xileretro';
        unset($data['server']);

        return $data;
    }
}
