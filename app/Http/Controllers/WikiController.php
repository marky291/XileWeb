<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class WikiController extends Controller
{
    public function show(Request $request, $path = 'index')
    {
        // Sanitize the path to prevent directory traversal
        $path = str_replace(['..', '//'], '', $path);
        $path = trim($path, '/');

        // Default to index.md if no path specified
        if (empty($path)) {
            $path = 'index';
        }

        // Special handling for wiki index page
        if ($path === 'index') {
            return view('wiki.index');
        }

        // Build the full file path
        $filePath = resource_path("wiki/{$path}.md");

        // Check if file exists
        if (! File::exists($filePath)) {
            // Try checking if it's a directory with an index.md
            $indexPath = resource_path("wiki/{$path}/index.md");
            if (File::exists($indexPath)) {
                $filePath = $indexPath;
            } else {
                abort(404, 'Wiki page not found');
            }
        }

        // Read the markdown content
        $content = File::get($filePath);

        // Extract title from first H1 or use path
        $title = 'Wiki';
        if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
            $title = $matches[1];
        }

        // Generate breadcrumbs
        $breadcrumbs = $this->generateBreadcrumbs($path);

        return view('wiki.show', [
            'content' => $content,
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
            'path' => $path,
        ]);
    }

    private function generateBreadcrumbs($path)
    {
        if ($path === 'index') {
            return [];
        }

        $parts = explode('/', $path);
        $breadcrumbs = [
            ['name' => 'Wiki', 'url' => '/wiki'],
        ];

        $currentPath = '';
        foreach ($parts as $part) {
            if ($part === 'index') {
                continue;
            }

            $currentPath .= ($currentPath ? '/' : '').$part;
            $breadcrumbs[] = [
                'name' => Str::title(str_replace('-', ' ', $part)),
                'url' => '/wiki/'.$currentPath,
            ];
        }

        return $breadcrumbs;
    }
}
