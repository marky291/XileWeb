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

    protected ?string $plainTextToken = null;

    protected ?string $tokenName = null;

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
        $this->tokenName = $data['name'];

        return $newAccessToken->accessToken;
    }

    protected function afterCreate(): void
    {
        session([
            'api_token_plain_text' => $this->plainTextToken,
            'api_token_name' => $this->tokenName,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return ApiTokenResource::getUrl('success');
    }
}
