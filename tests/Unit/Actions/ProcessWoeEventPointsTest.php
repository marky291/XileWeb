<?php

namespace Tests\Unit\Actions;

use App\Actions\ProcessWoeEventPoints;
use App\Exceptions\WoeEventNotEnoughEventsToProcessException;
use App\Exceptions\WoeMissingEventException;
use App\Ragnarok\GameWoeEvent;
use App\Ragnarok\GameWoeScore;
use App\Ragnarok\Guild;
use App\Ragnarok\GuildCastle;
use App\WoeEvents\WoeEventScoreRecorder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProcessWoeEventPointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_events()
    {
        $this->expectException(WoeEventNotEnoughEventsToProcessException::class);

        $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());

        $action->handle('Kriemhild');

        $this->assertDatabaseCount('game_woe_scores', 0);
    }

    public function test_single_event()
    {
        $this->expectException(WoeEventNotEnoughEventsToProcessException::class);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild1]',
            'created_at' => Carbon::now(),
            'processed' => false
        ]);
       $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $action->handle('Kriemhild');
        $this->assertDatabaseCount('game_woe_scores', 0);
    }

    public function test_multiple_events_same_guilds()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        $start_event = GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'The [Swanhild] castle is currently held by the [PHP Unit Test] guild.',
            'created_at' => Carbon::now(),
            'processed' => false
        ]);
        /** @var GameWoeEvent $end_event */
        $end_event = GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'The [Swanhild] castle has been conquered by the [PHP Unit Test] guild.',
            'created_at' => Carbon::now()->addMinutes(20),
            'processed' => false
        ]);
        $recorder = (new ProcessWoeEventPoints(new WoeEventScoreRecorder))->handle('Kriemhild');

        $this->assertDatabaseCount('game_woe_scores', 1);

        $this->assertEquals(1, $recorder->longest_hold_guild->guild_id);
        $this->assertEquals(1, $recorder->winning_guild->guild_id);
    }

    public function test_points_are_calculated_correctly()
    {
        $this->withExceptionHandling();

        $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());

        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'edition' => 1,
            'message' => 'Guild [Guild1] has started',
            'created_at' => now(),
            'processed' => false,
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::BREAK,
            'guild_id' => 1,
            'season' => now()->format('n'),
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
            'season' => now()->format('n'),
            'message' => 'Guild [Guild1] has ended',
            'created_at' => now()->addSeconds(20),
            'processed' => false,
        ]);

        $action->handle('Kriemhild');

        $score = GameWoeScore::firstWhere('guild_id', 1);

        $this->assertEquals(GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_FIRST_BREAK + GameWoeScore::POINTS_CASTLE_OWNER, $score->guild_score);
    }

    public function test_with_multiple_guilds()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        Guild::factory()->create([
            'guild_id' => 2,
            'name' => "Rohan Legends",
            'master' => 'Rohan',
            'char_id' => '150001'
        ]);

        // Create events for Guild1
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild1]',
            'created_at' => now(),
            'processed' => false,
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'message' => 'Guild [Guild1] has attended castle [Kriemhild] with member count [4].',
            'season' => now()->format('n'),
            'created_at' => now()->addSeconds(5),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::BREAK,
            'guild_id' => 2,
            'message' => 'Guild [Guild1]',
            'created_at' => now()->addSeconds(10),
            'season' => now()->format('n'),
            'processed' => false
        ]);

        // Create events for Guild2
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::BREAK,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(30),
            'processed' => false
        ]);

       $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $action->handle('Kriemhild', today());

        $guild1Score = GameWoeScore::firstWhere('guild_id', 1);
        $guild2Score = GameWoeScore::firstWhere('guild_id', 2);

        $this->assertEquals(GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_CASTLE_OWNER, $guild1Score->guild_score);
        $this->assertEquals(GameWoeScore::POINTS_FIRST_BREAK + GameWoeScore::POINTS_ATTENDED, $guild2Score->guild_score);
    }

    public function test_CastleOwnerIsAwardedExtraPoints()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        // Create initial events for a guild
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild2]',
            'created_at' => now(),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        // Act
       $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $action->handle('Kriemhild', today(), 1);

        // Assert
        $gameWoeScore = GameWoeScore::first();
        $this->assertNotNull($gameWoeScore);
        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD, $gameWoeScore->guild_score);
    }


    /** @test */
    public function it_does_not_carry_score_across_two_castles()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(0),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Swanhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(0),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Swanhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        (new ProcessWoeEventPoints(new WoeEventScoreRecorder()))->handle('Kriemhild', today(), 1);
        (new ProcessWoeEventPoints(new WoeEventScoreRecorder()))->handle('Swanhild', today(), 1);

        $scores = GameWoeScore::all();

        $this->assertCount(2, $scores);

        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD, $scores[0]->guild_score);
        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD, $scores[1]->guild_score);
    }

    /** @test */
    public function it_gives_1_point_to_attended_guild_with_multiple_attendences()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        Guild::factory()->create([
            'guild_id' => 2,
            'name' => "Rohan Legends",
            'master' => 'Rohan',
            'char_id' => '150001'
        ]);

        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'The [Skoegul] castle is currently held by the [Viexens] guild.',
            'created_at' => now(),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Viexens] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(1),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(2),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(3),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(4),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(5),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(6),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(7),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(8),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(9),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(10),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended castle [Skoegul] with member count [8].',
            'created_at' => now()->addMinutes(11),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'The [Skoegul] castle has been conquered by the [Viexens] guild.',
            'created_at' => now()->addMinutes(12),
            'processed' => false
        ]);

        (new ProcessWoeEventPoints(new WoeEventScoreRecorder()))->handle('Skoegul', today(), 1);

        $scores = GameWoeScore::all();

        $this->assertCount(2, $scores);

        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_ATTENDED, $scores[0]->guild_score);
        $this->assertEquals(GameWoeScore::POINTS_ATTENDED, $scores[1]->guild_score);

    }

    /** @test */
    public function it_tests_that_the_xilero_gm_team_guild_does_not_record_score()
    {
        $this->expectException(WoeEventNotEnoughEventsToProcessException::class);

        Guild::factory()->create([
            'guild_id' => 1,
            'name' => Guild::GM_TEAM,
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::HLJOD,
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => "The [Swanhild] castle is currently held by the [" . Guild::GM_TEAM . "] guild.",
            'created_at' => now(),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::HLJOD,
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => "The [Swanhild] castle has been conquered by the [" . Guild::GM_TEAM . "] guild.",
            'created_at' => now()->addMinutes(1),
            'processed' => false
        ]);

        (new ProcessWoeEventPoints(new WoeEventScoreRecorder()))->handle(GuildCastle::HLJOD);

        $scores = GameWoeScore::all();

        $this->assertCount(0, $scores);
    }
    /** @test */
    public function it_correctly_displays_woe_2_scores()
    {
        Guild::factory()->create([
            'guild_id' => 1053,
            'name' => 'Viexens',
            'master' => 'Stone Called',
            'char_id' => '181405'
        ]);

        Guild::factory()->create([
            'guild_id' => 736,
            'name' => 'Viexens',
            'master' => 'Stone Called',
            'char_id' => '181405'
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::HLJOD,
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1053,
            'season' => now()->format('n'),
            'message' => "The [Hljod] castle is currently held by the [Viexens] guild.",
            'created_at' => now(),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::HLJOD,
            'event' => GameWoeEvent::BREAK,
            'guild_id' => 736,
            'season' => now()->format('n'),
            'message' => "[Agony] of the [Viexens] guild has conquered the [Nithafjoll 4] stronghold of Hljod!",
            'created_at' => now()->addMinutes(5),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::HLJOD,
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1053,
            'season' => now()->format('n'),
            'message' => "The [Hljod] castle has been conquered by the [Viexens] guild.",
            'created_at' => now()->addMinutes(10),
            'processed' => false
        ]);

        (new ProcessWoeEventPoints(new WoeEventScoreRecorder()))->handle(GuildCastle::HLJOD, today(), 1);

        $scores = GameWoeScore::all();

        $this->assertCount(2, $scores);
    }

    /** @test */
   public function it_tests_that_the_longest_held_castle_is_vixens()
   {
       Guild::factory()->create([
           'guild_id' => 736,
           'name' => 'Viexens',
           'master' => 'Stone Called',
           'char_id' => '181405'
       ]);
       Guild::factory()->create([
           'guild_id' => 1021,
           'name' => '"XileRO Team"',
           'master' => 'Marky',
           'char_id' => '150000'
       ]);
       Guild::factory()->create([
           'guild_id' => 1039,
           'name' => 'l M M O R T A L S',
           'master' => 'Undying',
           'char_id' => '187465'
       ]);

       GameWoeEvent::create([
           'castle' => GuildCastle::SWANHILD,
           'event' => GameWoeEvent::STARTED,
           'guild_id' => 1021,
           'season' => now()->format('n'),
           'edition' => 1,
           'message' => 'The [Swanhild] castle is currently held by the ["XileRO Team"] guild.',
           'created_at' => now(),
           'processed' => false
       ]);
       GameWoeEvent::create([
           'castle' => GuildCastle::SWANHILD,
           'event' => GameWoeEvent::BREAK,
           'guild_id' => 1039,
           'edition' => 1,
           'player' => '187277',
           'season' => now()->format('n'),
           'message' => 'Castle [Swanhild] has been captured by [imAssassin] for Guild [l M M O R T A L S]',
           'created_at' => now()->addMinutes(5),
           'processed' => false
       ]);
       GameWoeEvent::create([
           'castle' => GuildCastle::SWANHILD,
           'event' => GameWoeEvent::BREAK,
           'player' => '182820',
           'edition' => 1,
           'guild_id' => 736,
           'season' => now()->format('n'),
           'message' => 'Castle [Swanhild] has been captured by [Boy Cocaine] for Guild [Viexens]',
           'created_at' => now()->addMinutes(10),
           'processed' => false
       ]);
       GameWoeEvent::create([
           'castle' => GuildCastle::SWANHILD,
           'event' => GameWoeEvent::ATTENDED,
           'player' => '181405',
           'edition' => 1,
           'guild_id' => 736,
           'season' => now()->format('n'),
           'message' => 'Guild [Viexens] has attended castle [Swanhild] with member count [8].',
           'created_at' => now()->addMinutes(15),
           'processed' => false
       ]);
       GameWoeEvent::create([
           'castle' => GuildCastle::SWANHILD,
           'event' => GameWoeEvent::BREAK,
           'guild_id' => 1039,
           'season' => now()->format('n'),
           'player' => '187277',
           'message' => 'Castle [Swanhild] has been captured by [imAssassin] for Guild [l M M O R T A L S]',
           'edition' => 1,
           'created_at' => now()->addMinutes(25),
           'processed' => false
       ]);
       GameWoeEvent::create([
           'castle' => GuildCastle::SWANHILD,
           'event' => GameWoeEvent::BREAK,
           'guild_id' => 736,
           'edition' => 1,
           'player' => '182820',
           'season' => now()->format('n'),
           'message' => 'Castle [Swanhild] has been captured by [Boy Cocaine] for Guild [Viexens]',
           'created_at' => now()->addMinutes(30),
           'processed' => false
       ]);
       GameWoeEvent::create([
           'castle' => GuildCastle::SWANHILD,
           'event' => GameWoeEvent::ATTENDED,
           'guild_id' => 736,
           'edition' => 1,
           'player' => '181405',
           'season' => now()->format('n'),
           'message' => 'Guild [Viexens] has attended castle [Swanhild] with member count [8].',
           'created_at' => now()->addMinutes(60),
           'processed' => false
       ]);
       GameWoeEvent::create([
           'castle' => GuildCastle::SWANHILD,
           'event' => GameWoeEvent::ATTENDED,
           'edition' => 1,
           'guild_id' => 736,
           'player' => '181405',
           'season' => now()->format('n'),
           'message' => 'Guild [Viexens] has attended castle [Swanhild] with member count [8].',
           'created_at' => now()->addMinutes(65),
           'processed' => false
       ]);
       GameWoeEvent::create([
           'castle' => GuildCastle::SWANHILD,
           'event' => GameWoeEvent::ENDED,
           'edition' => 1,
           'guild_id' => 736,
           'season' => now()->format('n'),
           'message' => 'The [Swanhild] castle has been conquered by the [Viexens] guild.',
           'created_at' => now()->addMinutes(70),
           'processed' => false
       ]);

       (new ProcessWoeEventPoints(new WoeEventScoreRecorder()))->handle(GuildCastle::SWANHILD);

       $scores = GameWoeScore::all();

       $this->assertCount(2, $scores);

       // Scores will order based on the guild score, so the array could be mixed if your test points are weird.
       $this->assertEquals(GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_ATTENDED + GameWoeScore::POINTS_CASTLE_OWNER, $scores[0]->guild_score);
       $this->assertEquals(GameWoeScore::POINTS_FIRST_BREAK, $scores[1]->guild_score);
   }


   /** @test */
    public function it_does_not_run_if_there_is_no_start_and_end_date()
    {
        $this->expectException(WoeMissingEventException::class);

        Guild::factory()->create([
            'guild_id' => 736,
            'name' => 'Viexens',
            'master' => 'Stone Called',
            'char_id' => '181405'
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::HLJOD,
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 736,
            'season' => now()->format('n'),
            'message' => "The [Hljod] castle is currently held by the [Viexens] guild.",
            'created_at' => now(),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::HLJOD,
            'event' => GameWoeEvent::BREAK,
            'guild_id' => 736,
            'season' => now()->format('n'),
            'message' => "[Agony] of the [Viexens] guild has conquered the [Nithafjoll 4] stronghold of Hljod!",
            'created_at' => now()->addMinutes(5),
            'processed' => false
        ]);

        (new ProcessWoeEventPoints(new WoeEventScoreRecorder()))->handle(GuildCastle::HLJOD, today(), 1);

        $scores = GameWoeScore::all();

        $this->assertCount(0, $scores);
    }

    public function test_no_woe_events()
    {
        $this->expectException(WoeEventNotEnoughEventsToProcessException::class);
       $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $action->handle('NonExistentCastle', now());
        $this->assertDatabaseCount('game_woe_scores', 0);
    }

    public function test_events_without_end_event()
    {
        $this->expectException(WoeEventNotEnoughEventsToProcessException::class);
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'message' => 'Guild [Guild1]',
            'created_at' => Carbon::now(),
            'processed' => false
        ]);
       $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $action->handle('Kriemhild', now());
        $this->assertDatabaseCount('game_woe_scores', 0);
    }

    public function test_events_without_start_event()
    {
        $this->expectException(WoeEventNotEnoughEventsToProcessException::class);
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'message' => 'Guild [Guild1]',
            'created_at' => Carbon::now(),
            'processed' => false
        ]);
       $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $action->handle('Kriemhild');
        $this->assertDatabaseCount('game_woe_scores', 0);
    }

    public function test_events_spanning_multiple_days_can_be_tallied()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild1]',
            'created_at' => Carbon::now()->subDay(),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild1]',
            'created_at' => Carbon::now(),
            'processed' => false
        ]);
       $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $action->handle('Kriemhild', now());
        $this->assertDatabaseCount('game_woe_scores', 1);
    }

    /** @test */
    public function it_handles_scores_based_on_the_current_month_as_season()
    {
        // Simulate events for the month of March
        $march = Carbon::createFromDate(null, 3, 1);

        Carbon::setTestNow($march);

        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        Guild::factory()->create([
            'guild_id' => 2,
            'name' => "Xeleros Brothers 2",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        // Create events for Guild1 in March
        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'message' => 'Guild [Guild1]',
            'created_at' => $march,
            'processed' => false,
            'season' => 3,
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'message' => 'Guild [Guild1]',
            'created_at' => $march->copy()->addMinutes(10),
            'processed' => false,
            'season' => 3,
        ]);

        // Process events for March
       $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $action->handle(GuildCastle::KRIEMHILD, $march, 3);

        // Assert scores for March
        $marchScore = GameWoeScore::firstWhere(['guild_id' => 1, 'season' => 3, 'castle_name' => GuildCastle::KRIEMHILD]);
        $this->assertNotNull($marchScore);
        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD, $marchScore->guild_score);

        // Simulate events for the month of April
        $april = Carbon::createFromDate(null, 4, 1);

        Carbon::setTestNow($april);

        // Create events for Guild1 and Guild2 in April
        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'message' => 'Guild [Guild1]',
            'created_at' => $april,
            'processed' => false,
            'season' => 4,
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::BREAK,
            'guild_id' => 2,
            'message' => 'Guild [Guild2]',
            'created_at' => $april->copy()->addMinutes(5),
            'processed' => false,
            'season' => 4,
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 2,
            'message' => 'Guild [Guild2]',
            'created_at' => $april->copy()->addMinutes(14),
            'processed' => false,
            'season' => 4,
        ]);

        // Process events for April
        $action->handle(GuildCastle::KRIEMHILD, $april, 4);

        // Assert scores for April
        $aprilScore = GameWoeScore::firstWhere(['guild_id' => 2, 'season' => 4, 'castle_name' => GuildCastle::KRIEMHILD]);
        $this->assertNotNull($aprilScore);
        $this->assertEquals(
            GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_FIRST_BREAK,
            $aprilScore->guild_score
        );

        // Ensure scores are not carried over from March to April
        $marchScoreAfterApril = GameWoeScore::firstWhere(['guild_id' => 1, 'season' => 3, 'castle_name' => GuildCastle::KRIEMHILD]);
        $this->assertNotNull($marchScoreAfterApril);
        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD, $marchScoreAfterApril->guild_score);
    }

    public function test_woe_events_with_real_database_data()
    {
        // Creating guilds for the test
        Guild::factory()->create([
            'guild_id' => 1140,
            'name' => "LatinEvolution",
            'master' => 'Master1',
            'char_id' => '111111'
        ]);

        Guild::factory()->create([
            'guild_id' => 1149,
            'name' => "Gantz",
            'master' => 'Master2',
            'char_id' => '222222'
        ]);

        Guild::factory()->create([
            'guild_id' => 1322,
            'name' => "Bimbingan OrangTua",
            'master' => 'Master3',
            'char_id' => '333333'
        ]);

        // Creating the events
        GameWoeEvent::create([
            'id' => 57,
            'message' => 'The [Kriemhild] castle is currently held by the [LatinEvolution] guild.',
            'castle' => 'Kriemhild',
            'guild_id' => 1140,
            'season' => 1,
            'event' => 'start',
            'edition' => 2,
            'processed' => false,
            'created_at' => '2024-06-08 14:00:02',
            'updated_at' => '2024-06-08 14:00:02'
        ]);

        GameWoeEvent::create([
            'id' => 58,
            'message' => 'Castle [Kriemhild] has been captured by [Suzuki] of the [Gantz] guild',
            'castle' => 'Kriemhild',
            'guild_id' => 1149,
            'season' => 1,
            'event' => 'break',
            'edition' => 1,
            'processed' => false,
            'player' => 196979,
            'created_at' => '2024-06-08 14:02:37',
            'updated_at' => '2024-06-08 14:02:37'
        ]);

        GameWoeEvent::create([
            'id' => 59,
            'message' => 'Castle [Kriemhild] has been captured by [Tea +] of the [Bimbingan OrangTua] guild',
            'castle' => 'Kriemhild',
            'guild_id' => 1322,
            'season' => 1,
            'event' => 'break',
            'edition' => 1,
            'processed' => false,
            'player' => 153865,
            'created_at' => '2024-06-08 14:39:11',
            'updated_at' => '2024-06-08 14:39:11'
        ]);

        GameWoeEvent::create([
            'id' => 60,
            'message' => 'Castle [Kriemhild] has been captured by [Suzuki] of the [Gantz] guild',
            'castle' => 'Kriemhild',
            'guild_id' => 1149,
            'season' => 1,
            'event' => 'break',
            'edition' => 1,
            'processed' => false,
            'player' => 196979,
            'created_at' => '2024-06-08 14:48:03',
            'updated_at' => '2024-06-08 14:48:03'
        ]);

        GameWoeEvent::create([
            'id' => 61,
            'message' => 'Castle [Kriemhild] has been captured by [Ling] of the [Bimbingan OrangTua] guild',
            'castle' => 'Kriemhild',
            'guild_id' => 1322,
            'season' => 1,
            'event' => 'break',
            'edition' => 1,
            'processed' => false,
            'player' => 211371,
            'created_at' => '2024-06-08 14:57:30',
            'updated_at' => '2024-06-08 14:57:30'
        ]);

        GameWoeEvent::create([
            'id' => 62,
            'message' => 'The [Kriemhild] castle has been conquered by the [Bimbingan OrangTua] guild.',
            'castle' => 'Kriemhild',
            'guild_id' => 1322,
            'season' => 1,
            'event' => 'end',
            'edition' => 2,
            'processed' => false,
            'created_at' => '2024-06-08 15:00:01',
            'updated_at' => '2024-06-08 15:00:01'
        ]);

        // Process the events
        $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $recorder = $action->handle('Kriemhild');

        // Assert the scores
        $scores = GameWoeScore::all();

        $this->assertCount(2, $scores);

        $latinEvolutionScore = GameWoeScore::firstWhere('guild_id', 1140);
        $gantzScore = GameWoeScore::firstWhere('guild_id', 1149);
        $bimbinganOrangTuaScore = GameWoeScore::firstWhere('guild_id', 1322);

        $this->assertEquals(GameWoeScore::POINTS_FIRST_BREAK + GameWoeScore::POINTS_LONGEST_HELD, $gantzScore->guild_score);
        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER, $bimbinganOrangTuaScore->guild_score);
    }

    public function test_woe_events_with_real_database_data_for_hljod()
    {
        // Creating guilds for the test
        $this->createGuild(1140, "LatinEvolution", "Master1", '111111');
        $this->createGuild(1149, "Gantz", "Master2", '222222');
        $this->createGuild(1322, "Bimbingan OrangTua", "Master3", '333333');
        $this->createGuild(1365, "FrateIIi", "Master4", '444444');
        $this->createGuild(1241, "Unity", "Master5", '555555');

        // Creating the events for Kriemhild
        $this->createEvent(57, 'The [Kriemhild] castle is currently held by the [LatinEvolution] guild.', 'Kriemhild', 1140, 1, 'start', 2, true, '2024-06-08 14:00:02');
        $this->createEvent(58, 'Castle [Kriemhild] has been captured by [Suzuki] of the [Gantz] guild', 'Kriemhild', 1149, 1, 'break', 1, true, '2024-06-08 14:02:37', 196979);
        $this->createEvent(59, 'Castle [Kriemhild] has been captured by [Tea +] of the [Bimbingan OrangTua] guild', 'Kriemhild', 1322, 1, 'break', 1, true, '2024-06-08 14:39:11', 153865);
        $this->createEvent(60, 'Castle [Kriemhild] has been captured by [Suzuki] of the [Gantz] guild', 'Kriemhild', 1149, 1, 'break', 1, true, '2024-06-08 14:48:03', 196979);
        $this->createEvent(61, 'Castle [Kriemhild] has been captured by [Ling] of the [Bimbingan OrangTua] guild', 'Kriemhild', 1322, 1, 'break', 1, true, '2024-06-08 14:57:30', 211371);
        $this->createEvent(62, 'The [Kriemhild] castle has been conquered by the [Bimbingan OrangTua] guild.', 'Kriemhild', 1322, 1, 'end', 2, true, '2024-06-08 15:00:01');

        // Creating the events for Hljod
        $this->createEvent(63, 'The [Hljod] castle is currently held by the [Gantz] guild.', 'Hljod', 1149, 1, 'start', 2, false, '2024-06-09 14:00:03');
        $this->createEvent(64, '[Ling] of the [Bimbingan OrangTua] guild has conquered the [Nithafjoll 4] stronghold of Hljod!', 'Hljod', 1322, 1, 'break', 1, false, '2024-06-09 14:36:37', 211371);
        $this->createEvent(65, '[Zenitsu] of the [FrateIIi] guild has conquered the [Nithafjoll 4] stronghold of Hljod!', 'Hljod', 1365, 1, 'break', 1, false, '2024-06-09 14:43:06', 219332);
        $this->createEvent(66, '[Ling] of the [Bimbingan OrangTua] guild has conquered the [Nithafjoll 4] stronghold of Hljod!', 'Hljod', 1322, 1, 'break', 1, false, '2024-06-09 14:49:14', 211371);
        $this->createEvent(67, '[Andres Bonifacio] of the [Unity] guild has conquered the [Nithafjoll 4] stronghold of Hljod!', 'Hljod', 1241, 1, 'break', 1, false, '2024-06-09 14:56:59', 182246);
        $this->createEvent(68, '[Ling] of the [Bimbingan OrangTua] guild has conquered the [Nithafjoll 4] stronghold of Hljod!', 'Hljod', 1322, 1, 'break', 1, false, '2024-06-09 14:58:28', 211371);
        $this->createEvent(69, 'The [Hljod] castle has been conquered by the [Bimbingan OrangTua] guild.', 'Hljod', 1322, 1, 'end', 2, false, '2024-06-09 15:00:01');

        // Process the events
        $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $recorder = $action->handle('Hljod');

        // Assert the scores
        $scores = GameWoeScore::all();

        $this->assertCount(2, $scores); // Ensure all guilds that participated are accounted for

        $latinEvolutionScore = GameWoeScore::firstWhere('guild_id', 1140);
        $gantzScore = GameWoeScore::firstWhere('guild_id', 1149);
        $bimbinganOrangTuaScore = GameWoeScore::firstWhere('guild_id', 1322);
        $frateIIiScore = GameWoeScore::firstWhere('guild_id', 1365);
        $unityScore = GameWoeScore::firstWhere('guild_id', 1241);

        $this->assertEquals(GameWoeScore::POINTS_LONGEST_HELD, $gantzScore->guild_score);
        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_FIRST_BREAK, $bimbinganOrangTuaScore->guild_score);
    }

    /**
     * Helper method to create a guild.
     */
    private function createGuild(int $guildId, string $name, string $master, string $charId)
    {
        Guild::factory()->create([
            'guild_id' => $guildId,
            'name' => $name,
            'master' => $master,
            'char_id' => $charId
        ]);
    }

    /**
     * Helper method to create an event.
     */
    private function createEvent(
        int $id,
        string $message,
        string $castle,
        int $guildId,
        int $season,
        string $event,
        int $edition,
        bool $processed,
        string $createdAt,
        int $player = null
    )
    {
        GameWoeEvent::create([
            'id' => $id,
            'message' => $message,
            'castle' => $castle,
            'guild_id' => $guildId,
            'season' => $season,
            'event' => $event,
            'edition' => $edition,
            'processed' => $processed,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'player' => $player
        ]);
    }

    public function test_guild_with_most_kills_gets_point()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        // Create initial events for a guild
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild2]',
            'created_at' => now(),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::KILLED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Player [Killer Player] of guild [Xeleros Brother] has killed [Dead Player]!',
            'created_at' => now(),
            'processed' => false,
            'event_rid' => 2000000
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Guild2]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        // Act
        $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $action->handle('Kriemhild', today(), 1);

        // Assert
        $gameWoeScore = GameWoeScore::first();
        $this->assertNotNull($gameWoeScore);
        $this->assertEquals(GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_GUILD_MOST_KILLS, $gameWoeScore->guild_score);
    }

    public function test_guild_with_most_kills_earns_most_kills_point()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        Guild::factory()->create([
            'guild_id' => 2,
            'name' => "Rohan Legends",
            'master' => 'Rohan',
            'char_id' => '150001'
        ]);

        // Create initial events for both guilds
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Xeleros Brothers]',
            'created_at' => now(),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::KILLED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Player [Killer Player 1] of guild [Xeleros Brothers] has killed [Dead Player 1]!',
            'created_at' => now()->addSeconds(10),
            'processed' => false,
            'event_rid' => 2000001
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::KILLED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Player [Killer Player 2] of guild [Xeleros Brothers] has killed [Dead Player 2]!',
            'created_at' => now()->addSeconds(20),
            'processed' => false,
            'event_rid' => 2000002
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::KILLED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Player [Killer Player 3] of guild [Rohan Legends] has killed [Dead Player 3]!',
            'created_at' => now()->addSeconds(30),
            'processed' => false,
            'event_rid' => 2000003
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Xeleros Brothers]',
            'created_at' => now()->addSeconds(40),
            'processed' => false
        ]);

        // Act
        $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $recorder = $action->handle('Kriemhild', today());

        // Assert
        $gameWoeScore = GameWoeScore::where('guild_id', 1)->first();
        $this->assertNotNull($gameWoeScore);
        $expectedScore = GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_GUILD_MOST_KILLS;
        $this->assertEquals($expectedScore, $gameWoeScore->guild_score);

        $gameWoeScoreRohan = GameWoeScore::where('guild_id', 2)->first();
        $this->assertNull($gameWoeScoreRohan); // Rohan Legends shouldn't have any points since they didn't own the castle or hold it the longest.
    }

    public function test_tie_in_kills()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        Guild::factory()->create([
            'guild_id' => 2,
            'name' => "Rohan Legends",
            'master' => 'Rohan',
            'char_id' => '150001'
        ]);

        // Create initial events for both guilds
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Xeleros Brothers]',
            'created_at' => now(),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::KILLED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Player [Killer Player 1] of guild [Xeleros Brothers] has killed [Dead Player 1]!',
            'created_at' => now()->addSeconds(10),
            'processed' => false,
            'event_rid' => 2000001
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::KILLED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Player [Killer Player 2] of guild [Rohan Legends] has killed [Dead Player 2]!',
            'created_at' => now()->addSeconds(20),
            'processed' => false,
            'event_rid' => 2000002
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::KILLED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Player [Killer Player 3] of guild [Xeleros Brothers] has killed [Dead Player 3]!',
            'created_at' => now()->addSeconds(30),
            'processed' => false,
            'event_rid' => 2000003
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::KILLED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Player [Killer Player 4] of guild [Rohan Legends] has killed [Dead Player 4]!',
            'created_at' => now()->addSeconds(40),
            'processed' => false,
            'event_rid' => 2000004
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Xeleros Brothers]',
            'created_at' => now()->addSeconds(50),
            'processed' => false
        ]);

        // Act
        $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $recorder = $action->handle('Kriemhild', today());

        // Assert
        $gameWoeScore1 = GameWoeScore::where('guild_id', 1)->first();
        $gameWoeScore2 = GameWoeScore::where('guild_id', 2)->first();

        $this->assertNotNull($gameWoeScore1);
        $this->assertNotNull($gameWoeScore2);

        $expectedScore1 = GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD + GameWoeScore::POINTS_GUILD_MOST_KILLS;
        $expectedScore2 = GameWoeScore::POINTS_GUILD_MOST_KILLS;

        $this->assertEquals($expectedScore1, $gameWoeScore1->guild_score);
        $this->assertEquals($expectedScore2, $gameWoeScore2->guild_score);
    }

    public function test_no_kills()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        // Create initial events for a guild
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Xeleros Brothers]',
            'created_at' => now(),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Xeleros Brothers]',
            'created_at' => now()->addSeconds(20),
            'processed' => false
        ]);

        // Act
        $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $recorder = $action->handle('Kriemhild', today());

        // Assert
        $gameWoeScore = GameWoeScore::first();
        $this->assertNotNull($gameWoeScore);
        $expectedScore = GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD;
        $this->assertEquals($expectedScore, $gameWoeScore->guild_score);
    }

    public function test_guild_with_most_kills_gets_point_even_without_castle()
    {
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => "Xeleros Brothers",
            'master' => 'Marky',
            'char_id' => '150000'
        ]);

        Guild::factory()->create([
            'guild_id' => 2,
            'name' => "Rohan Legends",
            'master' => 'Rohan',
            'char_id' => '150001'
        ]);

        Guild::factory()->create([
            'guild_id' => 3,
            'name' => "Mighty Warriors",
            'master' => 'Warrior',
            'char_id' => '150002'
        ]);

        // Create initial events for the guilds
        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Xeleros Brothers]',
            'created_at' => now(),
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::KILLED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Player [Killer Player 1] of guild [Rohan Legends] has killed [Dead Player 1]!',
            'created_at' => now()->addSeconds(10),
            'processed' => false,
            'event_rid' => 2000001
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::KILLED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Player [Killer Player 2] of guild [Rohan Legends] has killed [Dead Player 2]!',
            'created_at' => now()->addSeconds(20),
            'processed' => false,
            'event_rid' => 2000002
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [Xeleros Brothers]',
            'created_at' => now()->addSeconds(30),
            'processed' => false
        ]);

        // Act
        $action = new ProcessWoeEventPoints(new WoeEventScoreRecorder());
        $recorder = $action->handle('Kriemhild', today());

        // Assert
        $gameWoeScore1 = GameWoeScore::where('guild_id', 1)->first();
        $gameWoeScore2 = GameWoeScore::where('guild_id', 2)->first();

        $this->assertNotNull($gameWoeScore1);
        $this->assertNotNull($gameWoeScore2);

        $expectedScore1 = GameWoeScore::POINTS_CASTLE_OWNER + GameWoeScore::POINTS_LONGEST_HELD;
        $expectedScore2 = GameWoeScore::POINTS_GUILD_MOST_KILLS;

        $this->assertEquals($expectedScore1, $gameWoeScore1->guild_score);
        $this->assertEquals($expectedScore2, $gameWoeScore2->guild_score);
    }

}
