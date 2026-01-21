<?php

namespace Tests\Feature\Console;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExportItemDatabaseTest extends TestCase
{
    use RefreshDatabase;

    private string $exportPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exportPath = 'tests/temp/items-export.json';
    }

    protected function tearDown(): void
    {
        // Clean up test file
        $fullPath = base_path($this->exportPath);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
        // Clean up temp directory
        $tempDir = base_path('tests/temp');
        if (File::isDirectory($tempDir) && count(File::files($tempDir)) === 0) {
            File::deleteDirectory($tempDir);
        }
        parent::tearDown();
    }

    #[Test]
    public function it_exports_items_to_json_file(): void
    {
        Item::factory()->count(3)->create(['is_xileretro' => false]);

        $this->artisan('items:export', ['--path' => $this->exportPath])
            ->assertSuccessful()
            ->expectsOutputToContain('Exported 3 items');

        $this->assertFileExists(base_path($this->exportPath));
    }

    #[Test]
    public function it_creates_directory_if_not_exists(): void
    {
        $nestedPath = 'tests/temp/nested/dir/items.json';
        Item::factory()->create();

        $this->artisan('items:export', ['--path' => $nestedPath])
            ->assertSuccessful();

        $this->assertFileExists(base_path($nestedPath));

        // Cleanup
        File::delete(base_path($nestedPath));
        File::deleteDirectory(base_path('tests/temp/nested'));
    }

    #[Test]
    public function it_exports_all_item_fields(): void
    {
        Item::factory()->create([
            'item_id' => 12345,
            'aegis_name' => 'Test_Item',
            'name' => 'Test Item',
            'description' => 'Test description',
            'type' => 'Weapon',
            'subtype' => 'Dagger',
            'weight' => 100,
            'buy' => 1000,
            'sell' => 500,
            'attack' => 50,
            'defense' => 10,
            'slots' => 2,
            'refineable' => true,
            'is_xileretro' => false,
        ]);

        $this->artisan('items:export', ['--path' => $this->exportPath])
            ->assertSuccessful();

        $content = File::get(base_path($this->exportPath));
        $items = json_decode($content, true);

        $this->assertCount(1, $items);
        $this->assertEquals(12345, $items[0]['item_id']);
        $this->assertEquals('Test_Item', $items[0]['aegis_name']);
        $this->assertEquals('Test Item', $items[0]['name']);
        $this->assertEquals('Weapon', $items[0]['type']);
        $this->assertEquals('Dagger', $items[0]['subtype']);
        $this->assertEquals(100, $items[0]['weight']);
        $this->assertEquals(1000, $items[0]['buy']);
        $this->assertEquals(500, $items[0]['sell']);
        $this->assertEquals(50, $items[0]['attack']);
        $this->assertEquals(10, $items[0]['defense']);
        $this->assertEquals(2, $items[0]['slots']);
        $this->assertTrue($items[0]['refineable']);
    }

    #[Test]
    public function it_orders_items_by_server_then_item_id(): void
    {
        Item::factory()->create(['item_id' => 300, 'is_xileretro' => false]);
        Item::factory()->create(['item_id' => 100, 'is_xileretro' => true]);
        Item::factory()->create(['item_id' => 200, 'is_xileretro' => false]);

        $this->artisan('items:export', ['--path' => $this->exportPath])
            ->assertSuccessful();

        $content = File::get(base_path($this->exportPath));
        $items = json_decode($content, true);

        // XileRO items (is_xileretro = false) should come first, ordered by item_id
        $this->assertEquals(200, $items[0]['item_id']);
        $this->assertEquals(300, $items[1]['item_id']);
        // XileRetro items (is_xileretro = true) should come last
        $this->assertEquals(100, $items[2]['item_id']);
    }

    #[Test]
    public function it_exports_valid_json(): void
    {
        Item::factory()->count(5)->create();

        $this->artisan('items:export', ['--path' => $this->exportPath])
            ->assertSuccessful();

        $content = File::get(base_path($this->exportPath));
        $decoded = json_decode($content, true);

        $this->assertNotNull($decoded);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
    }

    #[Test]
    public function it_uses_default_path(): void
    {
        Item::factory()->create();

        // Without --path, it should use default: database/seeders/items.json
        $this->artisan('items:export')
            ->assertSuccessful();

        $defaultPath = base_path('database/seeders/items.json');
        $this->assertFileExists($defaultPath);

        // Clean up
        File::delete($defaultPath);
    }

    #[Test]
    public function it_handles_empty_database(): void
    {
        $this->artisan('items:export', ['--path' => $this->exportPath])
            ->assertSuccessful()
            ->expectsOutputToContain('Exported 0 items');

        $content = File::get(base_path($this->exportPath));
        $items = json_decode($content, true);

        $this->assertEquals([], $items);
    }

    #[Test]
    public function it_preserves_unicode_characters(): void
    {
        Item::factory()->create([
            'name' => 'Japanese Item',
            'description' => 'Test with unicode',
        ]);

        $this->artisan('items:export', ['--path' => $this->exportPath])
            ->assertSuccessful();

        $content = File::get(base_path($this->exportPath));

        // With JSON_UNESCAPED_UNICODE, characters should not be escaped
        $this->assertStringContainsString('Japanese Item', $content);
    }
}
