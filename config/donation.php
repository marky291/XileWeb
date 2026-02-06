<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Donation Tiers
    |--------------------------------------------------------------------------
    |
    | Configure the donation amounts and their corresponding Uber rewards.
    | These anchor tiers define key points on the rate curve. The calculator
    | interpolates between them and extrapolates above the highest tier,
    | matching XileRetro rates for any donation amount $5-$2000+.
    |
    */
    'tiers' => [
        ['amount' => 5, 'ubers' => 3],
        ['amount' => 10, 'ubers' => 8],
        ['amount' => 20, 'ubers' => 18],
        ['amount' => 40, 'ubers' => 42],
        ['amount' => 75, 'ubers' => 88],
        ['amount' => 100, 'ubers' => 129],
        ['amount' => 200, 'ubers' => 269],
        ['amount' => 300, 'ubers' => 422],
        ['amount' => 500, 'ubers' => 733],
        ['amount' => 1000, 'ubers' => 1525],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Amount Calculator Settings
    |--------------------------------------------------------------------------
    |
    | Settings for calculating Ubers for custom donation amounts.
    | The algorithm interpolates between tiers and extrapolates above the
    | highest tier, with higher amounts getting progressively better rates.
    | Tuned to match XileRetro rates across the full $5-$2000+ range.
    |
    */
    'calculator' => [
        // Minimum donation amount allowed
        'minimum_amount' => 5,

        // Base rate for amounts below the first tier (Ubers per dollar)
        'base_rate' => 0.6,

        // Growth factor for extrapolating above highest tier (higher = more generous)
        // This adds extra value as amounts increase beyond $1000
        'extrapolation_growth' => 0.025,

        // Maximum rate cap (Ubers per dollar) to prevent excessive rewards
        'max_rate' => 1.584,

        // Round calculated ubers to nearest integer (false) or floor (true)
        'round_down' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Configure available payment methods and their bonuses.
    |
    */
    'payment_methods' => [
        'paypal' => ['name' => 'PayPal', 'bonus' => 0],
        'crypto' => ['name' => 'Binance (Crypto)', 'bonus' => 10],
        'zelle' => ['name' => 'Zelle', 'bonus' => 0],
        'gcash' => ['name' => 'GCash', 'bonus' => 0],
    ],

    /*
    |--------------------------------------------------------------------------
    | Donation items
    |--------------------------------------------------------------------------
    |
    | Here you may configure what donation items are shown on the website.
    |
    */
    'items' => [
        [
            'name' => 'Scarlet Angel Helm',
            'image' => 'scarlet_helm.png',
            'description' => 'Helm of the fallen Scarlet Angel.',
            'stats' => 'DEX +4, STR +4, VIT +8, MDEF +9',
            'isTokenSet' => true,
            'cost' => 6,
        ],
        [
            'name' => 'Scarlet Angel Ears',
            'image' => 'scarlet_ears.png',
            'description' => 'Ears of the fallen Scarlet Angel.',
            'stats' => 'Add 10% resistance against status.',
            'isTokenSet' => true,
            'cost' => 6,
        ],
        [
            'name' => 'Scarlet Angel Wings',
            'image' => 'scarlet_wings.png',
            'description' => 'Wings of the fallen Scarlet Angel.',
            'stats' => '+5 to All Stats',
            'isTokenSet' => true,
            'cost' => 9,
        ],
        [
            'name' => 'Emperor Helm',
            'image' => 'emperor_helm.png',
            'description' => 'Helm of the Emperor, ruler of all things.',
            'stats' => 'INT +3, AGI +3, VIT +5, MDEF +3',
            'isTokenSet' => true,
            'cost' => 6,
        ],
        [
            'name' => 'Emperor Shoulder',
            'image' => 'emperor_shoulders.png',
            'description' => 'Shoulderpads of the Emperor, ruler of all things.',
            'stats' => 'Max HP +5%',
            'isTokenSet' => true,
            'cost' => 6,
        ],
        [
            'name' => 'Emperor Wings',
            'image' => 'emperor_wings.png',
            'description' => 'Wings of the Emperor, ruler of all things.',
            'stats' => '+5 to All Stats',
            'isTokenSet' => true,
            'cost' => 9,
        ],
        [
            'name' => 'Little Devil Horns',
            'image' => 'little_devil_helm.png',
            'description' => 'Horns that mark one as a devil.',
            'stats' => 'STR +5, VIT +1, MDEF +3',
            'isTokenSet' => true,
            'cost' => 7,
        ],
        [
            'name' => 'Little Devil Tail',
            'image' => 'little_devil_tail.png',
            'description' => 'A tail that mark one as a devil.',
            'stats' => '+5 to All Stats, MDEF +3, ATK +25, HIT +20.',
            'isTokenSet' => true,
            'cost' => 7,
        ],
        [
            'name' => 'Little Devil Wings',
            'image' => 'little_devil_wings.png',
            'description' => 'Wings that mark one as a devil.',
            'stats' => 'MaxHP +5%, Movement +10%.',
            'isTokenSet' => true,
            'cost' => 11,
        ],
    ],
];
