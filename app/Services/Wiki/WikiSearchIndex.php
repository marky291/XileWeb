<?php

namespace App\Services\Wiki;

class WikiSearchIndex
{
    public function __construct(
        private WikiRepository $repo,
        private SummaryParser $summary,
        private FrontmatterParser $frontmatter,
    ) {
    }

    /**
     * Build a flat search index across every available wiki server.
     *
     * @return array<int, array{server:string,serverLabel:string,title:string,url:string,description:string,headings:string,content:string}>
     */
    public function build(): array
    {
        $entries = [];

        foreach (config('wiki.servers', []) as $slug => $cfg) {
            if (! $this->repo->isAvailable($slug)) {
                continue;
            }

            $summaryText = $this->repo->readSummary($slug);
            if ($summaryText === null) {
                continue;
            }

            foreach ($this->flatten($this->summary->parse($summaryText, $slug)) as $item) {
                $path = $this->pathFromUrl($item['url'], $slug);
                $raw = $this->repo->readPage($slug, $path);
                if ($raw === null) {
                    continue;
                }

                ['attributes' => $fm, 'body' => $body] = $this->frontmatter->parse($raw);

                $entries[] = [
                    'server' => $slug,
                    'serverLabel' => $cfg['label'] ?? ucfirst($slug),
                    'title' => $item['label'] !== '' ? $item['label'] : ($this->firstHeading($body) ?? ($fm['title'] ?? $slug)),
                    'url' => $item['url'],
                    'description' => $this->emojify((string) ($fm['description'] ?? '')),
                    'headings' => $this->emojify($this->headings($body)),
                    'content' => $this->plainText($body),
                ];
            }
        }

        return $entries;
    }

    /** @return array<int, array{label:string,url:string}> */
    private function flatten(array $sections): array
    {
        $out = [];
        $seen = [];

        $walk = function (array $items) use (&$walk, &$out, &$seen): void {
            foreach ($items as $it) {
                if (! isset($seen[$it['url']])) {
                    $seen[$it['url']] = true;
                    $out[] = ['label' => $it['label'], 'url' => $it['url']];
                }
                if (! empty($it['children'])) {
                    $walk($it['children']);
                }
            }
        };

        foreach ($sections as $section) {
            $walk($section['items']);
        }

        return $out;
    }

    private function pathFromUrl(string $url, string $slug): string
    {
        return ltrim(substr($url, strlen("/wiki/{$slug}")), '/');
    }

    private function firstHeading(string $body): ?string
    {
        return preg_match('/^#\s+(.+)$/m', $body, $m) ? trim($m[1]) : null;
    }

    private function headings(string $body): string
    {
        preg_match_all('/^#{2,4}\s+(.+?)\s*$/m', $body, $m);

        return trim(implode(' · ', array_map('trim', $m[1] ?? [])));
    }

    /** Strip markdown/HTML to searchable plain text, collapsed and length-capped. */
    private function plainText(string $body): string
    {
        $t = preg_replace('/```.*?```/s', ' ', $body);            // fenced code
        $t = preg_replace('/\{%.*?%\}/s', ' ', (string) $t);      // {% hint %} / {% endhint %}
        $t = preg_replace('/<[^>]+>/', ' ', (string) $t);         // html tags
        $t = preg_replace('/!?\[([^\]]*)\]\([^)]*\)/', '$1', (string) $t); // links/images -> text
        $t = preg_replace('/^\[\^[0-9]+\]:.*$/m', ' ', (string) $t); // footnote defs
        $t = $this->emojify((string) $t);                         // :coin: -> 🪙
        $t = preg_replace('/[#>*_`~|]+/', ' ', (string) $t);      // md punctuation
        $t = preg_replace('/\s+/', ' ', (string) $t);             // collapse whitespace

        return mb_substr(trim((string) $t), 0, 4000);
    }

    /** Replace :shortcode: with the mapped emoji (shared map with the renderer). */
    private function emojify(string $text): string
    {
        static $map;
        $map ??= require __DIR__ . '/emoji-shortcodes.php';

        return preg_replace_callback('/:([a-z0-9_+-]+):/', fn ($m) => $map[$m[1]] ?? $m[0], $text);
    }
}
