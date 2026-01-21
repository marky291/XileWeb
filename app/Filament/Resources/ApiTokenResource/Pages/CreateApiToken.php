<?php

namespace App\Filament\Resources\ApiTokenResource\Pages;

use App\Filament\Resources\ApiTokenResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateApiToken extends CreateRecord
{
    protected static string $resource = ApiTokenResource::class;

    protected ?string $plainTextToken = null;

    protected function handleRecordCreation(array $data): Model
    {
        $user = User::findOrFail($data['tokenable_id']);

        $expiresAt = isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null;

        $newAccessToken = $user->createToken(
            name: $data['name'],
            abilities: $data['abilities'] ?? ['read'],
            expiresAt: $expiresAt,
        );

        $this->plainTextToken = $newAccessToken->plainTextToken;

        return $newAccessToken->accessToken;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('API Token Created')
            ->body("Copy your token now - it won't be shown again:\n\n{$this->plainTextToken}")
            ->persistent();
    }
}
