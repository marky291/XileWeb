<?php

return [

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
            'stats' => 'Add a 10% resistance against all Statuses.',
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
        ]
    ]
];
