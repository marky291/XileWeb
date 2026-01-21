<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Jobs\SendPostToDiscord;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate slug from title if not provided
        $data['slug'] = Str::slug($data['title']);

        // Track who created the post
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        // Send Discord notification
        SendPostToDiscord::dispatch($this->record);
    }
}
