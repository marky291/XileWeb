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
