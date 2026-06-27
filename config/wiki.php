<?php
// config/wiki.php

return [
    'servers' => [
        'xilero' => [
            'label' => 'XileRO',
            'rate'  => 'Mid-Rate',
            'path'  => env('WIKI_XILERO_PATH'),
        ],
        'xileretro' => [
            'label' => 'XileRetro',
            'rate'  => 'High-Rate',
            'path'  => env('WIKI_XILERETRO_PATH'),
        ],
    ],

    'default' => 'xilero',
];
