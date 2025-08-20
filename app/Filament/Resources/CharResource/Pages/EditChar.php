<?php

namespace App\Filament\Resources\CharResource\Pages;

use Filament\Actions\Action;
use App\Actions\ResetCharacterPosition;
use App\Filament\Resources\CharResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditChar extends EditRecord
{
    protected static string $resource = CharResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
            Action::make('Reset Character Position')->button()->action(function() {
                   ResetCharacterPosition::run($this->record);
            })
        ];
    }
}
