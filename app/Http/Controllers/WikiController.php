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
