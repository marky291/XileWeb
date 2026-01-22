<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Jobs\SendPostToDiscord;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('aiPrompt')
                ->label('AI Prompt')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'bg-gradient-to-r from-purple-600 to-amber-500 hover:from-purple-500 hover:to-amber-400 border-0',
                ])
                ->modalHeading('AI Article Generator Prompt')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(view('filament.resources.post-resource.ai-prompt-modal'))
                ->modalWidth('4xl'),
        ];
    }

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
