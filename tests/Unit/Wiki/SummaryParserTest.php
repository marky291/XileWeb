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

    public function test_parses_depth_two_nesting(): void
    {
        $summary = <<<MD
        ## Deep

        * [Parent](parent.md)
          * [Child](child.md)
            * [Grandchild](grandchild.md)
        MD;

        $sections = (new SummaryParser())->parse($summary, 'xilero');

        $this->assertCount(1, $sections);

        $parent     = $sections[0]['items'][0];
        $child      = $parent['children'][0];
        $grandchild = $child['children'][0];

        $this->assertSame('Parent', $parent['label']);
        $this->assertSame('/wiki/xilero/parent', $parent['url']);

        $this->assertSame('Child', $child['label']);
        $this->assertSame('/wiki/xilero/child', $child['url']);
        $this->assertSame([], $child['children'][0]['children']);

        $this->assertSame('Grandchild', $grandchild['label']);
        $this->assertSame('/wiki/xilero/grandchild', $grandchild['url']);
    }
}
