<?php

return [

    'character' => [
        'reset' => [
            'position' => [
                'map' => 'prontera',
                'x' => 156,
                'y' => 153,
            ],
        ],
    ],
    'hyperdrive' => [
        'ip_address' => '15.197.157.22',
    ],

    'max_level' => '255',
    'max_job' => '120',
    'base_exp' => '1k',
    'job_exp' => '1k',
    'card_drops' => '10%',

    'donation' => [
        'conversion' => [
            5.00 => 3,
            10.00 => 8,
            20.00 => 18,
            40.00 => 42,
            75.00 => 88,
        ],
    ],

    'uber_shop' => [
        // When false, only admin users can purchase from the Uber Shop
        'purchasing_enabled' => true,
    ],

    'auth' => [
        // When false, login and registration are disabled (e.g., during server migration)
        'enabled' => true,
        'maintenance_message' => 'Login and registration are temporarily disabled while we migrate to new servers. Please check back soon!',
    ],
];
