<?php

namespace App\Filament\Resources\PatchResource\Pages;

use App\Filament\Resources\PatchResource;
use App\Models\Post;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePatch extends CreateRecord
{
    protected static string $resource = PatchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove post-related fields from patch data
        unset($data['create_post']);
        unset($data['post_title']);
        unset($data['post_patcher_notice']);
        unset($data['post_article_content']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Check if we should create a post
        $formData = $this->form->getState();

        if ($formData['create_post'] ?? false) {
            // Create the post with auto-generated slug
            $post = Post::create([
                'title' => $formData['post_title'],
                'slug' => Str::slug($formData['post_title']),
                'patcher_notice' => $formData['post_patcher_notice'],
                'article_content' => $formData['post_article_content'],
            ]);

            // Update the patch with the post_id
            $this->record->update(['post_id' => $post->id]);

            Notification::make()
                ->title('Announcement Post Created')
                ->body('The announcement post has been created successfully.')
                ->success()
                ->send();
        }
    }
}
