<?php

namespace Tests\Unit\Wiki;

use App\Services\Wiki\SummaryParser;
use Tests\TestCase;

class SummaryParserTest extends TestCase
{
    public function test_parses_sections_items_and_nesting(): void
    {
        $summary = <<<MD
        # Table of contents

        ## Start

        * [Welcome](README.md)

        ## Guides

        * [Sample Guide](guide/sample.md)
          * [Nested](guide/nested.md)
        MD;

        $sections = (new SummaryParser())->parse($summary, 'xilero');

        $this->assertCount(2, $sections);
        $this->assertSame('Start', $sections[0]['title']);
        $this->assertSame('Welcome', $sections[0]['items'][0]['label']);
        $this->assertSame('/wiki/xilero', $sections[0]['items'][0]['url']);

        $this->assertSame('Guides', $sections[1]['title']);
        $this->assertSame('/wiki/xilero/guide/sample', $sections[1]['items'][0]['url']);
        $this->assertSame('Nested', $sections[1]['items'][0]['children'][0]['label']);
        $this->assertSame('/wiki/xilero/guide/nested', $sections[1]['items'][0]['children'][0]['url']);
    }
}
