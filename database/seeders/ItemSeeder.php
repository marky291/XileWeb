<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Seed items from the exported JSON file.
     *
     * To generate the JSON file, run: php artisan items:export
     */
    public function run(): void
    {
        $path = database_path('seeders/items.json');

        if (! file_exists($path)) {
            $this->command->error("Item seed file not found: {$path}");
            $this->command->info('Run "php artisan items:export" to generate the seed file from the database.');

            return;
        }

        $json = file_get_contents($path);
        $items = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Failed to parse JSON: '.json_last_error_msg());

            return;
        }

        $this->command->info('Seeding '.count($items).' items...');

        // Truncate existing items
        Item::query()->delete();

        // Insert in chunks for performance
        $chunks = array_chunk($items, 500);

        $this->command->withProgressBar($chunks, function (array $chunk) {
            foreach ($chunk as $itemData) {
                Item::create($itemData);
            }
        });

        $this->command->newLine();
        $this->command->info('Seeded '.count($items).' items.');
    }
}
