<?php

namespace Tests\Unit;

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
            'processed' => false
        ]);

        GameWoeEvent::create([
            'castle' => 'Kriemhild',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'message' => 'Guild [Guild1]',
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
        $action->handle('Kriemhild', today(), 1, false);

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
            'message' => 'Guild [Viexens] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(1),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(2),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(3),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(4),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(5),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(6),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(7),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(8),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(9),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
            'created_at' => now()->addMinutes(10),
            'processed' => false
        ]);
        GameWoeEvent::create([
            'castle' => 'Skoegul',
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [.Bounty-Hunters.] has attended with member count greater than size [8].',
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
           'message' => 'Guild [Viexens] has attended with member count greater than size [8].',
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
           'message' => 'Guild [Viexens] has attended with member count greater than size [8].',
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
           'message' => 'Guild [Viexens] has attended with member count greater than size [8].',
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
        $action->handle('NonExistentCastle', new \DateTime(), 1, false);
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
        $action->handle('Kriemhild', new \DateTime(), 1);
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
        $action->handle('Kriemhild', new \DateTime(), 1);
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
}
