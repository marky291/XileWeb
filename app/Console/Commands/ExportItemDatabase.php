<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;

class ExportItemDatabase extends Command
{
    protected $signature = 'items:export
                            {--path=database/seeders/items.json : Path to JSON file relative to base_path()}';

    protected $description = 'Export item database to JSON file for seeding';

    public function handle(): int
    {
        $path = $this->option('path');
        $fullPath = base_path($path);

        // Ensure directory exists
        $directory = dirname($fullPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->info('Exporting items from database...');

        $items = Item::query()
            ->orderBy('is_xileretro')
            ->orderBy('item_id')
            ->get()
            ->map(fn (Item $item) => [
                'item_id' => $item->item_id,
                'aegis_name' => $item->aegis_name,
                'name' => $item->name,
                'description' => $item->description,
                'type' => $item->type,
                'subtype' => $item->subtype,
                'weight' => $item->weight,
                'buy' => $item->buy,
                'sell' => $item->sell,
                'attack' => $item->attack,
                'defense' => $item->defense,
                'slots' => $item->slots,
                'refineable' => $item->refineable,
                'jobs' => $item->jobs,
                'locations' => $item->locations,
                'flags' => $item->flags,
                'trade' => $item->trade,
                'script' => $item->script,
                'equip_script' => $item->equip_script,
                'unequip_script' => $item->unequip_script,
                'is_xileretro' => $item->is_xileretro,
            ])
            ->toArray();

        $json = json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        file_put_contents($fullPath, $json);

        $this->info('Exported '.count($items)." items to: {$fullPath}");

        return Command::SUCCESS;
    }
}
