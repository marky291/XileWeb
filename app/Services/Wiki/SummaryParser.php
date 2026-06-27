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
