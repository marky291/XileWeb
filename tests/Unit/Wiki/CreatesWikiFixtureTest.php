<?php

namespace Tests\Unit\Wiki;

use Tests\TestCase;
use Tests\Wiki\CreatesWikiFixture;

class CreatesWikiFixtureTest extends TestCase
{
    use CreatesWikiFixture;

    protected function tearDown(): void
    {
        $this->tearDownWikiFixture();
        parent::tearDown();
    }

    public function test_fixture_creates_gitbook_tree_and_sets_config(): void
    {
        $base = $this->makeWikiFixture('xilero');

        $this->assertSame($base, config('wiki.servers.xilero.path'));
        $this->assertFileExists($base . '/README.md');
        $this->assertFileExists($base . '/SUMMARY.md');
        $this->assertFileExists($base . '/guide/sample.md');
        $this->assertFileExists($base . '/.gitbook/assets/pic.png');
        $this->assertStringContainsString('{% hint style="info" %}', file_get_contents($base . '/guide/sample.md'));
    }
}
