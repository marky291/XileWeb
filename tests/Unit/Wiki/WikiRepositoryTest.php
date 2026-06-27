<?php

namespace Tests\Unit\Wiki;

use App\Services\Wiki\WikiRepository;
use Tests\TestCase;
use Tests\Wiki\CreatesWikiFixture;

class WikiRepositoryTest extends TestCase
{
    use CreatesWikiFixture;

    protected function tearDown(): void
    {
        $this->tearDownWikiFixture();
        parent::tearDown();
    }

    private function repo(): WikiRepository
    {
        return new WikiRepository();
    }

    public function test_reads_index_pages_and_summary(): void
    {
        $this->makeWikiFixture('xilero');
        $repo = $this->repo();

        $this->assertTrue($repo->hasServer('xilero'));
        $this->assertTrue($repo->isAvailable('xilero'));
        $this->assertStringContainsString('# Welcome', $repo->readPage('xilero', ''));
        $this->assertStringContainsString('# Sample Guide', $repo->readPage('xilero', 'guide/sample'));
        $this->assertStringContainsString('## Guides', $repo->readSummary('xilero'));
    }

    public function test_unknown_or_unavailable_server(): void
    {
        $repo = $this->repo();
        $this->assertFalse($repo->hasServer('nope'));
        $this->assertFalse($repo->isAvailable('xileretro')); // configured, no path
    }

    public function test_resolves_allowed_asset_only(): void
    {
        $base = $this->makeWikiFixture('xilero');
        $repo = $this->repo();

        $this->assertSame(realpath($base . '/.gitbook/assets/pic.png'), $repo->resolveAsset('xilero', 'pic.png'));
        $this->assertNull($repo->resolveAsset('xilero', 'nope.png'));
        $this->assertNull($repo->resolveAsset('xilero', 'pic.txt')); // disallowed extension
    }

    public function test_blocks_path_traversal(): void
    {
        $this->makeWikiFixture('xilero');
        $repo = $this->repo();

        $this->assertNull($repo->readPage('xilero', '../../../etc/passwd'));
        $this->assertNull($repo->resolveAsset('xilero', '../../README.md'));
    }
}
