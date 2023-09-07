<?php

namespace Tests\Unit;

use App\Actions\ProcessWoeEventPoints;
use App\Ragnarok\GameWoeEvent;
use App\Ragnarok\GameWoeScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProcessWoeEventPointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_events()
    {
        $action = new ProcessWoeEventPoints();
        $action->handle('Kriemhild', new \DateTime(), 1, false);
        $this->assertDatabaseCount('game_woe_scores', 0);
    }

    public function test_single_event()
    {
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'message' => 'Guild [Guild1]',
            'created_at' => Carbon::now(),
            'processed' => false
        ]);
        $action = new ProcessWoeEventPoints();
        $action->handle('Kriemhild', new \DateTime(), 1);
        $this->assertDatabaseCount('game_woe_scores', 0);
    }

    public function test_multiple_events_same_guilds()
    {
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'message' => 'Guild [Guild1]',
            'created_at' => Carbon::now(),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'message' => 'Guild [Guild2]',
            'created_at' => Carbon::now()->addSeconds(2),
            'processed' => false
        ]);
        $action = new ProcessWoeEventPoints();
        $action->handle('Kriemhild', new \DateTime(), 1);
        $this->assertDatabaseCount('game_woe_scores', 1);
    }

    public function test_points_are_calculated_correctly()
    {
        $this->withExceptionHandling();

        $action = new ProcessWoeEventPoints();

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'edition' => 1,
            'message' => 'Guild [Guild1] has started',
            'created_at' => now(),
            'processed' => false,
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::BREAK,
            'guild_id' => 1,
            'edition' => 1,
            'message' => 'Guild [Guild1] has broken',
            'created_at' => now()->addSeconds(10),
            'processed' => false,
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'edition' => 1,
            'message' => 'Guild [Guild1] has ended',
            'created_at' => now()->addSeconds(20),
            'processed' => false,
        ]);

        $action->handle('Kriemhild', today(), 1, false);

        $score = GameWoeScore::firstWhere('guild_id', 1);

        $this->assertEquals(GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_FIRST_BREAK, $score->guild_score);
    }

    public function test_with_multiple_guilds()
    {
        // Create events for Guild1
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'message' => 'Guild [Guild1]',
            'created_at' => now(),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'message' => 'Guild [Guild1]',
            'created_at' => now()->addSeconds(5),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::BREAK,
            'guild_id' => 2,
            'message' => 'Guild [Guild1]',
            'created_at' => now()->addSeconds(10),
            'processed' => false
        ]);

        // Create events for Guild2
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::BREAK,
            'guild_id' => 1,
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 2,
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(30),
            'processed' => false
        ]);

        $action = new ProcessWoeEventPoints();
        $action->handle('Kriemhild', today(), 1, false);

        $guild1Score = GameWoeScore::firstWhere('guild_id', 1);
        $guild2Score = GameWoeScore::firstWhere('guild_id', 2);

        $this->assertEquals(GameWoeScore::POINTS_LONGEST_HELD, $guild1Score->guild_score);
        $this->assertEquals(GameWoeScore::POINTS_FIRST_BREAK + GameWoeScore::POINTS_ATTENDED, $guild2Score->guild_score);
    }
}
