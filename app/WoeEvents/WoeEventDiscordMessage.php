<?php

namespace App\WoeEvents;

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

        foreach ($scoring->attendee as $attendee) {
            $message .= "- `{$attendee->name}`  saw  **" . $scoring->attendee_award . "** Point for [__Attendance__]\n";
        }

        if ($scoring->most_kills_guild) {
            $message .= "- `{$scoring->most_kills_guild->name}`  earned  **" . $scoring->most_kills_award . "** Points for [__Most Kills__]\n";
        }

        $leaderboard = $scoring->leaderboard($castle, now()->format('n'));

        if ($leaderboard->count()) {
            $message .= "\n**{$castle} Leaderboard:**\n";

            foreach ($leaderboard as $index => $gameWoeScore) {
                $index = $index + 1;
                $message .= "#{$index}. `{$gameWoeScore->guild->name}`  with  `{$gameWoeScore->guild_score} Points Total` ({$gameWoeScore->previous_score})\n";
            }
        }

        $globalLeaderboard = $scoring->globalLeaderboard(now()->format('n'));

        if ($globalLeaderboard->count()) {
            $message .= "\n**Season Leaderboard:**\n";

            foreach ($globalLeaderboard as $index => $gameWoeScore) {
                $index = $index + 1;
                $message .= "#{$index}. `{$gameWoeScore->guild->name}`  with  `{$gameWoeScore->total_score} Points Total`\n";
            }
        }

        if ($message != "") {
            $message .= "\n-----------------------------------------------------------------------------\n";
        }

        return $message;
    }
}
