<?php

namespace Tests\Unit\Wiki;

use App\Services\Wiki\FrontmatterParser;
use Tests\TestCase;

class FrontmatterParserTest extends TestCase
{
    public function test_parses_frontmatter_and_strips_it_from_body(): void
    {
        $raw = "---\ndescription: Hello there\ncover: x.png\n---\n\n# Title\n\nBody.";

        $result = (new FrontmatterParser())->parse($raw);

        $this->assertSame('Hello there', $result['attributes']['description']);
        $this->assertSame('x.png', $result['attributes']['cover']);
        $this->assertStringStartsWith('# Title', ltrim($result['body']));
        $this->assertStringNotContainsString('description:', $result['body']);
    }

    public function test_returns_empty_attributes_when_no_frontmatter(): void
    {
        $raw = "# Title\n\nNo frontmatter here.";

        $result = (new FrontmatterParser())->parse($raw);

        $this->assertSame([], $result['attributes']);
        $this->assertSame($raw, $result['body']);
    }
}
