<?php

namespace App\Console\Commands;

use App\Models\DatabaseItem;
use Illuminate\Console\Command;

class ImportItemDatabase extends Command
{
    protected $signature = 'items:import
                            {--path=UberShop/web_itemdb.json : Path to JSON file relative to storage/}
                            {--fresh : Truncate table before importing}
                            {--dry-run : Show what would be imported without making changes}';

    protected $description = 'Import item database from web_itemdb.json into database_items table';

    public function handle(): int
    {
        $path = $this->option('path');
        $fresh = $this->option('fresh');
        $dryRun = $this->option('dry-run');

        $fullPath = storage_path($path);

        if (! file_exists($fullPath)) {
            $this->error("File not found: {$fullPath}");

            return Command::FAILURE;
        }

        $this->info("Importing from: {$fullPath}");
        $this->newLine();

        $json = file_get_contents($fullPath);
        $items = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Failed to parse JSON: '.json_last_error_msg());

            return Command::FAILURE;
        }

        $this->info('Found '.count($items).' items in JSON file.');

        if ($dryRun) {
            $this->info('[DRY RUN] Would import '.count($items).' items.');
            $this->table(
                ['Item ID', 'Name', 'Type'],
                collect($items)->take(10)->map(fn ($item) => [
                    $item['client_item_id'],
                    $item['client_item_name'],
                    $item['item_type'] ?: '-',
                ])->toArray()
            );
            $this->info('... and '.(count($items) - 10).' more items.');

            return Command::SUCCESS;
        }

        if ($fresh) {
            if (! $this->confirm('This will delete all existing items. Continue?')) {
                $this->info('Import cancelled.');

                return Command::SUCCESS;
            }
            DatabaseItem::truncate();
            $this->warn('Truncated database_items table.');
        }

        $imported = 0;
        $updated = 0;

        $this->withProgressBar($items, function ($item) use (&$imported, &$updated) {
            $exists = DatabaseItem::where('item_id', $item['client_item_id'])->exists();

            DatabaseItem::updateOrCreate(
                ['item_id' => $item['client_item_id']],
                [
                    'name' => $item['client_item_name'],
                    'description' => $item['client_item_description'] ?: null,
                    'aegis_name' => $item['aegis_name'] ?: null,
                    'item_type' => $item['item_type'] ?: null,
                    'item_subtype' => $item['item_subtype'] ?: null,
                    'slots' => $item['client_item_slots'] ?? 0,
                    'weight' => $item['weight'] ?? 0,
                    'attack' => $item['attack'] ?? 0,
                    'defense' => $item['defense'] ?? 0,
                    'equip_level_min' => $item['equip_level_min'] ?? 0,
                    'weapon_level' => $item['weapon_level'] ?? 0,
                    'equip_locations' => ! empty($item['equip_locations']) ? $item['equip_locations'] : null,
                    'jobs' => ! empty($item['jobs']) ? $item['jobs'] : null,
                    'buy_price' => $item['buy_price'] ?? 0,
                    'sell_price' => $item['sell_price'] ?? 0,
                    'icon_path' => $item['web_item'] ?: null,
                    'collection_path' => $item['web_collection'] ?: null,
                    'client_icon' => $item['client_item'] ?: null,
                    'client_collection' => $item['client_collection'] ?: null,
                ]
            );

            if ($exists) {
                $updated++;
            } else {
                $imported++;
            }
        });

        $this->newLine(2);
        $this->info("Import complete: {$imported} created, {$updated} updated.");

        return Command::SUCCESS;
    }
}
