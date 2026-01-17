<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;

class ImportItemDatabase extends Command
{
    protected $signature = 'items:import
                            {--path=UberShop/web_itemdb.json : Path to JSON file relative to storage/}
                            {--xileretro : Import as XileRetro items (default: XileRO)}
                            {--fresh : Truncate table before importing}
                            {--dry-run : Show what would be imported without making changes}';

    protected $description = 'Import item database from JSON file into items table';

    public function handle(): int
    {
        $path = $this->option('path');
        $isXileretro = $this->option('xileretro');
        $fresh = $this->option('fresh');
        $dryRun = $this->option('dry-run');

        $fullPath = storage_path($path);

        if (! file_exists($fullPath)) {
            $this->error("File not found: {$fullPath}");

            return Command::FAILURE;
        }

        $serverName = $isXileretro ? 'XileRetro' : 'XileRO';
        $this->info("Importing {$serverName} items from: {$fullPath}");
        $this->newLine();

        $json = file_get_contents($fullPath);
        $items = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Failed to parse JSON: '.json_last_error_msg());

            return Command::FAILURE;
        }

        $this->info('Found '.count($items).' items in JSON file.');

        if ($dryRun) {
            $this->info('[DRY RUN] Would import '.count($items)." {$serverName} items.");
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
            if (! $this->confirm("This will delete all existing {$serverName} items. Continue?")) {
                $this->info('Import cancelled.');

                return Command::SUCCESS;
            }
            Item::where('is_xileretro', $isXileretro)->delete();
            $this->warn("Deleted existing {$serverName} items.");
        }

        $imported = 0;
        $updated = 0;

        $this->withProgressBar($items, function ($item) use (&$imported, &$updated, $isXileretro) {
            $exists = Item::where('item_id', $item['client_item_id'])
                ->where('is_xileretro', $isXileretro)
                ->exists();

            Item::updateOrCreate(
                [
                    'item_id' => $item['client_item_id'],
                    'is_xileretro' => $isXileretro,
                ],
                [
                    'aegis_name' => $item['aegis_name'] ?? '',
                    'name' => $item['client_item_name'],
                    'description' => $item['client_item_description'] ?: null,
                    'type' => $item['item_type'] ?: 'Etc',
                    'subtype' => $item['item_subtype'] ?: null,
                    'weight' => $item['weight'] ?? 0,
                    'buy' => $item['buy_price'] ?? 0,
                    'sell' => $item['sell_price'] ?? 0,
                    'attack' => $item['attack'] ?? 0,
                    'defense' => $item['defense'] ?? 0,
                    'slots' => $item['client_item_slots'] ?? 0,
                    'refineable' => ! empty($item['refineable']),
                    'jobs' => ! empty($item['jobs']) ? $item['jobs'] : null,
                    'locations' => ! empty($item['equip_locations']) ? $item['equip_locations'] : null,
                    'flags' => ! empty($item['flags']) ? $item['flags'] : null,
                    'trade' => ! empty($item['trade']) ? $item['trade'] : null,
                    'script' => $item['script'] ?? null,
                    'equip_script' => $item['equip_script'] ?? null,
                    'unequip_script' => $item['unequip_script'] ?? null,
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
