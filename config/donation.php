<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Donation Tiers
    |--------------------------------------------------------------------------
    |
    | Configure the donation amounts and their corresponding Uber rewards.
    | Based on optimized CSV rates - more generous for higher donations.
    |
    */
    'tiers' => [
        ['amount' => 5, 'ubers' => 3],
        ['amount' => 10, 'ubers' => 8],
        ['amount' => 15, 'ubers' => 13],
        ['amount' => 20, 'ubers' => 18],
        ['amount' => 25, 'ubers' => 24],
        ['amount' => 30, 'ubers' => 30],
        ['amount' => 35, 'ubers' => 36],
        ['amount' => 40, 'ubers' => 42],
        ['amount' => 45, 'ubers' => 49],
        ['amount' => 50, 'ubers' => 55],
        ['amount' => 55, 'ubers' => 62],
        ['amount' => 60, 'ubers' => 68],
        ['amount' => 65, 'ubers' => 75],
        ['amount' => 70, 'ubers' => 81],
        ['amount' => 75, 'ubers' => 88],
        ['amount' => 80, 'ubers' => 96],
        ['amount' => 85, 'ubers' => 104],
        ['amount' => 90, 'ubers' => 113],
        ['amount' => 95, 'ubers' => 121],
        ['amount' => 100, 'ubers' => 129],
        ['amount' => 125, 'ubers' => 164],
        ['amount' => 150, 'ubers' => 199],
        ['amount' => 175, 'ubers' => 234],
        ['amount' => 200, 'ubers' => 269],
        ['amount' => 250, 'ubers' => 346],
        ['amount' => 300, 'ubers' => 422],
        ['amount' => 350, 'ubers' => 500],
        ['amount' => 400, 'ubers' => 578],
        ['amount' => 450, 'ubers' => 655],
        ['amount' => 500, 'ubers' => 733],
        ['amount' => 600, 'ubers' => 891],
        ['amount' => 700, 'ubers' => 1050],
        ['amount' => 800, 'ubers' => 1208],
        ['amount' => 900, 'ubers' => 1367],
        ['amount' => 1000, 'ubers' => 1525],
        ['amount' => 1250, 'ubers' => 1921],
        ['amount' => 1500, 'ubers' => 2317],
        ['amount' => 1750, 'ubers' => 2713],
        ['amount' => 2000, 'ubers' => 3109],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Amount Calculator Settings
    |--------------------------------------------------------------------------
    |
    | Settings for calculating Ubers for custom donation amounts.
    | Tuned to match CSV progression - more generous for high amounts.
    |
    */
    'calculator' => [
        'minimum_amount' => 5,
        'base_rate' => 0.6,
        'extrapolation_growth' => 0.02,
        'max_rate' => 1.56,
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
