<?php

namespace App\WoeEvents;

use App\Ragnarok\GameWoeEvent;
use App\Ragnarok\GameWoeScore;
use App\Ragnarok\Guild;
use Lorisleiva\Actions\Concerns\AsAction;

class WoeEventDiscordMessage
{
    use AsAction;

    public function handle(WoeEventScoreRecorder $scoring, $castle)
    {
        $message = "";

        if ($scoring->hasEvents()) {
            $message .= ">>> -----------------------------------------------------------------------------\n";
            $message .= "\n**{$castle} Events:**\n";
        }

        if ($scoring->winning_guild) {
            $message .= "- `{$scoring->winning_guild->name}`  won  **" . $scoring->winning_award . "** Points as [__Castle Owner__]\n";
        }

        if ($scoring->longest_hold_guild) {
            $message .= "- `{$scoring->longest_hold_guild->name}`  earned  **" . $scoring->longest_hold_award . "** Points for [__Longest Castle Defense__] \n";
        }

        if ($scoring->first_break_guild) {
            $message .= "- `{$scoring->first_break_guild->name}`  took  **" . $scoring->first_break_award . "** Points for [__First Castle Break__]\n";
        }

        /** @var Guild $attendee */
        foreach ($scoring->attendee as $attendee) {
            $message .= "- `{$attendee->name}`  saw  **" . $scoring->attendee_award . "** Point for [__Attendance__]\n";
        }

        $leaderboard = $scoring->leaderboard($castle, now()->format('n'));

        if ($leaderboard->count()) {
            $message .= "\n**{$castle} Leaderboard:**\n";

            /** @var GameWoeScore $gameWoeScore */
            foreach ($scoring->leaderboard($castle, now()->format('n')) as $index => $gameWoeScore) {
                $index = $index + 1;
                $message .= "#{$index}. `{$gameWoeScore->guild->name}`  with  `{$gameWoeScore->guild_score} Points Total` ({$gameWoeScore->previous_score})\n";
            }
        }

        $globalLeaderboard = $scoring->globalLeaderboard(now()->format('n'));

        if ($globalLeaderboard->count()) {
            $message .= "\n**Global Leaderboard:**\n";

            /** @var GameWoeScore $gameWoeScore */
            foreach ($scoring->globalLeaderboard(now()->format('n')) as $index => $gameWoeScore) {
                $index = $index + 1;
                $message .= "#{$index}. `{$gameWoeScore->guild->name}`  with  `{$gameWoeScore->guild_score} Points Total` ({$gameWoeScore->previous_score})\n";
            }
        }


//        $firstBreakGuild = optional($events->firstWhere('event', GameWoeEvent::BREAK));
//        $mostBreaksGuildId = !empty($breakCounts) ? array_search(max($breakCounts), $breakCounts) : null;
//
//        if ($firstBreakGuild || $mostBreaksGuildId) {
//            $highlights .= "\n**{$castle} Highlights:**\n";
//        }
//
//        if ($scoring->first_break_event) {
//            $highlights .= "- First Castle Break by player: `" . $scoring->first_break_event->getPlayerNameFromMessageAttribute() . "`\n";
//        }
//
//        $breakCounts = [];
//        foreach ($events as $event) {
//            if ($event->event === GameWoeEvent::BREAK) {
//                $breakCounts[$event->guild_id] = ($breakCounts[$event->guild_id] ?? 0) + 1;
//            }
//        }
//
//        if ($mostBreaksGuildId) {
//            $mostBreaksGuildName = $events->firstWhere('guild_id', $mostBreaksGuildId)->guild_name_from_message ?? '';
//            $highlights .= "- Guild with Most Breaks: **{$mostBreaksGuildName}**\n";
//        }
//
//        if (!empty($highlights)) {
//            $message .= $highlights;
//        }

        if ($message != "") {
            $message .= "\n-----------------------------------------------------------------------------\n";
        }

        return $message;
    }
}
