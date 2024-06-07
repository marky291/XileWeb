<?php

namespace App\WoeEvents;

use App\Ragnarok\GuildCastle;
use Lorisleiva\Actions\Concerns\AsAction;

class WoeEventDiscordChannelResolver
{
    use AsAction;

    public function handle(string $castle): string
    {
        $channels = [
            GuildCastle::KRIEMHILD => config('services.discord.kriemhild_guild_points'),
            GuildCastle::SWANHILD => config('services.discord.swanhild_guild_points'),
            GuildCastle::FADHRINGH => config('services.discord.fadhringh_guild_points'),
            GuildCastle::SKOEGUL => config('services.discord.skoegul_guild_points'),
            GuildCastle::GONDUL => config('services.discord.gondul_guild_points'),
            GuildCastle::HLJOD => config('services.discord.hljod_guild_points'),
            GuildCastle::CYR => config('services.discord.cyr_guild_points'),
        ];

        return $channels[$castle];
    }
}
