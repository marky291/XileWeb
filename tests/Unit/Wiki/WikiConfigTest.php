<?php

namespace Tests\Unit\Wiki;

use Tests\TestCase;

class WikiConfigTest extends TestCase
{
    public function test_config_defines_both_servers_and_a_default(): void
    {
        $servers = config('wiki.servers');

        $this->assertArrayHasKey('xilero', $servers);
        $this->assertArrayHasKey('xileretro', $servers);
        $this->assertSame('XileRO', $servers['xilero']['label']);
        $this->assertSame('XileRetro', $servers['xileretro']['label']);
        $this->assertArrayHasKey('path', $servers['xilero']);
        $this->assertSame('xilero', config('wiki.default'));
    }
}
