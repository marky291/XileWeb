<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditPost extends EditRecord
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
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Re-generate slug from title when updating
        $data['slug'] = Str::slug($data['title']);

        return $data;
    }
}
