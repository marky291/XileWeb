<?php

namespace App\Filament\Resources\PatchResource\Pages;

use App\Filament\Resources\PatchResource;
use App\Jobs\CompilePatch;
use App\Models\Patch;
use App\Models\Post;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePatch extends CreateRecord
{
    protected static string $resource = PatchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate the correct patch number for the selected client
        $client = $data['client'];
        $maxNumber = Patch::where('client', $client)->max('number');
        $data['number'] = $maxNumber ? $maxNumber + 1 : 1;

        // Mark as compiling immediately since we'll auto-compile
        $data['is_compiling'] = true;

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
            // Create the post with auto-generated slug using the selected client
            $post = Post::create([
                'title' => $formData['post_title'],
                'slug' => Str::slug($formData['post_title']),
                'client' => $this->record->client, // Use the client selected for the patch
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

        // Auto-compile the patch
        if (! empty($this->record->file)) {
            CompilePatch::dispatch($this->record);

            Notification::make()
                ->title('Compiling Patch')
                ->body("Patch #{$this->record->number} is being compiled in the background.")
                ->info()
                ->send();
        } else {
            // No file uploaded, reset compiling state
            $this->record->update(['is_compiling' => false]);
        }
    }
}
