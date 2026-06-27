<?php
// app/Services/Wiki/WikiMarkdownRenderer.php

namespace App\Services\Wiki;

use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class WikiMarkdownRenderer
{
    private ?MarkdownRenderer $configured = null;

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
        // GitHub-style emoji shortcodes (:coin: :heart: …) → unicode, like GitBook.
        $markdown = $this->emojify($markdown);

        // Pull <figure> blocks out before parsing: GitBook's exported
        // <figure><img><figcaption></figure> markup confuses CommonMark's
        // block scanner and prevents following GFM tables from parsing. We
        // restore them verbatim into the final HTML afterwards.
        [$markdown, $figures] = $this->protectFigures($markdown);

        // Pull GitBook footnote definitions ([^N]: ...) out of the body; they
        // become hover tooltips on the matching <a data-footnote-ref> links
        // (GitBook's item-info-on-hover behavior) instead of a footnote list.
        [$markdown, $footnotes] = $this->extractFootnotes($markdown);

        $markdown = $this->renderHints($markdown);
        $html = $this->renderer()->toHtml($markdown);
        $html = $this->restoreFigures($html, $figures);
        $html = $this->applyFootnoteTooltips($html, $footnotes);

        return $this->rewriteAssetUrls($html, $server);
    }

    /**
     * Extract `[^N]: definition` lines and remove them from the body.
     *
     * @return array{0: string, 1: array<string, string>}
     */
    private function extractFootnotes(string $markdown): array
    {
        $defs = [];

        $markdown = preg_replace_callback(
            '/^\[\^([0-9]+)\]:[ \t]*(.+)$/m',
            function (array $m) use (&$defs): string {
                $defs[$m[1]] = trim($m[2]);

                return '';
            },
            $markdown
        );

        return [$markdown, $defs];
    }

    /**
     * Turn each <a data-footnote-ref href="#user-content-fn-N">label</a> into a
     * hover tooltip carrying footnote N's rendered definition.
     *
     * @param array<string, string> $defs
     */
    private function applyFootnoteTooltips(string $html, array $defs): string
    {
        if ($defs === []) {
            return $html;
        }

        return preg_replace_callback(
            '/<a\b([^>]*\bdata-footnote-ref\b[^>]*)>(.*?)<\/a>/is',
            function (array $m) use ($defs): string {
                $label = $m[2];
                if (! preg_match('/#user-content-fn-([0-9]+)/', $m[1], $idMatch)) {
                    return $label;
                }

                $id = $idMatch[1];
                if (! isset($defs[$id])) {
                    return $label;
                }

                // Render the definition (markdown + <br>) and unwrap the <p>.
                $pop = trim($this->renderer()->toHtml($defs[$id]));
                $pop = preg_replace('#^<p>(.*)</p>$#is', '$1', $pop);

                return '<span class="wiki-fn">'
                    . '<a class="wiki-fn-ref" tabindex="0">' . $label . '</a>'
                    . '<span class="wiki-fn-pop" role="tooltip">' . $pop . '</span>'
                    . '</span>';
            },
            $html
        );
    }

    /**
     * Replace each <figure>…</figure> with a placeholder HTML-block div and
     * return the captured originals keyed by index.
     *
     * @return array{0: string, 1: array<int, string>}
     */
    private function protectFigures(string $markdown): array
    {
        $figures = [];

        $markdown = preg_replace_callback(
            '/<figure>.*?<\/figure>/is',
            function (array $m) use (&$figures): string {
                $i = count($figures);
                $figures[] = $m[0];

                return "\n\n<div data-wiki-figure=\"{$i}\"></div>\n\n";
            },
            $markdown
        );

        return [$markdown, $figures];
    }

    private function restoreFigures(string $html, array $figures): string
    {
        foreach ($figures as $i => $original) {
            $html = str_replace("<div data-wiki-figure=\"{$i}\"></div>", $original, $html);
        }

        return $html;
    }

    private function renderer(): MarkdownRenderer
    {
        if ($this->configured !== null) {
            return $this->configured;
        }

        // GitHub-flavored extensions to match GitBook: tables, ~~strikethrough~~,
        // bare-URL autolinks, and - [ ] task lists. (spatie already supplies
        // heading anchors/ids and Shiki code highlighting.) Configured once so
        // the per-hint render calls don't re-add extensions.
        return $this->configured = $this->markdown
            ->commonmarkOptions([
                'html_input' => 'allow',
                'allow_unsafe_links' => false,
            ])
            ->addExtension(new TableExtension())
            ->addExtension(new StrikethroughExtension())
            ->addExtension(new AutolinkExtension())
            ->addExtension(new TaskListExtension());
    }

    /**
     * Replace each {% hint %}…{% endhint %} with a block-level callout div.
     * The hint's inner content is markdown, so it is rendered to HTML first;
     * the emitted <div> is then passed through verbatim by html_input: allow.
     *
     * The div MUST be surrounded by blank lines (\n\n) and start at column 0
     * so CommonMark treats it as an HTML block and passes it through verbatim.
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

    /** Replace :shortcode: with the mapped emoji; leave unknown names untouched. */
    private function emojify(string $markdown): string
    {
        static $map;
        $map ??= require __DIR__ . '/emoji-shortcodes.php';

        return preg_replace_callback(
            '/:([a-z0-9_+-]+):/',
            fn (array $m) => $map[$m[1]] ?? $m[0],
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
