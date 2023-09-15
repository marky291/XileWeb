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

        $this->assertEquals(GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_FIRST_BREAK + GameWoeScore::POINTS_CASTLE_OWNER, $score->guild_score);
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
            'guild_id' => 1,
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(30),
            'processed' => false
        ]);

        $action = new ProcessWoeEventPoints();
        $action->handle('Kriemhild', today(), 1, false);

        $guild1Score = GameWoeScore::firstWhere('guild_id', 1);
        $guild2Score = GameWoeScore::firstWhere('guild_id', 2);

        $this->assertEquals(GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_CASTLE_OWNER, $guild1Score->guild_score);
        $this->assertEquals(GameWoeScore::POINTS_FIRST_BREAK + GameWoeScore::POINTS_ATTENDED, $guild2Score->guild_score);
    }

    public function test_CastleOwnerIsAwardedExtraPoints()
    {
        // Create initial events for a guild
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        // Act
        $action = new ProcessWoeEventPoints();
        $action->handle('Kriemhild', today(), 1);

        // Assert
        $gameWoeScore = GameWoeScore::first();
        $this->assertNotNull($gameWoeScore);
        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD, $gameWoeScore->guild_score);
    }

    /** @test */
    public function it_does_not_carry_score_across_two_castles()
    {
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => 1,
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(0),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => 1,
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Swanhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => 1,
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(0),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Swanhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => 1,
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        (new ProcessWoeEventPoints())->handle('Kriemhild', today(), 1);
        (new ProcessWoeEventPoints())->handle('Swanhild', today(), 1);

        $scores = GameWoeScore::all();

        $this->assertCount(2, $scores);

        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD, $scores[0]->guild_score);
        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD, $scores[1]->guild_score);
    }

    /** @test */
    public function it_gives_1_point_to_attended_guild_with_multiple_attendences()
    {
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => 1,
            'message' => 'The [Skoegul] castle is currently held by the [Viexens] guild.',
            'created_at' => now(),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 1,
            'season' => 1,
            'message' => 'Guild [Viexens] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(1),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => 1,
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(2),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => 1,
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(3),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => 1,
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(4),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => 1,
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(5),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => 1,
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(6),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => 1,
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(7),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => 1,
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(8),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => 1,
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(9),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => 1,
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(10),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => 1,
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(10),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => 1,
            'message' => 'The [Skoegul] castle has been conquered by the [Viexens] guild.',
            'created_at' => now()->addMinutes(11),
            'processed' => false
        ]);

        (new ProcessWoeEventPoints())->handle('Skoegul', today(), 1);

        $scores = GameWoeScore::all();

        $this->assertCount(2, $scores);

        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_ATTENDED, $scores[0]->guild_score);
        $this->assertEquals(GameWoeScore::POINTS_ATTENDED, $scores[1]->guild_score);

    }
}
