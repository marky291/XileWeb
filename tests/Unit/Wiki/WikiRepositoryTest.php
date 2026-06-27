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
        config(['wiki.servers.xileretro.path' => null]); // force "configured but no path"
        $repo = $this->repo();
        $this->assertFalse($repo->hasServer('nope'));
        $this->assertFalse($repo->isAvailable('xileretro'));
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

    public function test_resolve_asset_blocks_image_traversal_outside_assets(): void
    {
        $base = $this->makeWikiFixture('xilero');
        $repo = $this->repo();

        // Place a real PNG at the gitbook root (outside .gitbook/assets/)
        file_put_contents(
            $base . '/outside.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=')
        );

        // Traversal with an image extension must be blocked (outside assets/)
        $this->assertNull($repo->resolveAsset('xilero', '../outside.png'));

        // Legitimate asset inside .gitbook/assets/ must still resolve (regression guard)
        $this->assertSame(
            realpath($base . '/.gitbook/assets/pic.png'),
            $repo->resolveAsset('xilero', 'pic.png')
        );
    }
}
