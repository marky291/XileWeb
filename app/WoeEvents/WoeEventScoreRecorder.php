<?php

namespace App\WoeEvents;

use App\Ragnarok\GameWoeEvent;
use App\Ragnarok\GameWoeScore;
use App\Ragnarok\Guild;
use Illuminate\Support\Collection;

class WoeEventScoreRecorder
{
    public string $castle;
    public int $season;
    public ?Guild $longest_hold_guild = null;
    public int $longest_hold_award = 0;
    public ?Guild $first_break_guild = null;
    public ?GameWoeEvent $first_break_event = null;
    public int $first_break_award = 0;
    public Collection $attendee;
    public ?Guild $winning_guild = null;
    public int $winning_award = 0;
    public ?GameWoeEvent $winning_event = null;
    public int $attendee_award = 0;
    public ?Guild $most_kills_guild = null;
    public int $most_kills_award = 0;

    public function __construct()
    {
        $this->attendee = collect();
    }

    public function addAttendee(Guild $guild): void
    {
        $this->attendee->push($guild);
    }

    public function hasEvents(): bool
    {
        return $this->winning_guild || $this->longest_hold_guild || $this->first_break_guild || $this->attendee->isNotEmpty() || $this->most_kills_guild;
    }

    public function leaderboard(string $castle, string $season): Collection
    {
        return GameWoeScore::with('guild')
            ->where('castle_name', $castle)
            ->whereNot('guild_name', Guild::GM_TEAM)
            ->where('season', $season)
            ->orderByDesc('guild_score')
            ->get();
    }

    public function globalLeaderboard(string $season): Collection
    {
        return GameWoeScore::with('guild')
            ->selectRaw('guild_id, SUM(guild_score) as total_score')
            ->whereNot('guild_name', Guild::GM_TEAM)
            ->where('season', $season)
            ->groupBy('guild_id')
            ->orderByDesc('total_score')
            ->get();
    }
}
