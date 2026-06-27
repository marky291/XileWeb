<?php
// app/Services/Wiki/SummaryParser.php

namespace App\Services\Wiki;

class SummaryParser
{
    public function parse(string $summary, string $server): array
    {
        $sections     = [];
        $currentIndex = null;
        $currentFlat  = [];

        foreach (preg_split('/\R/', $summary) as $line) {
            // Section heading: ## Title
            if (preg_match('/^##\s+(.+?)\s*$/', $line, $m)) {
                if ($currentIndex !== null) {
                    $sections[$currentIndex]['items'] = $this->buildTree($currentFlat);
                }
                $sections[]   = ['title' => $m[1], 'items' => []];
                $currentIndex = array_key_last($sections);
                $currentFlat  = [];
                continue;
            }

            // List item: optional indent, * or -, [label](href)
            if ($currentIndex !== null && preg_match('/^(\s*)[\*\-]\s+\[(.+?)\]\((.+?)\)/', $line, $m)) {
                $currentFlat[] = [
                    'label'    => $m[2],
                    'url'      => $this->url($server, $m[3]),
                    'depth'    => (int) floor(strlen($m[1]) / 2),
                    'children' => [],
                ];
            }
        }

        if ($currentIndex !== null) {
            $sections[$currentIndex]['items'] = $this->buildTree($currentFlat);
        }

        return $sections;
    }

    /**
     * Fold a flat list (each item has a 'depth' key) into a nested tree.
     *
     * $childrenRefs[$d] is a reference to the children array at depth $d.
     * Appending to $childrenRefs[$d] goes directly into the tree, then we
     * advance $childrenRefs[$d+1] to point at the new item's children array,
     * ready for the next deeper item.
     */
    private function buildTree(array $flat): array
    {
        $tree         = [];
        $childrenRefs = [0 => &$tree];

        foreach ($flat as $it) {
            $d = $it['depth'];
            unset($it['depth']);
            $childrenRefs[$d][] = $it;
            $lastKey             = array_key_last($childrenRefs[$d]);
            $childrenRefs[$d + 1] = &$childrenRefs[$d][$lastKey]['children'];
        }

        return $tree;
    }

    private function url(string $server, string $href): string
    {
        $slug = preg_replace('/\.md$/', '', trim($href));
        $slug = preg_replace('#(^|/)README$#', '', $slug);
        $slug = trim($slug, '/');

        return $slug === '' ? "/wiki/{$server}" : "/wiki/{$server}/{$slug}";
    }
}
