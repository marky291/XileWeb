<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'discord' => [
        // Bot tokens
        'player_count_token' => env('DISCORD_PLAYER_COUNT_TOKEN'),
        'latest_player_token' => env('DISCORD_LATEST_PLAYER_TOKEN'),
        'kriemhild_guild_points' => env('DISCORD_KRIEMHILD_GUILD_POINTS'),
        'swanhild_guild_points' => env('DISCORD_SWANHILD_GUILD_POINTS'),
        'skoegul_guild_points' => env('DISCORD_SKOEGUL_GUILD_POINTS'),
        'fadhringh_guild_points' => env('DISCORD_FADHRINGH_GUILD_POINTS'),
        'gondul_guild_points' => env('DISCORD_GONDUL_GUILD_POINTS'),
        'hljod_guild_points' => env('DISCORD_HLJOD_GUILD_POINTS'),
        'cyr_guild_points' => env('DISCORD_CYR_GUILD_POINTS'),
        'server_time_token' => env('DISCORD_SERVER_TIME_TOKEN'),
        'next_woe_time_token' => env('DISCORD_NEXT_WOE_TIME_TOKEN'),

        // OAuth credentials (for Socialite login)
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect' => env('DISCORD_REDIRECT_URI', '/auth/discord/callback'),
    ],
];
