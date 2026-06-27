<?php

namespace Tests\Feature\Wiki;

use Tests\TestCase;
use Tests\Wiki\CreatesWikiFixture;

class WikiRoutesTest extends TestCase
{
    use CreatesWikiFixture;

    protected function tearDown(): void
    {
        $this->tearDownWikiFixture();
        parent::tearDown();
    }

    public function test_index_page_renders_with_sidebar(): void
    {
        $this->makeWikiFixture('xilero');

        $this->get('/wiki/xilero')
            ->assertOk()
            ->assertSee('Welcome')
            ->assertSee('Guides');          // sidebar section from SUMMARY
    }

    public function test_content_page_renders_hint_figure_and_subtitle(): void
    {
        $this->makeWikiFixture('xilero');

        $res = $this->get('/wiki/xilero/guide/sample')->assertOk();
        $res->assertSee('wiki-hint wiki-hint-info', false);
        $res->assertSee('<figcaption>A picture</figcaption>', false);
        $res->assertSee('A sample guide subtitle');  // description -> subtitle
    }

    public function test_asset_route_serves_image(): void
    {
        $this->makeWikiFixture('xilero');

        $this->get('/wiki/xilero/assets/pic.png')
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }

    public function test_unavailable_server_shows_coming_soon_not_500(): void
    {
        config(['wiki.servers.xileretro.path' => null]); // force unavailable
        $this->get('/wiki/xileretro')->assertOk()->assertSee('coming soon', false);
    }

    public function test_unknown_server_404s(): void
    {
        $this->get('/wiki/nope')->assertNotFound();
    }

    public function test_path_traversal_404s(): void
    {
        $this->makeWikiFixture('xilero');
        $this->get('/wiki/xilero/assets/..%2f..%2fREADME.md')->assertNotFound();
    }
}
