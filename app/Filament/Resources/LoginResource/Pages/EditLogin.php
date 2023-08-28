<?php

namespace App\Filament\Resources\LoginResource\Pages;

use App\Actions\MakeHashedLoginPassword;
use App\Filament\Resources\LoginResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditLogin extends EditRecord
{
    protected static string $resource = LoginResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('data.user_pass', $this->oldFormState) && $this->data['user_pass'] != "") {
            $data['user_pass'] = MakeHashedLoginPassword::run($data['user_pass']);
        } else {
            $data['user_pass'] = $this->record->user_pass;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }
}
