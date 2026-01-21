<?php

namespace Tests\Feature\Console;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportItemDatabaseTest extends TestCase
{
    use RefreshDatabase;

    private string $importPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importPath = 'tests/temp/items-import.json';

        // Ensure temp directory exists
        File::ensureDirectoryExists(storage_path('tests/temp'));
    }

    protected function tearDown(): void
    {
        // Clean up test files
        $fullPath = storage_path($this->importPath);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }

        $tempDir = storage_path('tests/temp');
        if (File::isDirectory($tempDir)) {
            File::deleteDirectory($tempDir);
        }

        parent::tearDown();
    }

    private function createTestJson(array $items): void
    {
        $fullPath = storage_path($this->importPath);
        File::ensureDirectoryExists(dirname($fullPath));
        File::put($fullPath, json_encode($items));
    }

    private function createCompleteItem(array $overrides = []): array
    {
        return array_merge([
            'client_item_id' => 501,
            'aegis_name' => 'Red_Potion',
            'client_item_name' => 'Red Potion',
            'client_item_description' => 'A small red potion.',
            'item_type' => 'Healing',
            'item_subtype' => null,
            'weight' => 70,
            'buy_price' => 50,
            'sell_price' => 25,
            'attack' => 0,
            'defense' => 0,
            'client_item_slots' => 0,
            'refineable' => false,
            'jobs' => null,
            'equip_locations' => null,
            'flags' => null,
            'trade' => null,
            'script' => null,
            'equip_script' => null,
            'unequip_script' => null,
        ], $overrides);
    }

    #[Test]
    public function it_fails_when_file_not_found(): void
    {
        $this->artisan('items:import', ['--path' => 'nonexistent/file.json'])
            ->assertFailed()
            ->expectsOutputToContain('File not found');
    }

    #[Test]
    public function it_fails_on_invalid_json(): void
    {
        $fullPath = storage_path($this->importPath);
        File::ensureDirectoryExists(dirname($fullPath));
        File::put($fullPath, 'invalid json {{{');

        $this->artisan('items:import', ['--path' => $this->importPath])
            ->assertFailed()
            ->expectsOutputToContain('Failed to parse JSON');
    }

    #[Test]
    public function it_imports_items_to_database(): void
    {
        $this->createTestJson([
            $this->createCompleteItem([
                'client_item_id' => 501,
                'client_item_name' => 'Red Potion',
            ]),
        ]);

        $this->artisan('items:import', ['--path' => $this->importPath])
            ->assertSuccessful()
            ->expectsOutputToContain('1 created');

        $this->assertDatabaseHas('items', [
            'item_id' => 501,
            'name' => 'Red Potion',
            'is_xileretro' => false,
        ]);
    }

    #[Test]
    public function it_imports_as_xileretro_items_with_flag(): void
    {
        $this->createTestJson([
            $this->createCompleteItem([
                'client_item_id' => 502,
                'client_item_name' => 'Blue Potion',
            ]),
        ]);

        $this->artisan('items:import', ['--path' => $this->importPath, '--xileretro' => true])
            ->assertSuccessful();

        $this->assertDatabaseHas('items', [
            'item_id' => 502,
            'is_xileretro' => true,
        ]);
    }

    #[Test]
    public function it_updates_existing_items(): void
    {
        // Create existing item
        Item::factory()->create([
            'item_id' => 503,
            'name' => 'Old Name',
            'is_xileretro' => false,
        ]);

        $this->createTestJson([
            $this->createCompleteItem([
                'client_item_id' => 503,
                'aegis_name' => 'Updated_Item',
                'client_item_name' => 'New Name',
            ]),
        ]);

        $this->artisan('items:import', ['--path' => $this->importPath])
            ->assertSuccessful()
            ->expectsOutputToContain('1 updated');

        $this->assertDatabaseHas('items', [
            'item_id' => 503,
            'name' => 'New Name',
        ]);

        // Should not create duplicate
        $this->assertEquals(1, Item::where('item_id', 503)->count());
    }

    #[Test]
    public function it_shows_preview_in_dry_run_mode(): void
    {
        $this->createTestJson([
            $this->createCompleteItem([
                'client_item_id' => 504,
                'client_item_name' => 'Test Item',
                'item_type' => 'Weapon',
            ]),
        ]);

        $this->artisan('items:import', ['--path' => $this->importPath, '--dry-run' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('DRY RUN');

        // Should NOT create the item
        $this->assertDatabaseMissing('items', [
            'item_id' => 504,
        ]);
    }

    #[Test]
    public function it_deletes_existing_items_with_fresh_flag(): void
    {
        // Create existing items
        Item::factory()->create([
            'item_id' => 505,
            'is_xileretro' => false,
        ]);

        Item::factory()->create([
            'item_id' => 506,
            'is_xileretro' => false,
        ]);

        $this->createTestJson([
            $this->createCompleteItem([
                'client_item_id' => 507,
                'client_item_name' => 'New Item',
            ]),
        ]);

        // Need to confirm the deletion
        $this->artisan('items:import', ['--path' => $this->importPath, '--fresh' => true])
            ->expectsConfirmation('This will delete all existing XileRO items. Continue?', 'yes')
            ->assertSuccessful();

        // Old items should be deleted
        $this->assertDatabaseMissing('items', ['item_id' => 505]);
        $this->assertDatabaseMissing('items', ['item_id' => 506]);

        // New item should be created
        $this->assertDatabaseHas('items', ['item_id' => 507]);
    }

    #[Test]
    public function it_cancels_when_fresh_flag_not_confirmed(): void
    {
        Item::factory()->create([
            'item_id' => 508,
            'is_xileretro' => false,
        ]);

        $this->createTestJson([
            $this->createCompleteItem([
                'client_item_id' => 509,
                'client_item_name' => 'New Item',
            ]),
        ]);

        $this->artisan('items:import', ['--path' => $this->importPath, '--fresh' => true])
            ->expectsConfirmation('This will delete all existing XileRO items. Continue?', 'no')
            ->assertSuccessful()
            ->expectsOutputToContain('cancelled');

        // Old item should still exist
        $this->assertDatabaseHas('items', ['item_id' => 508]);

        // New item should NOT be created
        $this->assertDatabaseMissing('items', ['item_id' => 509]);
    }

    #[Test]
    public function it_imports_multiple_items(): void
    {
        $this->createTestJson([
            $this->createCompleteItem(['client_item_id' => 511, 'client_item_name' => 'Item 1', 'item_type' => 'Weapon']),
            $this->createCompleteItem(['client_item_id' => 512, 'client_item_name' => 'Item 2', 'item_type' => 'Armor']),
            $this->createCompleteItem(['client_item_id' => 513, 'client_item_name' => 'Item 3', 'item_type' => 'Etc']),
        ]);

        $this->artisan('items:import', ['--path' => $this->importPath])
            ->assertSuccessful()
            ->expectsOutputToContain('3 created');

        $this->assertEquals(3, Item::whereIn('item_id', [511, 512, 513])->count());
    }
}
