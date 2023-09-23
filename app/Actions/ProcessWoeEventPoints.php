<?php

namespace App\Actions;

use App\Ragnarok\GameWoeEvent;
use App\Ragnarok\GameWoeScore;
use App\Ragnarok\Guild;
use App\Ragnarok\GuildCastle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class ProcessWoeEventPoints
{
    use AsAction;

    public function isDebug()
    {
        return false;
    }

    public function handle(string $castle, \DateTime $dateTime, int $season, bool $sendDiscordNotification = false)
    {
        $events = $this->fetchEvents($castle, $season, $dateTime);

        if ($events->count() <= 1) return;

        $events = $events->reject(function (GameWoeEvent $event) {
            return $event->guild_name_from_message == Guild::GM_TEAM;
        });

        if ($events->count() > 0) {
            [$guildDurations, $guildAttended] = $this->processEvents($events);
            $this->updateScores($guildDurations, $guildAttended, $events, $season, $castle);
            if ($sendDiscordNotification || $this->isDebug()) {
                $this->sendDiscordNotification($castle, $guildDurations, $guildAttended, $events, $season);
            }
        }
    }

    private function fetchEvents(string $castle, int $season, \DateTime $dateTime)
    {
        return GameWoeEvent::where(['processed' => false, 'castle' => $castle, 'season' => $season])
            ->whereDate('created_at', $dateTime->format('Y-m-d'))
            ->orderBy('created_at')
            ->get();
    }

    public function getCastleDiscordChannel(string $castle)
    {
       if ($castle == GuildCastle::KRIEMHILD) {
           return config('services.discord.kriemhild_guild_points');
       }

       if ($castle == GuildCastle::SWANHILD) {
           return config('services.discord.swanhild_guild_points');
       }

       if ($castle == GuildCastle::FADHRINGH) {
           return config('services.discord.fadhringh_guild_points');
       }

       if ($castle == GuildCastle::SKOEGUL) {
           return config('services.discord.skoegul_guild_points');
       }

       if ($castle == GuildCastle::GONDUL) {
          return config('services.discord.gondul_guild_points');
       }

       if ($castle == GuildCastle::HLJOD) {
           return config('services.discord.hljod_guild_points');
       }
    }

    private function processEvents($events): array
    {
        $guildDurations = [];
        $guildAttended = [];
        $lastEvent = null;

        $events->each(function ($event) use (&$guildDurations, &$guildAttended, &$lastEvent) {

            if ($event->event === GameWoeEvent::ATTENDED) {
                $guildAttended[$event->guild_id] = ($guildAttended[$event->guild_id] ?? 0) + 1;
                return;
            }

            if ($lastEvent) {
                $duration = $event->created_at->diffInSeconds($lastEvent->created_at);
                $guildDurations[$lastEvent->guild_id] = ($guildDurations[$lastEvent->guild_id] ?? 0) + $duration;
            }

            $lastEvent = $event;
        });

        return [$guildDurations, $guildAttended];
    }

    private function updateScores($guildDurations, $guildAttended, $events, $season, $castle): void
    {
        DB::transaction(function () use ($guildDurations, $guildAttended, $season, $events, $castle) {
            $longestDurationGuildId = array_search(max($guildDurations), $guildDurations);
            $firstBreakGuild = optional($events->firstWhere('event', GameWoeEvent::BREAK));

            $winningGuildEvent = $events->filter(function ($event) {
                return $event->event === GameWoeEvent::ENDED;
            })->last();

            $mergedGuilds = array_merge(array_keys($guildAttended), array_keys($guildDurations));

            foreach (array_unique($mergedGuilds) as $guild_id) {
                if ($guild_id == 0) continue;

                $score = GameWoeScore::firstOrNew(['guild_id' => $guild_id, 'season' => $season, 'castle_name' => $castle]);
                $guildName = $events->firstWhere('guild_id', $guild_id)->guild_name_from_message ?? '';

                $score->guild_name = $guildName;
                $score->castle_name = $events->first()->castle;

                if (isset($guildDurations[$guild_id])) {
                    if ($guild_id == $longestDurationGuildId) {
                        $score->guild_score += GameWoeScore::POINTS_LONGEST_HELD;
                    }
                }

                if ($firstBreakGuild && $firstBreakGuild->guild_id === $guild_id) {
                    $score->guild_score += GameWoeScore::POINTS_FIRST_BREAK;
                }

                if (isset($guildAttended[$guild_id])) {
                    $score->guild_score += GameWoeScore::POINTS_ATTENDED;
                }

                if ($winningGuildEvent && $guild_id == $winningGuildEvent->guild_id) {
                    $score->guild_score += GameWoeScore::POINTS_CASTLE_OWNER;
                }

                if ($score->exists || $score->guild_score > 0) {
                    $score->save();
                }
            }

            if (!$this->isDebug()) {
                GameWoeEvent::whereIn('id', $events->pluck('id'))->update(['processed' => true]);
            }
        });
    }

    private function sendDiscordNotification($castle, $guildDurations, $guildAttended, $events, $season): void
    {
        $webhookUrl = $this->getCastleDiscordChannel($castle);

        $topper = GameWoeScore::with('guild')
            ->where('castle_name', $castle)
            ->whereNot('guild_name', Guild::GM_TEAM)
            ->orderByDesc('guild_score')
            ->get();

        $message = "";
        $pointsAwarded = "";
        $highlights = "";

        $winningGuildEvent = $events->filter(function ($event) {
            return $event->event === GameWoeEvent::ENDED;
        })->last();

        if ($winningGuildEvent && $winningGuildEvent->guild_name_from_message) {
            $pointsAwarded .= "`{$winningGuildEvent->guild_name_from_message}`: **" . GameWoeScore::POINTS_CASTLE_OWNER . "** points    [__Castle Owner__] \n";
        }

        $longestDurationGuildId = array_search(max($guildDurations), $guildDurations);
        if ($longestDurationGuildId) {
            $guildName = $events->firstWhere('guild_id', $longestDurationGuildId)->guild_name_from_message ?? '';
            $pointsAwarded .= "`{$guildName}`: **" . GameWoeScore::POINTS_LONGEST_HELD . "** points    [__Longest Castle Defense__] \n";
        }

        $firstBreakGuild = optional($events->firstWhere('event', GameWoeEvent::BREAK));
        if ($firstBreakGuild && $firstBreakGuild->guild_name_from_message) {
            $pointsAwarded .= "`{$firstBreakGuild->guild_name_from_message}`: **" . GameWoeScore::POINTS_FIRST_BREAK . "** points    [__First Castle Break__] \n";
        }

        foreach ($guildAttended as $guild_id => $count) {
            $guildName = $events->firstWhere('guild_id', $guild_id)->guild_name_from_message ?? '';
            $pointsAwarded .= "`{$guildName}`: **" . GameWoeScore::POINTS_ATTENDED . "** points    [__Attendance__] \n";
        }

        if (!empty($pointsAwarded)) {
            $message .= ">>> **Points Awarded:**\n$pointsAwarded\n────────────────────────────────\n";
        }

        $message .= "**Current Scores:**\n";
        foreach ($topper as $top) {
            // Calculate original score by subtracting points earned during this event
            $pointsEarned = 0;
            if ($top->guild_id == $longestDurationGuildId) $pointsEarned += GameWoeScore::POINTS_LONGEST_HELD;
            if ($firstBreakGuild && $firstBreakGuild->guild_id === $top->guild_id) $pointsEarned += GameWoeScore::POINTS_FIRST_BREAK;
            if (isset($guildAttended[$top->guild_id])) $pointsEarned += GameWoeScore::POINTS_ATTENDED;
            if ($winningGuildEvent && $top->guild_id == $winningGuildEvent->guild_id) $pointsEarned += GameWoeScore::POINTS_CASTLE_OWNER;

            $originalScore = $top->guild_score - $pointsEarned;
            $message .= "`{$top->guild_name}`: **{$originalScore} -> {$top->guild_score}** points\n";
        }

        $firstBreakGuild = optional($events->firstWhere('event', GameWoeEvent::BREAK));
        if ($firstBreakGuild && $firstBreakGuild->getPlayerNameFromMessageAttribute()) {
            $highlights .= "────────────────────────────────\n";
            $highlights .= "**Highlights:**\n";
            $highlights .= "First Castle Break by player: **" . $firstBreakGuild->getPlayerNameFromMessageAttribute() . "**\n";
        }

        $breakCounts = [];
        foreach ($events as $event) {
            if ($event->event === GameWoeEvent::BREAK) {
                $breakCounts[$event->guild_id] = ($breakCounts[$event->guild_id] ?? 0) + 1;
            }
        }
        $mostBreaksGuildId = !empty($breakCounts) ? array_search(max($breakCounts), $breakCounts) : null;

        if ($mostBreaksGuildId) {
            $mostBreaksGuildName = $events->firstWhere('guild_id', $mostBreaksGuildId)->guild_name_from_message ?? '';
            $highlights .= "Guild with Most Breaks: **{$mostBreaksGuildName}**\n";
        }

        if (!empty($highlights)) {
            $message .= $highlights;
        }

        if ($this->isDebug()) {
            dd($message);
        }

        Http::post($webhookUrl, [
            'content' => $message
        ]);
    }
}
