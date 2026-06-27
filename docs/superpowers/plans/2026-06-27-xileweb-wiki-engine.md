# XileWeb Wiki Engine Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Render the existing GitBook markdown of two game servers (XileRO, XileRetro) as a self-hosted wiki inside the XileWeb Laravel site, reading the content in place from each server repo's `gitbook/` folder.

**Architecture:** Path-prefixed routes `/wiki/{server}/{path}` resolve a configured `gitbook/` base path per server. Five focused units (repository, two parsers, a markdown renderer, a controller) turn GitBook markdown — including `{% hint %}`, `<figure>`, `<details>`, frontmatter, and `.gitbook/assets` images — into a GitBook-style 3-pane page skinned in the XileRO theme. No database, no editor; content syncs by files.

**Tech Stack:** Laravel 12, PHP 8.4, `spatie/laravel-markdown` (league/commonmark), Symfony YAML, Tailwind 4 + Typography, PHPUnit.

## Global Constraints

- **No local PHP on the Windows host.** Run all `php artisan` / test commands inside a PHP 8.4 container. A preview container already exists; the canonical runner for this plan is: `docker compose -f <scratchpad>/xileweb-wiki/docker-compose.yml exec xileweb php artisan ...`. If unavailable, use any `php:8.4` container with the repo mounted at `/app`. Substitute `php artisan` accordingly in every step below.
- **Repo:** all work is in `D:\XileWeb` (the website), not `D:\XileRO`.
- **Server slugs:** exactly `xilero` and `xileretro`. The engine is generic over the slug — no per-server branching in code.
- **Content is read-only and in place.** Never write into the `gitbook/` source folders. Content source = `config('wiki.servers.<slug>.path')` (env `WIKI_<SLUG>_PATH`).
- **HTML passthrough is scoped to wiki rendering only**, via `commonmarkOptions(['html_input' => 'allow', 'allow_unsafe_links' => false])`.
- **Tests must not depend on `D:\XileRO`.** Each test builds a temporary fixture `gitbook/` tree and points config at it.
- **Tests are PHPUnit classes** extending `Tests\TestCase` (per project CLAUDE.md). Run with `php artisan test`.

---

### Task 1: Wiki configuration

**Files:**
- Create: `config/wiki.php`
- Modify: `.env.example` (append wiki path vars)
- Test: `tests/Unit/Wiki/WikiConfigTest.php`

**Interfaces:**
- Produces: `config('wiki.servers')` → array keyed by slug, each `['label'=>string,'rate'=>string,'path'=>?string]`; `config('wiki.default')` → string slug.

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=WikiConfigTest`
Expected: FAIL (config file does not exist; `config('wiki.servers')` is null).

- [ ] **Step 3: Create the config file**

```php
<?php
// config/wiki.php

return [
    'servers' => [
        'xilero' => [
            'label' => 'XileRO',
            'rate'  => 'Mid-Rate',
            'path'  => env('WIKI_XILERO_PATH'),
        ],
        'xileretro' => [
            'label' => 'XileRetro',
            'rate'  => 'High-Rate',
            'path'  => env('WIKI_XILERETRO_PATH'),
        ],
    ],

    'default' => 'xilero',
];
```

- [ ] **Step 4: Append env documentation**

Append to `.env.example`:

```ini

# Wiki — absolute path to each game-server repo's gitbook/ folder.
# Dev: only XileRO is cloned locally; XileRetro shows "coming soon" until set.
# Live: clone each server repo onto the XileWeb host and point these at gitbook/.
WIKI_XILERO_PATH=
WIKI_XILERETRO_PATH=
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=WikiConfigTest`
Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add config/wiki.php .env.example tests/Unit/Wiki/WikiConfigTest.php
git commit -m "feat(wiki): add per-server wiki config (gitbook path mapping)"
```

---

### Task 2: Test fixture trait

A reusable helper that writes a temporary GitBook-shaped `gitbook/` tree and points `config('wiki.servers.xilero.path')` at it. Every later test uses it, so it is built and verified once here.

**Files:**
- Create: `tests/Wiki/CreatesWikiFixture.php`
- Test: `tests/Unit/Wiki/CreatesWikiFixtureTest.php`

**Interfaces:**
- Produces: trait `Tests\Wiki\CreatesWikiFixture` with `string $fixturePath` and method `makeWikiFixture(string $server = 'xilero'): string` (creates the tree, sets config, returns base path); and `tearDownWikiFixture(): void` (removes the temp dir).

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=CreatesWikiFixtureTest`
Expected: FAIL ("Trait ... not found").

- [ ] **Step 3: Create the trait**

```php
<?php
// tests/Wiki/CreatesWikiFixture.php

namespace Tests\Wiki;

trait CreatesWikiFixture
{
    protected ?string $fixturePath = null;

    protected function makeWikiFixture(string $server = 'xilero'): string
    {
        $base = sys_get_temp_dir() . '/wiki-fixture-' . uniqid();
        $this->fixturePath = $base;

        @mkdir($base . '/guide', 0777, true);
        @mkdir($base . '/.gitbook/assets', 0777, true);

        file_put_contents($base . '/README.md', <<<MD
        ---
        description: The landing page description
        cover: .gitbook/assets/pic.png
        ---

        # Welcome

        Intro paragraph.
        MD);

        file_put_contents($base . '/SUMMARY.md', <<<MD
        # Table of contents

        ## Start

        * [Welcome](README.md)

        ## Guides

        * [Sample Guide](guide/sample.md)
        MD);

        file_put_contents($base . '/guide/sample.md', <<<MD
        ---
        description: A sample guide subtitle
        ---

        # Sample Guide

        Body text.

        {% hint style="info" %}
        A **hinted** note.
        {% endhint %}

        <figure><img src="../.gitbook/assets/pic.png" alt="Pic"><figcaption>A picture</figcaption></figure>
        MD);

        // 1x1 transparent PNG
        file_put_contents(
            $base . '/.gitbook/assets/pic.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=')
        );

        config(["wiki.servers.{$server}.path" => $base]);

        return $base;
    }

    protected function tearDownWikiFixture(): void
    {
        if ($this->fixturePath && is_dir($this->fixturePath)) {
            $it = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->fixturePath, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($it as $f) {
                $f->isDir() ? @rmdir($f->getPathname()) : @unlink($f->getPathname());
            }
            @rmdir($this->fixturePath);
        }
        $this->fixturePath = null;
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=CreatesWikiFixtureTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add tests/Wiki/CreatesWikiFixture.php tests/Unit/Wiki/CreatesWikiFixtureTest.php
git commit -m "test(wiki): add reusable gitbook fixture trait"
```

---

### Task 3: FrontmatterParser

**Files:**
- Create: `app/Services/Wiki/FrontmatterParser.php`
- Test: `tests/Unit/Wiki/FrontmatterParserTest.php`

**Interfaces:**
- Produces: `FrontmatterParser::parse(string $raw): array` → `['attributes' => array, 'body' => string]`. No leading frontmatter → `['attributes' => [], 'body' => $raw]`.

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=FrontmatterParserTest`
Expected: FAIL ("Class ... not found").

- [ ] **Step 3: Write the implementation**

```php
<?php
// app/Services/Wiki/FrontmatterParser.php

namespace App\Services\Wiki;

use Symfony\Component\Yaml\Yaml;

class FrontmatterParser
{
    public function parse(string $raw): array
    {
        // Frontmatter must be the very first thing in the file.
        if (! preg_match('/^---\R(.*?)\R---\R?(.*)$/s', $raw, $m)) {
            return ['attributes' => [], 'body' => $raw];
        }

        try {
            $attributes = Yaml::parse($m[1]) ?? [];
        } catch (\Throwable) {
            $attributes = [];
        }

        if (! is_array($attributes)) {
            $attributes = [];
        }

        return ['attributes' => $attributes, 'body' => ltrim($m[2], "\r\n")];
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=FrontmatterParserTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Services/Wiki/FrontmatterParser.php tests/Unit/Wiki/FrontmatterParserTest.php
git commit -m "feat(wiki): add YAML frontmatter parser"
```

---

### Task 4: SummaryParser

Parses a GitBook `SUMMARY.md` into a sidebar tree. `##` headings become section titles; `*`/`-` list items become links; indentation (2 spaces per level) becomes nesting. `README.md` links map to the wiki root; other links drop `.md`.

**Files:**
- Create: `app/Services/Wiki/SummaryParser.php`
- Test: `tests/Unit/Wiki/SummaryParserTest.php`

**Interfaces:**
- Produces: `SummaryParser::parse(string $summary, string $server): array` → list of sections `['title' => ?string, 'items' => Item[]]`, where `Item = ['label' => string, 'url' => string, 'children' => Item[]]`. `url` is `/wiki/{server}` for README, else `/wiki/{server}/{slug}` (slug = link path without `.md`).

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=SummaryParserTest`
Expected: FAIL ("Class ... not found").

- [ ] **Step 3: Write the implementation**

```php
<?php
// app/Services/Wiki/SummaryParser.php

namespace App\Services\Wiki;

class SummaryParser
{
    public function parse(string $summary, string $server): array
    {
        $sections = [];
        $current = null;          // current section (by reference index)
        $stack = [];              // [depth => &items array] for nesting

        foreach (preg_split('/\R/', $summary) as $line) {
            // Section heading: ## Title
            if (preg_match('/^##\s+(.+?)\s*$/', $line, $m)) {
                $sections[] = ['title' => $m[1], 'items' => []];
                $current = array_key_last($sections);
                $stack = [];
                continue;
            }

            // List item: optional indent, * or -, [label](href)
            if ($current !== null && preg_match('/^(\s*)[\*\-]\s+\[(.+?)\]\((.+?)\)/', $line, $m)) {
                $depth = (int) floor(strlen($m[1]) / 2);
                $item = ['label' => $m[2], 'url' => $this->url($server, $m[3]), 'children' => []];

                if ($depth === 0) {
                    $sections[$current]['items'][] = $item;
                    $stack = [0 => &$sections[$current]['items']];
                    $last = array_key_last($sections[$current]['items']);
                    $stack[1] = &$sections[$current]['items'][$last]['children'];
                } else {
                    $parent = $stack[$depth] ?? $stack[array_key_last($stack)];
                    $parent[] = $item;
                    $lastKey = array_key_last($parent);
                    $stack[$depth + 1] = &$parent[$lastKey]['children'];
                    // reassign back (PHP arrays are value types)
                    $this->writeBack($sections, $stack, $depth, $parent);
                }
            }
        }

        return $sections;
    }

    private function url(string $server, string $href): string
    {
        $slug = preg_replace('/\.md$/', '', trim($href));
        $slug = preg_replace('#(^|/)README$#', '', $slug);
        $slug = trim($slug, '/');

        return $slug === '' ? "/wiki/{$server}" : "/wiki/{$server}/{$slug}";
    }

    /**
     * PHP arrays are copied by value; nested children appended via a temp
     * reference must be written back into the section tree.
     */
    private function writeBack(array &$sections, array &$stack, int $depth, array $parent): void
    {
        $stack[$depth] = $parent;
    }
}
```

> Implementer note: PHP's value-array semantics make deep nesting with a
> reference stack fragile. If a nesting test fails, replace the reference-stack
> body with the simpler recursive approach below (same public signature and
> return shape) and keep the tests unchanged:
>
> ```php
> // Alternative: build a flat list with depths, then nest.
> // 1) collect items as ['label','url','depth'] in order under each section
> // 2) fold into a tree with a recursive helper that attaches an item of
> //    depth d+1 as a child of the most recent item of depth d.
> ```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=SummaryParserTest`
Expected: PASS. (If the nesting assertion fails, apply the implementer note's flat-then-nest approach, then re-run — PASS.)

- [ ] **Step 5: Commit**

```bash
git add app/Services/Wiki/SummaryParser.php tests/Unit/Wiki/SummaryParserTest.php
git commit -m "feat(wiki): parse SUMMARY.md into a sidebar nav tree"
```

---

### Task 5: WikiRepository (content access + path safety)

**Files:**
- Create: `app/Services/Wiki/WikiRepository.php`
- Test: `tests/Unit/Wiki/WikiRepositoryTest.php`

**Interfaces:**
- Produces:
  - `hasServer(string $server): bool` — slug is configured.
  - `isAvailable(string $server): bool` — configured path exists on disk.
  - `readPage(string $server, string $path): ?string` — raw markdown for `{path}.md`, `{path}/README.md`, or (empty path) `README.md`; null if missing/escaping.
  - `readSummary(string $server): ?string` — raw `SUMMARY.md`, or null.
  - `resolveAsset(string $server, string $file): ?string` — absolute fs path of an allowed image under `.gitbook/assets/`, or null.

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=WikiRepositoryTest`
Expected: FAIL ("Class ... not found").

- [ ] **Step 3: Write the implementation**

```php
<?php
// app/Services/Wiki/WikiRepository.php

namespace App\Services\Wiki;

class WikiRepository
{
    private const ASSET_EXT = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp'];

    public function hasServer(string $server): bool
    {
        return config("wiki.servers.{$server}") !== null;
    }

    public function isAvailable(string $server): bool
    {
        $base = $this->base($server);

        return $base !== null && is_dir($base);
    }

    public function readPage(string $server, string $path): ?string
    {
        $base = $this->base($server);
        if ($base === null) {
            return null;
        }

        $path = trim($path, '/');
        $candidates = $path === '' ? ['README.md'] : ["{$path}.md", "{$path}/README.md"];

        foreach ($candidates as $rel) {
            $full = $this->safePath($base, $rel);
            if ($full !== null && is_file($full)) {
                return file_get_contents($full);
            }
        }

        return null;
    }

    public function readSummary(string $server): ?string
    {
        $base = $this->base($server);
        if ($base === null) {
            return null;
        }

        $full = $this->safePath($base, 'SUMMARY.md');

        return ($full !== null && is_file($full)) ? file_get_contents($full) : null;
    }

    public function resolveAsset(string $server, string $file): ?string
    {
        $base = $this->base($server);
        if ($base === null) {
            return null;
        }

        $file = urldecode($file);
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (! in_array($ext, self::ASSET_EXT, true)) {
            return null;
        }

        $full = $this->safePath($base, '.gitbook/assets/' . $file);

        return ($full !== null && is_file($full)) ? $full : null;
    }

    private function base(string $server): ?string
    {
        $path = config("wiki.servers.{$server}.path");

        return $path ? rtrim($path, "/\\") : null;
    }

    /**
     * Resolve $base/$rel and guarantee the result stays within $base.
     * Returns the canonical path, or null if missing or escaping.
     */
    private function safePath(string $base, string $rel): ?string
    {
        $realBase = realpath($base);
        $real = realpath($base . DIRECTORY_SEPARATOR . $rel);

        if ($realBase === false || $real === false) {
            return null;
        }

        if ($real !== $realBase && ! str_starts_with($real, $realBase . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $real;
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=WikiRepositoryTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Services/Wiki/WikiRepository.php tests/Unit/Wiki/WikiRepositoryTest.php
git commit -m "feat(wiki): add content repository with path-traversal safety"
```

---

### Task 6: WikiMarkdownRenderer (hints, HTML passthrough, asset rewrite)

**Files:**
- Create: `app/Services/Wiki/WikiMarkdownRenderer.php`
- Test: `tests/Unit/Wiki/WikiMarkdownRendererTest.php`

**Interfaces:**
- Consumes: `Spatie\LaravelMarkdown\MarkdownRenderer` (constructor-injected; resolved from container).
- Produces: `WikiMarkdownRenderer::toHtml(string $markdown, string $server): string`.

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=WikiMarkdownRendererTest`
Expected: FAIL ("Class ... not found").

- [ ] **Step 3: Write the implementation**

```php
<?php
// app/Services/Wiki/WikiMarkdownRenderer.php

namespace App\Services\Wiki;

use Spatie\LaravelMarkdown\MarkdownRenderer;

class WikiMarkdownRenderer
{
    /** Inline SVG icons per hint style. */
    private const ICONS = [
        'info'    => '<svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/></svg>',
        'warning' => '<svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M8.257 3.1c.765-1.36 2.72-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/></svg>',
        'danger'  => '<svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.7 7.3a1 1 0 011.4 0L10 8.6l1.3-1.3a1 1 0 011.4 1.4L11.4 10l1.3 1.3a1 1 0 01-1.4 1.4L10 11.4l-1.3 1.3a1 1 0 01-1.4-1.4L8.6 10 7.3 8.7a1 1 0 010-1.4z"/></svg>',
        'success' => '<svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3a1 1 0 00-1.4-1.4L9 10.6 7.7 9.3a1 1 0 00-1.4 1.4l2 2a1 1 0 001.4 0l4-4z"/></svg>',
    ];

    public function __construct(private MarkdownRenderer $markdown)
    {
    }

    public function toHtml(string $markdown, string $server): string
    {
        $markdown = $this->renderHints($markdown);
        $html = $this->renderer()->toHtml($markdown);

        return $this->rewriteAssetUrls($html, $server);
    }

    private function renderer(): MarkdownRenderer
    {
        return $this->markdown->commonmarkOptions([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Replace each {% hint %}…{% endhint %} with a block-level callout div.
     * The hint's inner content is markdown, so it is rendered to HTML first;
     * the emitted <div> is then passed through verbatim by html_input: allow.
     */
    private function renderHints(string $markdown): string
    {
        return preg_replace_callback(
            '/\{%\s*hint\s+style="(?<style>\w+)"\s*%\}(?<body>.*?)\{%\s*endhint\s*%\}/s',
            function (array $m): string {
                $style = in_array($m['style'], ['info', 'warning', 'danger', 'success'], true) ? $m['style'] : 'info';
                $inner = $this->renderer()->toHtml(trim($m['body']));
                $icon = self::ICONS[$style];

                return "\n\n<div class=\"wiki-hint wiki-hint-{$style}\">"
                    . "<div class=\"wiki-hint-icon\">{$icon}</div>"
                    . "<div class=\"wiki-hint-body\">{$inner}</div>"
                    . "</div>\n\n";
            },
            $markdown
        );
    }

    private function rewriteAssetUrls(string $html, string $server): string
    {
        return preg_replace(
            '#(src|href)="[^"]*?\.gitbook/assets/([^"]+)"#',
            '$1="/wiki/' . $server . '/assets/$2"',
            $html
        );
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=WikiMarkdownRendererTest`
Expected: PASS.

> If `test_renders_hint_block...` shows the inner `<strong>` but the callout
> wrapper got swallowed (CommonMark didn't treat the div as an HTML block),
> ensure the emitted div is surrounded by blank lines (it is, via `\n\n`) and
> starts at column 0. This is the documented HTML-block trigger.

- [ ] **Step 5: Commit**

```bash
git add app/Services/Wiki/WikiMarkdownRenderer.php tests/Unit/Wiki/WikiMarkdownRendererTest.php
git commit -m "feat(wiki): render hints, pass through HTML, rewrite asset URLs"
```

---

### Task 7: WikiController + routes

**Files:**
- Modify: `app/Http/Controllers/WikiController.php` (replace existing class body)
- Modify: `routes/web.php` (replace the two wiki routes from lines ~219-220)
- Test: `tests/Feature/Wiki/WikiRoutesTest.php`

**Interfaces:**
- Consumes: `WikiRepository`, `FrontmatterParser`, `SummaryParser`, `WikiMarkdownRenderer`.
- Produces:
  - `home()` → view `wiki.home` with `servers` (available list).
  - `show(string $server, string $path = '')` → view `wiki.show` with `server,title,subtitle,html,nav,breadcrumbs,currentUrl`; 404 unknown slug/missing page; `wiki.coming-soon` (200) if configured but unavailable.
  - `asset(string $server, string $file)` → file response; 404 if not resolvable.

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=WikiRoutesTest`
Expected: FAIL (routes/views not wired; assertions fail).

- [ ] **Step 3: Replace the controller**

Replace the entire contents of `app/Http/Controllers/WikiController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Services\Wiki\FrontmatterParser;
use App\Services\Wiki\SummaryParser;
use App\Services\Wiki\WikiMarkdownRenderer;
use App\Services\Wiki\WikiRepository;
use Illuminate\Support\Str;

class WikiController extends Controller
{
    public function __construct(
        private WikiRepository $repo,
        private FrontmatterParser $frontmatter,
        private SummaryParser $summary,
        private WikiMarkdownRenderer $renderer,
    ) {
    }

    public function home()
    {
        $servers = collect(config('wiki.servers'))
            ->map(fn ($cfg, $slug) => array_merge($cfg, [
                'slug' => $slug,
                'available' => $this->repo->isAvailable($slug),
                'url' => "/wiki/{$slug}",
            ]))
            ->values()
            ->all();

        return view('wiki.home', ['servers' => $servers]);
    }

    public function show(string $server, string $path = '')
    {
        abort_unless($this->repo->hasServer($server), 404);

        if (! $this->repo->isAvailable($server)) {
            return response()->view('wiki.coming-soon', [
                'server' => $server,
                'label' => config("wiki.servers.{$server}.label", Str::title($server)),
            ]);
        }

        $raw = $this->repo->readPage($server, $path);
        abort_if($raw === null, 404);

        ['attributes' => $fm, 'body' => $body] = $this->frontmatter->parse($raw);

        $nav = ($s = $this->repo->readSummary($server)) ? $this->summary->parse($s, $server) : [];

        return view('wiki.show', [
            'server' => $server,
            'title' => $this->extractTitle($body) ?? ($fm['title'] ?? config("wiki.servers.{$server}.label") . ' Wiki'),
            'subtitle' => $fm['description'] ?? null,
            'html' => $this->renderer->toHtml($body, $server),
            'nav' => $nav,
            'currentUrl' => rtrim('/wiki/' . $server . '/' . trim($path, '/'), '/'),
            'breadcrumbs' => $this->breadcrumbs($server, $path),
        ]);
    }

    public function asset(string $server, string $file)
    {
        abort_unless($this->repo->hasServer($server), 404);
        $abs = $this->repo->resolveAsset($server, $file);
        abort_if($abs === null, 404);

        return response()->file($abs);
    }

    private function extractTitle(string $body): ?string
    {
        return preg_match('/^#\s+(.+)$/m', $body, $m) ? trim($m[1]) : null;
    }

    private function breadcrumbs(string $server, string $path): array
    {
        $crumbs = [['name' => config("wiki.servers.{$server}.label", $server), 'url' => "/wiki/{$server}"]];
        $acc = "/wiki/{$server}";
        foreach (array_filter(explode('/', trim($path, '/'))) as $part) {
            $acc .= "/{$part}";
            $crumbs[] = ['name' => Str::title(str_replace('-', ' ', $part)), 'url' => $acc];
        }

        return $crumbs;
    }
}
```

- [ ] **Step 4: Wire the routes**

In `routes/web.php`, replace the current two wiki route lines (the
`Route::get('/wiki', ...)` and `Route::get('/wiki/{path}', ...)` pair near
line 219) with:

```php
// Wiki routes (file-based, dual-server — see docs/superpowers/specs/2026-06-27-xileweb-wiki-engine-design.md)
Route::get('/wiki', [WikiController::class, 'home'])->name('wiki.home');
Route::get('/wiki/{server}/assets/{file}', [WikiController::class, 'asset'])
    ->where(['server' => '[a-z0-9_-]+', 'file' => '.*'])
    ->name('wiki.asset');
Route::get('/wiki/{server}/{path?}', [WikiController::class, 'show'])
    ->where(['server' => '[a-z0-9_-]+', 'path' => '.*'])
    ->name('wiki.show');
```

(The `WikiController` import at the top of `routes/web.php` already exists.)

- [ ] **Step 5: Run test to verify it fails on views only**

Run: `php artisan test --filter=WikiRoutesTest`
Expected: FAIL now on missing views (`wiki.home`, `wiki.show`, `wiki.coming-soon`) — controller/routes resolve. (Task 8 supplies the views; if you are executing strictly task-by-task, the view-dependent assertions remain red until Task 8. The traversal/unknown-server/asset tests should already PASS.)

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/WikiController.php routes/web.php tests/Feature/Wiki/WikiRoutesTest.php
git commit -m "feat(wiki): controller + path-prefixed dual-server routes"
```

---

### Task 8: Views, 3-pane layout, and styles

Supplies the three views the controller renders and the GitBook-style skin.
Completes Task 7's feature tests.

**Files:**
- Modify: `resources/views/wiki/show.blade.php` (replace with 3-pane layout)
- Create: `resources/views/wiki/partials/nav.blade.php` (recursive sidebar items)
- Create: `resources/views/wiki/home.blade.php` (server chooser)
- Create: `resources/views/wiki/coming-soon.blade.php`
- Modify: `resources/views/wiki/index.blade.php` → delete (superseded by `home.blade.php`)

**Interfaces:**
- Consumes: controller view data from Task 7 (`server,title,subtitle,html,nav,currentUrl,breadcrumbs`; `servers`; `label`).

- [ ] **Step 1: Create the recursive sidebar partial**

```blade
{{-- resources/views/wiki/partials/nav.blade.php --}}
{{-- expects: $items (array of ['label','url','children']), $currentUrl --}}
<ul class="space-y-1">
    @foreach ($items as $item)
        @php $active = ($item['url'] === $currentUrl); @endphp
        <li>
            <a href="{{ $item['url'] }}"
               class="block px-3 py-1.5 rounded text-sm transition-colors
                      {{ $active ? 'bg-amber-600/20 text-amber-400 font-semibold' : 'text-gray-300 hover:text-amber-400 hover:bg-white/5' }}">
                {{ $item['label'] }}
            </a>
            @if (! empty($item['children']))
                <div class="ml-3 mt-1 border-l border-gray-800 pl-2">
                    @include('wiki.partials.nav', ['items' => $item['children'], 'currentUrl' => $currentUrl])
                </div>
            @endif
        </li>
    @endforeach
</ul>
```

- [ ] **Step 2: Replace `show.blade.php` with the 3-pane layout**

```blade
{{-- resources/views/wiki/show.blade.php --}}
<x-app-layout>
    @section('title', config("wiki.servers.$server.label") . ' Wiki: ' . $title)
    @section('description', $subtitle ?? 'XileRO Wiki')

    <div id="reading-progress" class="fixed top-0 left-0 w-0 h-1 bg-amber-500 z-50"></div>

    <div class="bg-gray-950 min-h-screen pt-20 lg:pt-24">
        <div class="max-w-screen-2xl mx-auto px-5 pb-16">

            {{-- Breadcrumbs --}}
            <nav class="flex flex-wrap items-center text-sm text-gray-400 py-4">
                <a href="/wiki" class="hover:text-amber-500">Wiki</a>
                @foreach ($breadcrumbs as $c)
                    <span class="mx-2 text-gray-600">/</span>
                    @if (! $loop->last)
                        <a href="{{ $c['url'] }}" class="hover:text-amber-500">{{ $c['name'] }}</a>
                    @else
                        <span class="text-amber-500 font-semibold">{{ $c['name'] }}</span>
                    @endif
                @endforeach
            </nav>

            <div class="flex flex-col lg:flex-row gap-8">
                {{-- LEFT: SUMMARY sidebar --}}
                <aside class="lg:w-64 lg:flex-shrink-0">
                    <div class="lg:sticky lg:top-24 max-h-[calc(100vh-7rem)] overflow-y-auto pr-2">
                        @foreach ($nav as $section)
                            @if ($section['title'])
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mt-5 mb-2">{{ $section['title'] }}</h3>
                            @endif
                            @include('wiki.partials.nav', ['items' => $section['items'], 'currentUrl' => $currentUrl])
                        @endforeach
                    </div>
                </aside>

                {{-- CENTER: content --}}
                <main class="flex-1 min-w-0">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-100 mb-2">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="text-lg text-gray-400 mb-8">{{ $subtitle }}</p>
                    @endif
                    <article class="prose prose-invert prose-lg max-w-none wiki-content">
                        {!! $html !!}
                    </article>
                </main>
            </div>
        </div>
    </div>

    @include('wiki.partials.styles')
</x-app-layout>
```

- [ ] **Step 3: Create the styles partial**

```blade
{{-- resources/views/wiki/partials/styles.blade.php --}}
<style>
    /* GitBook-style hint callouts */
    .wiki-hint { display:flex; gap:.75rem; padding:1rem 1.25rem; margin:1.25rem 0; border-radius:.5rem; border-left:4px solid; }
    .wiki-hint-icon { flex-shrink:0; margin-top:.15rem; }
    .wiki-hint-body > :first-child { margin-top:0; }
    .wiki-hint-body > :last-child { margin-bottom:0; }
    .wiki-hint-info    { background:rgba(59,130,246,.10); border-color:#3b82f6; color:#bfdbfe; }
    .wiki-hint-warning { background:rgba(245,158,11,.10); border-color:#f59e0b; color:#fde68a; }
    .wiki-hint-danger  { background:rgba(239,68,68,.10);  border-color:#ef4444; color:#fecaca; }
    .wiki-hint-success { background:rgba(34,197,94,.10);  border-color:#22c55e; color:#bbf7d0; }
    .wiki-hint-info .wiki-hint-icon    { color:#3b82f6; }
    .wiki-hint-warning .wiki-hint-icon { color:#f59e0b; }
    .wiki-hint-danger .wiki-hint-icon  { color:#ef4444; }
    .wiki-hint-success .wiki-hint-icon { color:#22c55e; }

    /* Figures */
    .wiki-content figure { text-align:center; margin:1.5rem 0; }
    .wiki-content figure img { display:inline-block; border-radius:.5rem; }
    .wiki-content figcaption { margin-top:.5rem; font-size:.875rem; color:#9ca3af; }

    /* Details/summary */
    .wiki-content details { border:1px solid #374151; border-radius:.5rem; padding:.5rem 1rem; margin:1rem 0; background:rgba(255,255,255,.02); }
    .wiki-content summary { cursor:pointer; font-weight:600; color:#fbbf24; }
</style>
```

- [ ] **Step 4: Create the home (server chooser) view**

```blade
{{-- resources/views/wiki/home.blade.php --}}
<x-app-layout>
    @section('title', 'XileRO Wiki')

    <section class="bg-gray-950 min-h-screen pt-24 pb-16">
        <div class="max-w-screen-lg mx-auto px-5 text-center">
            <h1 class="text-5xl font-bold text-gray-100 mb-4">XileRO <span class="text-amber-500">Wiki</span></h1>
            <p class="text-xl text-gray-400 mb-12">Choose your server</p>

            <div class="grid md:grid-cols-2 gap-6">
                @foreach ($servers as $s)
                    <a href="{{ $s['url'] }}"
                       class="block bg-gray-900 border border-gray-800 hover:border-amber-500 rounded-lg p-8 transition-colors {{ $s['available'] ? '' : 'opacity-60' }}">
                        <div class="text-3xl font-bold text-amber-500 mb-2">{{ $s['label'] }}</div>
                        <div class="text-gray-400">{{ $s['rate'] }}</div>
                        @unless ($s['available'])
                            <div class="mt-3 text-sm text-gray-500">coming soon</div>
                        @endunless
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>
```

- [ ] **Step 5: Create the coming-soon view**

```blade
{{-- resources/views/wiki/coming-soon.blade.php --}}
<x-app-layout>
    @section('title', $label . ' Wiki — Coming Soon')

    <section class="bg-gray-950 min-h-screen pt-24 pb-16">
        <div class="max-w-screen-md mx-auto px-5 text-center">
            <h1 class="text-4xl font-bold text-gray-100 mb-4">{{ $label }} Wiki</h1>
            <p class="text-xl text-gray-400">This wiki is <span class="text-amber-500">coming soon</span>.</p>
            <a href="/wiki" class="inline-block mt-8 text-amber-500 hover:underline">← Back to wiki home</a>
        </div>
    </section>
</x-app-layout>
```

- [ ] **Step 6: Delete the superseded index view**

```bash
git rm resources/views/wiki/index.blade.php
```

- [ ] **Step 7: Run the feature tests to verify they pass**

Run: `php artisan test --filter=WikiRoutesTest`
Expected: PASS (all assertions, including sidebar, hint, figure, subtitle, asset, coming-soon, traversal).

- [ ] **Step 8: Run the full wiki suite**

Run: `php artisan test --filter=Wiki`
Expected: PASS (all unit + feature wiki tests).

- [ ] **Step 9: Commit**

```bash
git add resources/views/wiki/ tests/
git commit -m "feat(wiki): 3-pane GitBook-style views skinned in the XileRO theme"
```

---

### Task 9: Dev wiring, real-content validation, and asset build

Point the dev env at the real XileRO content and verify end-to-end against the
actual 50 pages (the fixtures only prove the mechanics).

**Files:**
- Modify: `.env` (dev only; git-ignored) — set `WIKI_XILERO_PATH`
- No code changes expected; this task is validation.

- [ ] **Step 1: Set the dev content path**

In the running preview container's environment / `.env`:

```ini
WIKI_XILERO_PATH=/xilero-gitbook
```

Mount the real content read-only into the container by adding to the preview
`docker-compose.yml` service volumes:

```yaml
      - "D:/XileRO/rathena/gitbook:/xilero-gitbook:ro"
```

Then `docker compose up -d` to recreate, and `php artisan config:clear`.

- [ ] **Step 2: Verify the landing and a content page render**

Run:
```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/wiki/xilero
curl -s http://localhost:8080/wiki/xilero/mechanics/rookie-path | grep -c "wiki-hint"
curl -s -o /dev/null -w "%{http_code}\n" "http://localhost:8080/wiki/xilero/assets/npc_syri.png"
```
Expected: `200`, a count `>= 1` (hints render), `200` (asset served).

- [ ] **Step 3: Spot-check three pages in the browser**

Open and visually confirm sidebar + content + hints + figures:
- http://localhost:8080/wiki/xilero
- http://localhost:8080/wiki/xilero/quests/excalibur-quest
- http://localhost:8080/wiki/xilero/mechanics/auto-attack

- [ ] **Step 4: Confirm XileRetro degrades gracefully**

Run: `curl -s http://localhost:8080/wiki/xileretro | grep -c "coming soon"`
Expected: `1` (no 500).

- [ ] **Step 5: Commit any dev-compose changes (not .env)**

```bash
git add docs/superpowers/plans/2026-06-27-xileweb-wiki-engine.md
git commit -m "docs(wiki): record dev wiring validation steps"
```

---

## Self-Review

**Spec coverage:**
- Routing (§3) → Task 7. Config/env (§4) → Task 1. Five components (§5) → Tasks 3-7. Rendering pipeline + GitBook-isms (§6-7) → Tasks 6, 8. Layout (§8) → Task 8. Security (§9) → Task 5 (+ Task 7 traversal test). Deployment (§10) → documented; Task 9 validates dev. Phasing (§11): Phase 1 only — search/prev-next/edit-on-git/icon correctly deferred. Testing (§12) → Tasks 1-8 tests. Risks (§13): hint inner-markdown handled in Task 6 with a fallback note; `<details>` styling in Task 8 (its nested-markdown caveat is content-authoring, surfaced in Task 9 spot-check).
- All spec sections map to a task. No gaps.

**Placeholder scan:** No "TBD/TODO". The SummaryParser carries a labeled fallback approach (a deliberate implementer aid, with full guidance), not a placeholder. All code steps include complete code.

**Type consistency:** `WikiRepository` methods (`hasServer/isAvailable/readPage/readSummary/resolveAsset`), `FrontmatterParser::parse` → `['attributes','body']`, `SummaryParser::parse` → sections of `['title','items'=>['label','url','children']]`, `WikiMarkdownRenderer::toHtml($md,$server)`, controller view keys (`server,title,subtitle,html,nav,currentUrl,breadcrumbs`) — all consistent across producers and consumers.
