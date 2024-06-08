<?php

namespace App\Actions;

use App\Exceptions\WoeEventNotEnoughEventsToProcessException;
use App\Exceptions\WoeEventOrderException;
use App\Exceptions\WoeMissingEventException;
use App\Exceptions\WoeNotCompletedException;
use App\Ragnarok\GameWoeEvent;
use App\Ragnarok\GameWoeScore;
use App\Ragnarok\Guild;
use App\Ragnarok\GuildCastle;
use App\WoeEvents\WoeEventDiscordMessage;
use App\WoeEvents\WoeEventScoreRecorder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class ProcessWoeEventPoints
{
    use AsAction;

    private WoeEventScoreRecorder $scoreRecorder;

    public function __construct(WoeEventScoreRecorder $scoreRecorder)
    {
        $this->scoreRecorder = $scoreRecorder;
    }

    /**
     * Handles the processing of WOE event points.
     *
     * @throws \Throwable
     */
    public function handle(string $castle, Carbon $season = null)
    {
        $currentSeason = $season->month ?? now()->format('n');

        $this->scoreRecorder->castle = $castle;
        $this->scoreRecorder->season = $currentSeason;

        $events = $this->fetchEvents($castle);

        if ($events->count() <= 1) {
            Log::info('Not enough events to process.', ['events_count' => $events->count()]);
            throw new WoeEventNotEnoughEventsToProcessException("Not enough events to process, found {$events->count()} events.");
        }

        throw_if(!$this->isEventOrderValid($events), new WoeEventOrderException('Events are out of order.'));

        $woeStarted = $events->contains('event', GameWoeEvent::STARTED);
        $woeEnded = $events->contains('event', GameWoeEvent::ENDED);

        if (!$woeStarted || !$woeEnded) {
            Log::info('WOE not completed. Missing STARTED or ENDED event.', [
                'woeStarted' => $woeStarted,
                'woeEnded' => $woeEnded
            ]);
            throw new WoeNotCompletedException();
        }

        $events = $events->reject(function (GameWoeEvent $event) {
            return $event->guild_name_from_message == Guild::GM_TEAM;
        });

        Log::info('Processing events.', ['events' => $events]);

        if ($events->count() > 0) {
            [$guildDurations, $guildAttended] = $this->processEvents($events);
            $this->updateScores($guildDurations, $guildAttended, $events, $currentSeason, $castle);
            return $this->scoreRecorder;
        }

        throw new WoeEventNotEnoughEventsToProcessException("Not enough events to process, found {$events->count()} events.");
    }

    /**
     * Validates the order of events.
     *
     * @throws \Throwable
     */
    private function isEventOrderValid($events)
    {
        $startedEvent = $events->firstWhere('event', GameWoeEvent::STARTED);
        $endedEvent = $events->firstWhere('event', GameWoeEvent::ENDED);

        throw_if(!$startedEvent, new WoeMissingEventException('STARTED'));
        throw_if(!$endedEvent, new WoeMissingEventException('ENDED'));

        return $startedEvent && $endedEvent && $startedEvent->created_at->lessThan($endedEvent->created_at);
    }

    /**
     * Fetches events for a given castle.
     */
    private function fetchEvents(string $castle)
    {
        $currentMonth = Carbon::now()->month;

        return GameWoeEvent::where([
            'processed' => false,
            'castle' => $castle
        ])
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Processes the events to calculate durations and attendance.
     */
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

    /**
     * Updates the scores based on the processed events.
     */
    private function updateScores($guildDurations, $guildAttended, $events, $season, $castle): void
    {
        DB::transaction(function () use ($guildDurations, $guildAttended, $season, $events, $castle) {
            $longestDurationGuildId = array_search(max($guildDurations), $guildDurations);
            $firstBreakGuild = $events->firstWhere('event', GameWoeEvent::BREAK);
            $winningGuildEvent = $events->firstWhere('event', GameWoeEvent::ENDED);
            $mergedGuilds = array_merge(array_keys($guildAttended), array_keys($guildDurations));

            // Ensure the guild from the 'STARTED' event is also included
            $startedGuildEvent = $events->firstWhere('event', GameWoeEvent::STARTED);
            if ($startedGuildEvent && !in_array($startedGuildEvent->guild_id, $mergedGuilds)) {
                $mergedGuilds[] = $startedGuildEvent->guild_id;
            }

            foreach (array_unique($mergedGuilds) as $guild_id) {
                if ($guild_id == 0) continue;

                $score = GameWoeScore::firstOrNew(['guild_id' => $guild_id, 'season' => $season, 'castle_name' => $castle]);
                $guildName = $events->firstWhere('guild_id', $guild_id)->guild_name_from_message ?? 'Unknown Guild';

                $score->guild_name = $guildName;
                $score->castle_name = $events->first()->castle;
                $score->previous_score = $score->guild_score ?? 0;

                if (isset($guildDurations[$guild_id]) && $guild_id == $longestDurationGuildId) {
                    $score->guild_score += GameWoeScore::POINTS_LONGEST_HELD;
                    $this->scoreRecorder->longest_hold_guild = Guild::firstWhere('guild_id', $guild_id);
                    $this->scoreRecorder->longest_hold_award = GameWoeScore::POINTS_LONGEST_HELD;
                }

                if ($firstBreakGuild && $firstBreakGuild->guild_id === $guild_id) {
                    $score->guild_score += GameWoeScore::POINTS_FIRST_BREAK;
                    $this->scoreRecorder->first_break_guild = Guild::firstWhere('guild_id', $guild_id);
                    $this->scoreRecorder->first_break_event = $firstBreakGuild;
                    $this->scoreRecorder->first_break_award = GameWoeScore::POINTS_FIRST_BREAK;
                }

                if (isset($guildAttended[$guild_id])) {
                    $score->guild_score += GameWoeScore::POINTS_ATTENDED;
                    $this->scoreRecorder->addAttendee(Guild::firstWhere('guild_id', $guild_id));
                    $this->scoreRecorder->attendee_award = GameWoeScore::POINTS_ATTENDED;
                }

                if ($winningGuildEvent && $guild_id == $winningGuildEvent->guild_id) {
                    $score->guild_score += GameWoeScore::POINTS_CASTLE_OWNER;
                    $this->scoreRecorder->winning_guild = Guild::firstWhere('guild_id', $guild_id);
                    $this->scoreRecorder->winning_event = $winningGuildEvent;
                    $this->scoreRecorder->winning_award = GameWoeScore::POINTS_CASTLE_OWNER;
                }

                if ($score->exists || $score->guild_score > 0) {
                    $score->save();
                }
            }

            GameWoeEvent::whereIn('id', $events->pluck('id'))->update(['processed' => true]);
        });
    }
}
