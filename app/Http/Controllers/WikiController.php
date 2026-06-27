<?php

namespace App\Http\Controllers;

use App\Services\Wiki\FrontmatterParser;
use App\Services\Wiki\SummaryParser;
use App\Services\Wiki\WikiMarkdownRenderer;
use App\Services\Wiki\WikiRepository;
use App\Services\Wiki\WikiSearchIndex;
use Illuminate\Support\Facades\Cache;
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

        $title = $this->extractTitle($body) ?? ($fm['title'] ?? config("wiki.servers.{$server}.label") . ' Wiki');
        $nav = ($s = $this->repo->readSummary($server)) ? $this->summary->parse($s, $server) : [];

        return view('wiki.show', [
            'server' => $server,
            'title' => $title,
            'subtitle' => $fm['description'] ?? null,
            // The first H1 is shown as the page title above the body — strip it
            // from the rendered content so GitBook's single-title look is kept.
            'html' => $this->renderer->toHtml($this->stripLeadingH1($body), $server),
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

    public function searchIndex(WikiSearchIndex $index)
    {
        // Scanning every page is expensive; cache the built index (cleared by
        // `php artisan cache:clear` / `optimize:clear` on content deploys).
        $data = Cache::remember('wiki.search.index', now()->addMinutes(10), fn () => $index->build());

        return response()->json($data)->header('Cache-Control', 'public, max-age=300');
    }

    private function extractTitle(string $body): ?string
    {
        return preg_match('/^#\s+(.+)$/m', $body, $m) ? trim($m[1]) : null;
    }

    private function stripLeadingH1(string $body): string
    {
        // Remove the first level-1 heading line (the one used as the page title).
        return preg_replace('/^#\s+.*$\R?/m', '', $body, 1) ?? $body;
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
