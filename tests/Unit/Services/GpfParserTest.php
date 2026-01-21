<?php

namespace Tests\Unit\Services;

use App\Services\GpfParser;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class GpfParserTest extends TestCase
{
    private GpfParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new GpfParser;
    }

    #[Test]
    public function it_throws_exception_for_invalid_header(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid GPF file header');

        iterator_to_array($this->parser->extractAllFromString('Invalid file content'));
    }

    #[Test]
    public function it_throws_exception_for_too_small_file(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('GPF file is too small');

        iterator_to_array($this->parser->extractAllFromString('short'));
    }

    #[Test]
    public function it_yields_nothing_when_no_valid_zlib_data(): void
    {
        // Valid header but no zlib data
        $data = "Master of Magic\0".str_repeat('x', 100);

        $results = iterator_to_array($this->parser->extractAllFromString($data));

        $this->assertEmpty($results);
    }

    #[Test]
    public function it_extracts_zlib_compressed_content(): void
    {
        $content = 'tbl = { [501] = { identifiedDisplayName = "Test Item" } }';
        $compressed = gzcompress($content);

        $data = "Master of Magic\0".str_repeat("\0", 30).$compressed;

        $results = iterator_to_array($this->parser->extractAllFromString($data));

        $this->assertCount(1, $results);
        $this->assertEquals($content, $results[0]);
    }

    #[Test]
    public function it_extracts_multiple_zlib_streams(): void
    {
        $content1 = 'AccNameTable = { }';
        $content2 = 'tbl = { [501] = { identifiedDisplayName = "Test" } }';

        $compressed1 = gzcompress($content1);
        $compressed2 = gzcompress($content2);

        $data = "Master of Magic\0".str_repeat("\0", 30).$compressed1.str_repeat("\0", 10).$compressed2;

        $results = iterator_to_array($this->parser->extractAllFromString($data));

        $this->assertGreaterThanOrEqual(1, count($results));
    }

    #[Test]
    public function has_item_info_returns_true_for_valid_content(): void
    {
        $content = 'tbl = { [501] = { identifiedDisplayName = "Test" } }';

        $this->assertTrue($this->parser->hasItemInfo($content));
    }

    #[Test]
    public function has_item_info_returns_false_for_invalid_content(): void
    {
        $content = 'AccNameTable = { }';

        $this->assertFalse($this->parser->hasItemInfo($content));
    }

    #[Test]
    public function find_item_info_from_string_returns_item_info_content(): void
    {
        $itemInfoContent = 'tbl = { [501] = { identifiedDisplayName = "Test" } }';
        $compressed = gzcompress($itemInfoContent);
        $data = "Master of Magic\0".str_repeat("\0", 30).$compressed;

        $result = $this->parser->findItemInfoFromString($data);

        $this->assertNotNull($result);
        $this->assertStringContainsString('tbl = {', $result);
        $this->assertStringContainsString('identifiedDisplayName', $result);
    }

    #[Test]
    public function find_item_info_from_string_returns_null_when_no_item_info(): void
    {
        $content = 'AccNameTable = { }';
        $compressed = gzcompress($content);
        $data = "Master of Magic\0".str_repeat("\0", 30).$compressed;

        $this->assertNull($this->parser->findItemInfoFromString($data));
    }

    #[Test]
    public function find_item_info_searches_multiple_streams(): void
    {
        // First stream: AccNameTable (no ItemInfo)
        $content1 = 'AccNameTable = { [1] = "test" }';
        $compressed1 = gzcompress($content1);

        // Second stream: ItemInfo
        $content2 = 'tbl = { [501] = { identifiedDisplayName = "Found Item" } }';
        $compressed2 = gzcompress($content2);

        $data = "Master of Magic\0".str_repeat("\0", 30).$compressed1.str_repeat("\0", 50).$compressed2;

        $result = $this->parser->findItemInfoFromString($data);

        $this->assertNotNull($result);
        $this->assertStringContainsString('Found Item', $result);
    }
}
