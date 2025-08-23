<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate slug from title if not provided
        $data['slug'] = Str::slug($data['title']);
        // Posts are always for XileRO (x9) client
        $data['client'] = 'x9';

        return $data;
    }
}
