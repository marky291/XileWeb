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
