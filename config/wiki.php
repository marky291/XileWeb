<?php
// config/wiki.php

return [
    'servers' => [
        'xilero' => [
            'label' => 'XileRO',
            'rate'  => 'Mid-Rate',
            'path'  => env('WIKI_XILERO_PATH'),
            // Git working dir to `git pull` on webhook (the clone root; for the
            // game repo this is the parent of the sparse rathena/gitbook path).
            'repo'  => env('WIKI_XILERO_REPO'),
            // Branch the wiki content tracks; webhook only pulls on pushes to it.
            'branch' => env('WIKI_XILERO_BRANCH', 'stable'),
        ],
        'xileretro' => [
            'label' => 'XileRetro',
            'rate'  => 'High-Rate',
            'path'  => env('WIKI_XILERETRO_PATH'),
            'repo'  => env('WIKI_XILERETRO_REPO'),
            'branch' => env('WIKI_XILERETRO_BRANCH', 'master'),
        ],
    ],

    'default' => 'xilero',

    // Shared secret for GitHub webhook signature verification (X-Hub-Signature-256).
    'webhook_secret' => env('WIKI_WEBHOOK_SECRET'),
];
