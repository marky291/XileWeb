<?php

namespace App\Filament\Resources\ApiTokenResource\Pages;

use App\Filament\Resources\ApiTokenResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateApiToken extends CreateRecord
{
    protected static string $resource = ApiTokenResource::class;

    protected ?string $createdToken = null;

    protected function handleRecordCreation(array $data): Model
    {
        $tokenOwner = User::findOrFail($data['tokenable_id']);

        $expiresAt = isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null;

        $newAccessToken = $tokenOwner->createToken(
            name: $data['name'],
            abilities: $data['abilities'] ?? ['read'],
            expiresAt: $expiresAt,
        );

        $this->createdToken = $newAccessToken->plainTextToken;

        return $newAccessToken->accessToken;
    }

    protected function afterCreate(): void
    {
        session(['api_token_created' => $this->createdToken]);
    }

    protected function getRedirectUrl(): string
    {
        return ApiTokenResource::getUrl('view', ['record' => $this->record]);
    }
}
