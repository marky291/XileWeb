<?php

namespace Tests\Unit\Wiki;

use App\Services\Wiki\WikiMarkdownRenderer;
use Tests\TestCase;

class WikiMarkdownRendererTest extends TestCase
{
    private function render(string $md, string $server = 'xilero'): string
    {
        return app(WikiMarkdownRenderer::class)->toHtml($md, $server);
    }

    public function test_renders_hint_block_as_themed_callout_with_inner_markdown(): void
    {
        $html = $this->render("{% hint style=\"warning\" %}\nBe **careful**.\n{% endhint %}");

        $this->assertStringContainsString('wiki-hint wiki-hint-warning', $html);
        $this->assertStringContainsString('<strong>careful</strong>', $html);
    }

    public function test_passes_through_figure_html(): void
    {
        $html = $this->render('<figure><img src="x.png"><figcaption>Cap</figcaption></figure>');

        $this->assertStringContainsString('<figure>', $html);
        $this->assertStringContainsString('<figcaption>Cap</figcaption>', $html);
    }

    public function test_rewrites_gitbook_asset_urls_to_the_asset_route(): void
    {
        $html = $this->render('![pic](../.gitbook/assets/pic.png)', 'xilero');

        $this->assertStringContainsString('/wiki/xilero/assets/pic.png', $html);
        $this->assertStringNotContainsString('.gitbook/assets', $html);
    }
}
