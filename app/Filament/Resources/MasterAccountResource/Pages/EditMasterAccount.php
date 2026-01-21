<?php

namespace App\Filament\Resources\MasterAccountResource\Pages;

use App\Filament\Resources\MasterAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMasterAccount extends EditRecord
{
    protected static string $resource = MasterAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Handle guarded fields (is_admin, max_game_accounts) which can't be mass-assigned.
     * These fields are guarded for security but admins need to be able to edit them.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle guarded fields explicitly
        if (array_key_exists('is_admin', $data)) {
            $this->record->is_admin = $data['is_admin'];
            unset($data['is_admin']);
        }

        if (array_key_exists('max_game_accounts', $data)) {
            $this->record->max_game_accounts = $data['max_game_accounts'];
            unset($data['max_game_accounts']);
        }

        return $data;
    }
}
