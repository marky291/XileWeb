<?php

namespace App\Jobs;

use App\Models\Item;
use App\Models\Patch;
use App\Services\GpfParser;
use App\Services\GrfImageExtractor;
use App\Services\ItemInfoParser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CompilePatch implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Patch $patch
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        GpfParser $gpfParser,
        ItemInfoParser $itemInfoParser,
        GrfImageExtractor $imageExtractor
    ): void {
        $disk = $this->getDiskForClient($this->patch->client);
        $filePath = $this->patch->file;

        if (! $filePath || ! Storage::disk($disk)->exists($filePath)) {
            $this->patch->update(['is_compiling' => false]);
            throw new RuntimeException("Patch file not found: {$filePath}");
        }

        $fullPath = Storage::disk($disk)->path($filePath);

        Log::info("Compiling patch #{$this->patch->number} ({$this->patch->type}) from {$fullPath}");

        try {
            if ($this->patch->type === 'FLD') {
                $this->compileFldPatch($gpfParser, $itemInfoParser, $fullPath);
            } elseif ($this->patch->type === 'GRF') {
                $this->compileGrfPatch($imageExtractor, $fullPath);
            } else {
                Log::info("Unknown patch type: {$this->patch->type}");
            }

            $this->patch->update([
                'is_compiling' => false,
                'compiled_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $this->patch->update(['is_compiling' => false]);
            throw $e;
        }
    }

    /**
     * Compile FLD patch - extract ItemInfo and update item database.
     */
    private function compileFldPatch(GpfParser $gpfParser, ItemInfoParser $itemInfoParser, string $fullPath): void
    {
        $itemInfoContent = $gpfParser->findItemInfo($fullPath);

        if ($itemInfoContent === null) {
            Log::info("Patch #{$this->patch->number} does not contain ItemInfo data");

            return;
        }

        Log::info("Found ItemInfo in patch #{$this->patch->number}, size: ".strlen($itemInfoContent).' bytes');

        $isXileRetro = $this->patch->client === Patch::CLIENT_RETRO;

        $updatedCount = 0;
        $createdCount = 0;

        foreach ($itemInfoParser->parse($itemInfoContent) as $itemData) {
            $item = Item::updateOrCreate(
                [
                    'item_id' => $itemData['item_id'],
                    'is_xileretro' => $isXileRetro,
                ],
                [
                    'name' => $itemData['name'],
                    'description' => $itemData['description'],
                    'resource_name' => $itemData['resource_name'],
                    'view_id' => $itemData['view_id'],
                    'slots' => $itemData['slot_count'],
                    'data_patch_id' => $this->patch->id,
                ]
            );

            if ($item->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        Log::info("Patch #{$this->patch->number} compiled: {$createdCount} items created, {$updatedCount} items updated");
    }

    /**
     * Compile GRF patch - extract images and save mapped to item IDs.
     */
    private function compileGrfPatch(GrfImageExtractor $imageExtractor, string $fullPath): void
    {
        $isXileRetro = $this->patch->client === Patch::CLIENT_RETRO;
        $clientFolder = $isXileRetro ? 'retro' : 'xilero';

        // Build resource_name => item_id map from database
        $resourceMap = Item::where('is_xileretro', $isXileRetro)
            ->whereNotNull('resource_name')
            ->where('resource_name', '!=', '')
            ->pluck('item_id', 'resource_name')
            ->toArray();

        Log::info('Built resource map with '.count($resourceMap).' items for '.($isXileRetro ? 'XileRetro' : 'XileRO'));

        // Extract images to public storage under client folder
        $result = $imageExtractor->extractImages($fullPath, 'public', $resourceMap, $clientFolder);

        Log::info("Patch #{$this->patch->number} images extracted: {$result['extracted']} extracted, {$result['skipped']} skipped");

        // Update items with sprite_patch_id
        if (! empty($result['extracted_item_ids'])) {
            Item::where('is_xileretro', $isXileRetro)
                ->whereIn('item_id', $result['extracted_item_ids'])
                ->update(['sprite_patch_id' => $this->patch->id]);
        }

        if (! empty($result['errors'])) {
            Log::warning("Patch #{$this->patch->number} had ".count($result['errors']).' errors during extraction');
            foreach (array_slice($result['errors'], 0, 10) as $error) {
                Log::warning("  - {$error}");
            }
        }
    }

    /**
     * Get the storage disk name for the given client.
     */
    private function getDiskForClient(string $client): string
    {
        return match ($client) {
            Patch::CLIENT_RETRO => 'retro_patch',
            Patch::CLIENT_XILERO => 'xilero_patch',
            default => 'xilero_patch',
        };
    }
}
