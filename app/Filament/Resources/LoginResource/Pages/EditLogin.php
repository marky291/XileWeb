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

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }
}
