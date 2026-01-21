<?php

namespace Tests\Unit\Services;

use App\Services\ItemInfoParser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemInfoParserTest extends TestCase
{
    private ItemInfoParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ItemInfoParser;
    }

    #[Test]
    public function it_parses_single_item(): void
    {
        $content = <<<'LUA'
tbl = {
    [501] = {
        identifiedDisplayName = "Red Potion",
        identifiedResourceName = "red_potion",
        identifiedDescriptionName = {
            "A healing potion.",
            "Weight: 7"
        },
        slotCount = 0,
        ClassNum = 5
    }
}
LUA;

        $items = iterator_to_array($this->parser->parse($content));

        $this->assertCount(1, $items);
        $this->assertEquals(501, $items[0]['item_id']);
        $this->assertEquals('Red Potion', $items[0]['name']);
        $this->assertEquals('red_potion', $items[0]['resource_name']);
        $this->assertEquals(5, $items[0]['view_id']);
        $this->assertEquals(0, $items[0]['slot_count']);
        $this->assertStringContainsString('A healing potion.', $items[0]['description']);
    }

    #[Test]
    public function it_parses_multiple_items(): void
    {
        $content = <<<'LUA'
tbl = {
    [501] = {
        identifiedDisplayName = "Red Potion",
        identifiedResourceName = "red_potion",
        slotCount = 0,
        ClassNum = 0
    },
    [502] = {
        identifiedDisplayName = "Orange Potion",
        identifiedResourceName = "orange_potion",
        slotCount = 0,
        ClassNum = 0
    },
    [503] = {
        identifiedDisplayName = "Yellow Potion",
        identifiedResourceName = "yellow_potion",
        slotCount = 0,
        ClassNum = 0
    }
}
LUA;

        $items = iterator_to_array($this->parser->parse($content));

        $this->assertCount(3, $items);
        $this->assertEquals(501, $items[0]['item_id']);
        $this->assertEquals(502, $items[1]['item_id']);
        $this->assertEquals(503, $items[2]['item_id']);
    }

    #[Test]
    public function it_handles_nested_description_arrays(): void
    {
        $content = <<<'LUA'
tbl = {
    [1101] = {
        unidentifiedDisplayName = "Sword",
        unidentifiedResourceName = "sword",
        unidentifiedDescriptionName = { "..." },
        identifiedDisplayName = "Sword",
        identifiedResourceName = "sword",
        identifiedDescriptionName = {
            "A sharp blade.",
            "Attack: 25",
            "Weight: 50"
        },
        slotCount = 3,
        ClassNum = 1
    }
}
LUA;

        $items = iterator_to_array($this->parser->parse($content));

        $this->assertCount(1, $items);
        $this->assertEquals(1101, $items[0]['item_id']);
        $this->assertEquals('Sword', $items[0]['name']);
        $this->assertEquals(3, $items[0]['slot_count']);
        $this->assertEquals(1, $items[0]['view_id']);
        $this->assertStringContainsString('A sharp blade.', $items[0]['description']);
        $this->assertStringContainsString('Attack: 25', $items[0]['description']);
    }

    #[Test]
    public function it_converts_color_codes_to_html_spans(): void
    {
        $content = <<<'LUA'
tbl = {
    [501] = {
        identifiedDisplayName = "Test",
        identifiedResourceName = "test",
        identifiedDescriptionName = {
            "^FF0000Red Text^000000"
        },
        slotCount = 0,
        ClassNum = 0
    }
}
LUA;

        $items = iterator_to_array($this->parser->parse($content));

        $this->assertStringContainsString('<span style="color:#FF0000">', $items[0]['description']);
    }

    #[Test]
    public function it_skips_items_without_identified_display_name(): void
    {
        $content = <<<'LUA'
tbl = {
    [501] = {
        unidentifiedDisplayName = "Unknown",
        slotCount = 0,
        ClassNum = 0
    }
}
LUA;

        $items = iterator_to_array($this->parser->parse($content));

        $this->assertCount(0, $items);
    }

    #[Test]
    public function it_handles_empty_content(): void
    {
        $items = iterator_to_array($this->parser->parse(''));

        $this->assertCount(0, $items);
    }

    #[Test]
    public function it_handles_items_with_high_ids(): void
    {
        $content = <<<'LUA'
tbl = {
    [999999] = {
        identifiedDisplayName = "Custom Item",
        identifiedResourceName = "custom_item",
        slotCount = 4,
        ClassNum = 100
    }
}
LUA;

        $items = iterator_to_array($this->parser->parse($content));

        $this->assertCount(1, $items);
        $this->assertEquals(999999, $items[0]['item_id']);
        $this->assertEquals(100, $items[0]['view_id']);
    }

    #[Test]
    public function it_returns_null_description_for_placeholder_only(): void
    {
        $content = <<<'LUA'
tbl = {
    [501] = {
        identifiedDisplayName = "Test",
        identifiedResourceName = "test",
        identifiedDescriptionName = { "..." },
        slotCount = 0,
        ClassNum = 0
    }
}
LUA;

        $items = iterator_to_array($this->parser->parse($content));

        $this->assertNull($items[0]['description']);
    }
}
