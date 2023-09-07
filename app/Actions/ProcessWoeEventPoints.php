<?php

namespace App\Actions;

use App\Ragnarok\GameWoeEvent;
use App\Ragnarok\GameWoeScore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class ProcessWoeEventPoints
{
    use AsAction;

    public function handle(string $castle, \DateTime $dateTime, int $season, bool $sendDiscordNotification = false)
    {
        $events = GameWoeEvent::where(['processed' => false, 'castle' => $castle])
            ->whereDate('created_at', $dateTime->format('Y-m-d'))
            ->orderBy('created_at')
            ->get();

        // should have more than one event.
        if (!$events->count()) {
            return;
        }

        // should have more than one event.
        if ($events->count() == 1) {
            return;
        }

        $guildDurations = [];
        $guildAttended = [];
        $lastEvent = null;

        $events->each(function ($event) use (&$guildDurations, &$guildAttended, &$lastEvent)
        {
            if ($event->event === GameWoeEvent::ATTENDED) {
                $guildAttended[$event->guild_id] = ($guildAttended[$event->guild_id] ?? 0) + 1;
            }

            if ($lastEvent) {
                $duration = $event->created_at->diffInSeconds($lastEvent->created_at);
                if (in_array($lastEvent->event, [GameWoeEvent::STARTED, GameWoeEvent::BREAK])) {
                    $guildDurations[$lastEvent->guild_id] = ($guildDurations[$lastEvent->guild_id] ?? 0) + $duration;
                }
            }
            $lastEvent = $event;
        });

        $longestDurationGuildId = array_search(max($guildDurations), $guildDurations);
        $firstBreakGuild = optional($events->firstWhere('event', GameWoeEvent::BREAK));

        DB::transaction(function () use ($guildDurations, $guildAttended, $longestDurationGuildId, $season, $firstBreakGuild, $events) {
            foreach ($guildDurations as $guild_id => $duration) {
                if ($guild_id == 0) continue;

                $score = GameWoeScore::firstOrNew(['guild_id' => $guild_id, 'season' => $season]);

                if ($guild_id == $longestDurationGuildId) {
                    $score->guild_score += GameWoeScore::POINTS_LONGEST_HELD;
                }

                if ($firstBreakGuild && $firstBreakGuild->guild_id === $guild_id) {
                    $score->guild_score += GameWoeScore::POINTS_FIRST_BREAK;
                }

                if (isset($guildAttended[$guild_id])) {
                    $score->guild_score += GameWoeScore::POINTS_ATTENDED;
                }

                if ($score->exists || $score->guild_score > 0) {
                    $score->save();
                }
            }

            GameWoeEvent::whereIn('id', $events->pluck('id'))->update(['processed' => true]);
        });

        if ($sendDiscordNotification) {
            $webhookUrl = config('services.discord.channel_guild_events');
            $topper = GameWoeScore::with('guild')->orderByDesc('guild_score')->get();
            $message = "";

            $pointsAwarded = "";

            if ($longestDurationGuildId) {
                $guildName = $events->firstWhere('guild_id', $longestDurationGuildId)->guild_name_from_message ?? '';
                $pointsAwarded .= "`{$guildName}`: **" . GameWoeScore::POINTS_LONGEST_HELD . "** points    [__Longest Castle Defense__] \n";
            }

            if ($firstBreakGuild && $firstBreakGuild->guild_name_from_message) {
                $pointsAwarded .= "`{$firstBreakGuild->guild_name_from_message}`: **" . GameWoeScore::POINTS_FIRST_BREAK . "** points    [__First Castle Break__] \n";
            }

            foreach ($guildAttended as $guild_id => $count) {
                $guildName = $events->firstWhere('guild_id', $guild_id)->guild_name_from_message ?? '';
                $pointsAwarded .= "`{$guildName}`: **" . GameWoeScore::POINTS_ATTENDED . "** points     [__Attendance__] \n";
            }

            if (!empty($pointsAwarded)) {
                $message .= ">>> **Points Awarded:**\n$pointsAwarded\n────────────────────────────────\n";
            }

            $message .= "**Current Scores:**\n";

            foreach ($topper as $top) {
                $message .= "`{$top->guild->name}`: **{$top->guild_score}** points\n";
            }

            Http::post($webhookUrl, [
                'content' => $message
            ]);
        }
    }
}
