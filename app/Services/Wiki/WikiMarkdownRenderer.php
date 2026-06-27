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

    private function rewriteAssetUrls(string $html, string $server): string
    {
        return preg_replace(
            '#(src|href)="[^"]*?\.gitbook/assets/([^"]+)"#',
            '$1="/wiki/' . $server . '/assets/$2"',
            $html
        );
    }
}
