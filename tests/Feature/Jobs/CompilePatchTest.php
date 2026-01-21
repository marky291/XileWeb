<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CompilePatch;
use App\Models\Item;
use App\Models\Patch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompilePatchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup fake storage for testing
        Storage::fake('xilero_patch');
        Storage::fake('retro_patch');
    }

    #[Test]
    public function it_processes_grf_patches_gracefully(): void
    {
        // Create a minimal GPF without valid file table
        $gpfContent = "Master of Magic\0".str_repeat("\0", 30).gzcompress('test');

        Storage::disk('xilero_patch')->put('test_grf.gpf', $gpfContent);

        $patch = Patch::factory()->create([
            'type' => 'GRF',
            'client' => Patch::CLIENT_XILERO,
            'file' => 'test_grf.gpf',
        ]);

        // Should not throw - handles missing file table gracefully
        $job = new CompilePatch($patch);
        $job->handle(
            app(\App\Services\GpfParser::class),
            app(\App\Services\ItemInfoParser::class),
            app(\App\Services\GrfImageExtractor::class)
        );

        $this->assertTrue(true);
    }

    #[Test]
    public function it_creates_items_from_item_info(): void
    {
        // Create a valid GPF file with ItemInfo data
        $itemInfoContent = <<<'LUA'
tbl = {
    [501] = {
        identifiedDisplayName = "Red Potion",
        identifiedResourceName = "red_potion",
        identifiedDescriptionName = {
            "A healing potion."
        },
        slotCount = 0,
        ClassNum = 5
    },
    [502] = {
        identifiedDisplayName = "Orange Potion",
        identifiedResourceName = "orange_potion",
        identifiedDescriptionName = {
            "A stronger healing potion."
        },
        slotCount = 0,
        ClassNum = 6
    }
}
LUA;

        $compressed = gzcompress($itemInfoContent);
        $gpfContent = "Master of Magic\0".str_repeat("\0", 30).$compressed;

        Storage::disk('xilero_patch')->put('test_patch.gpf', $gpfContent);

        $patch = Patch::factory()->create([
            'type' => 'FLD',
            'client' => Patch::CLIENT_XILERO,
            'file' => 'test_patch.gpf',
        ]);

        $job = new CompilePatch($patch);
        $job->handle(
            app(\App\Services\GpfParser::class),
            app(\App\Services\ItemInfoParser::class),
            app(\App\Services\GrfImageExtractor::class)
        );

        $this->assertEquals(2, Item::count());

        $item501 = Item::where('item_id', 501)->where('is_xileretro', false)->first();
        $this->assertNotNull($item501);
        $this->assertEquals('Red Potion', $item501->name);
        $this->assertEquals('red_potion', $item501->resource_name);
        $this->assertEquals(5, $item501->view_id);
        $this->assertEquals(0, $item501->slots);
        $this->assertFalse($item501->is_xileretro);
    }

    #[Test]
    public function it_sets_is_xileretro_for_retro_patches(): void
    {
        $itemInfoContent = <<<'LUA'
tbl = {
    [501] = {
        identifiedDisplayName = "Retro Potion",
        identifiedResourceName = "retro_potion",
        slotCount = 0,
        ClassNum = 0
    }
}
LUA;

        $compressed = gzcompress($itemInfoContent);
        $gpfContent = "Master of Magic\0".str_repeat("\0", 30).$compressed;

        Storage::disk('retro_patch')->put('retro_patch.gpf', $gpfContent);

        $patch = Patch::factory()->create([
            'type' => 'FLD',
            'client' => Patch::CLIENT_RETRO,
            'file' => 'retro_patch.gpf',
        ]);

        $job = new CompilePatch($patch);
        $job->handle(
            app(\App\Services\GpfParser::class),
            app(\App\Services\ItemInfoParser::class),
            app(\App\Services\GrfImageExtractor::class)
        );

        $item = Item::where('item_id', 501)->first();
        $this->assertNotNull($item);
        $this->assertTrue($item->is_xileretro);
    }

    #[Test]
    public function it_updates_existing_items(): void
    {
        // Create an existing item
        Item::factory()->create([
            'item_id' => 501,
            'name' => 'Old Name',
            'is_xileretro' => false,
        ]);

        $itemInfoContent = <<<'LUA'
tbl = {
    [501] = {
        identifiedDisplayName = "New Name",
        identifiedResourceName = "new_resource",
        slotCount = 2,
        ClassNum = 10
    }
}
LUA;

        $compressed = gzcompress($itemInfoContent);
        $gpfContent = "Master of Magic\0".str_repeat("\0", 30).$compressed;

        Storage::disk('xilero_patch')->put('update_patch.gpf', $gpfContent);

        $patch = Patch::factory()->create([
            'type' => 'FLD',
            'client' => Patch::CLIENT_XILERO,
            'file' => 'update_patch.gpf',
        ]);

        $job = new CompilePatch($patch);
        $job->handle(
            app(\App\Services\GpfParser::class),
            app(\App\Services\ItemInfoParser::class),
            app(\App\Services\GrfImageExtractor::class)
        );

        // Should still only have 1 item (updated, not created new)
        $this->assertEquals(1, Item::count());

        $item = Item::where('item_id', 501)->first();
        $this->assertEquals('New Name', $item->name);
        $this->assertEquals('new_resource', $item->resource_name);
        $this->assertEquals(10, $item->view_id);
    }

    #[Test]
    public function it_handles_patches_without_item_info(): void
    {
        $content = 'AccNameTable = { }';
        $compressed = gzcompress($content);
        $gpfContent = "Master of Magic\0".str_repeat("\0", 30).$compressed;

        Storage::disk('xilero_patch')->put('no_items.gpf', $gpfContent);

        $patch = Patch::factory()->create([
            'type' => 'FLD',
            'client' => Patch::CLIENT_XILERO,
            'file' => 'no_items.gpf',
        ]);

        $job = new CompilePatch($patch);
        $job->handle(
            app(\App\Services\GpfParser::class),
            app(\App\Services\ItemInfoParser::class),
            app(\App\Services\GrfImageExtractor::class)
        );

        // No items should be created
        $this->assertEquals(0, Item::count());
    }
}
