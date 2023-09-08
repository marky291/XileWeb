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
        $events = $this->fetchEvents($castle, $dateTime);
        if ($events->count() <= 1) return;

        [$guildDurations, $guildAttended] = $this->processEvents($events);

        $this->updateScores($guildDurations, $guildAttended, $events, $season);

        if ($sendDiscordNotification) {
            $this->sendDiscordNotification($guildDurations, $guildAttended, $events, $season);
        }
    }

    private function fetchEvents(string $castle, \DateTime $dateTime)
    {
        return GameWoeEvent::where(['processed' => false, 'castle' => $castle])
            ->whereDate('created_at', $dateTime->format('Y-m-d'))
            ->orderBy('created_at')
            ->get();
    }

    private function processEvents($events): array
    {
        $guildDurations = [];
        $guildAttended = [];
        $lastEvent = null;

        $events->each(function ($event) use (&$guildDurations, &$guildAttended, &$lastEvent) {
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

        return [$guildDurations, $guildAttended];
    }

    private function updateScores($guildDurations, $guildAttended, $events, $season): void
    {
        DB::transaction(function () use ($guildDurations, $guildAttended, $season, $events) {
            $longestDurationGuildId = array_search(max($guildDurations), $guildDurations);
            $firstBreakGuild = optional($events->firstWhere('event', GameWoeEvent::BREAK));

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

            //GameWoeEvent::whereIn('id', $events->pluck('id'))->update(['processed' => true]);
        });
    }

    private function sendDiscordNotification($guildDurations, $guildAttended, $events, $season): void
    {
        $webhookUrl = config('services.discord.channel_guild_events');
        $topper = GameWoeScore::with('guild')->orderByDesc('guild_score')->get();
        $message = "";
        $pointsAwarded = "";
        $highlights = "";

        $originalScores = GameWoeScore::with('guild')->orderByDesc('guild_score')->get()->keyBy('guild_id')->toArray();

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
            $pointsAwarded .= "`{$guildName}`: **" . GameWoeScore::POINTS_ATTENDED . "** points     [__Attendance__] \n";
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
            $originalScore = $top->guild_score - $pointsEarned;

            $message .= "`{$top->guild->name}`: **{$originalScore} -> {$top->guild_score}** points\n";
        }

        $firstBreakGuild = optional($events->firstWhere('event', GameWoeEvent::BREAK));
        if ($firstBreakGuild && $firstBreakGuild->getPlayerNameFromMessageAttribute()) {
            $highlights .= "────────────────────────────────\n";
            $highlights .= "**Highlights:**\n";
            $highlights .= "First Castle Break by: **" . $firstBreakGuild->getPlayerNameFromMessageAttribute() . "**\n";
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


        Http::post($webhookUrl, [
            'content' => $message
        ]);
    }
}
