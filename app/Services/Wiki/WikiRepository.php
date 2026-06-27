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

        $assetsBase = realpath($base . DIRECTORY_SEPARATOR . '.gitbook' . DIRECTORY_SEPARATOR . 'assets');
        if ($assetsBase === false) {
            return null;
        }
        $full = $this->safePath($assetsBase, $file);

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
