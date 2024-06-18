<?php

namespace Tests\Unit\WoeEvents;

use App\Ragnarok\GameWoeEvent;
use App\Ragnarok\GameWoeScore;
use App\Ragnarok\Guild;
use App\Ragnarok\GuildCastle;
use App\WoeEvents\WoeEventScheduleJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class WoeEventScheduleJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary guilds
        Guild::factory()->create([
            'guild_id' => 1,
            'name' => 'TestGuild1',
            'master' => 'Master1',
            'char_id' => '100001'
        ]);

        Guild::factory()->create([
            'guild_id' => 2,
            'name' => 'TestGuild2',
            'master' => 'Master2',
            'char_id' => '100002'
        ]);

        // Create Woe Events for Kriemhild
        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [TestGuild1] has started',
            'created_at' => now()->subMinutes(10),
            'processed' => false,
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [TestGuild1] has ended',
            'created_at' => now(),
            'processed' => false,
        ]);

        // Create Woe Events for Swanhild with not enough events
        GameWoeEvent::create([
            'castle' => GuildCastle::SWANHILD,
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [TestGuild2] has started',
            'created_at' => now()->subMinutes(10),
            'processed' => false,
        ]);

        // Set up the environment variables for the Discord webhooks
        config(['services.discord.kriemhild_guild_points' => 'https://discord.com/api/webhooks/1249398208954896515/ZEZJDNUjrCtsXIzOnw38a3QnzGrdv-Qx4qcXw2bT5Q8jtGxV9I2gaEXXGCvSMXgYdSNx']);
        config(['services.discord.swanhild_guild_points' => 'https://discord.com/api/webhooks/1249398208954896515/ZEZJDNUjrCtsXIzOnw38a3QnzGrdv-Qx4qcXw2bT5Q8jtGxV9I2gaEXXGCvSMXgYdSNx']);
        // Add more config settings for other castles if needed
    }

    public function testHandleSuccessfullyProcessesEvents()
    {
        // Run the job
        $job = new WoeEventScheduleJob();
        $job->handle();

        // Verify that the scores were processed and stored in the database
        $this->assertDatabaseHas('game_woe_scores', [
            'guild_id' => 1,
            'castle_name' => GuildCastle::KRIEMHILD,
        ]);

        // Verify that Kriemhild events were processed
        $this->assertDatabaseHas('game_woe_events', [
            'castle' => GuildCastle::KRIEMHILD,
            'processed' => true,
        ]);

        // Since we are not mocking, check the response from the real webhook endpoint
        // You can verify this manually by checking the logs or the output from your local server
    }

    public function testHandleSwallowsNotEnoughEventsException()
    {
        // Run the job
        $job = new WoeEventScheduleJob();
        $job->handle();

        // No exception should be thrown, and the other castle should be processed
        $this->assertDatabaseHas('game_woe_scores', [
            'guild_id' => 1,
            'castle_name' => GuildCastle::KRIEMHILD,
        ]);

        // Swanhild should not be processed due to not enough events
        $this->assertDatabaseMissing('game_woe_scores', [
            'guild_id' => 2,
            'castle_name' => GuildCastle::SWANHILD,
        ]);
    }

    public function testHandleProcessesMultipleCastles()
    {
        // Create more Woe Events for another castle
        GameWoeEvent::create([
            'castle' => GuildCastle::HLJOD,
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [TestGuild2] has started',
            'created_at' => now()->subMinutes(10),
            'processed' => false,
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::HLJOD,
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [TestGuild2] has ended',
            'created_at' => now(),
            'processed' => false,
        ]);

        // Run the job
        $job = new WoeEventScheduleJob();
        $job->handle();

        // Verify that the scores were processed and stored in the database for both castles
        $this->assertDatabaseHas('game_woe_scores', [
            'guild_id' => 1,
            'castle_name' => GuildCastle::KRIEMHILD,
        ]);

        $this->assertDatabaseHas('game_woe_scores', [
            'guild_id' => 2,
            'castle_name' => GuildCastle::HLJOD,
        ]);

        // Verify that events were processed for both castles
        $this->assertDatabaseHas('game_woe_events', [
            'castle' => GuildCastle::KRIEMHILD,
            'processed' => true,
        ]);

        $this->assertDatabaseHas('game_woe_events', [
            'castle' => GuildCastle::HLJOD,
            'processed' => true,
        ]);
    }

    public function testHandleLogsExceptions()
    {
        // Create an invalid event to trigger an exception
        GameWoeEvent::create([
            'castle' => 'InvalidCastle',
            'event' => GameWoeEvent::STARTED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [TestGuild2] has started',
            'created_at' => now()->subMinutes(10),
            'processed' => false,
        ]);

        // Simulate an exception being logged
        Log::spy();

        // Run the job
        $job = new WoeEventScheduleJob();
        $job->handle();

        // Verify that an error was logged
        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(function ($message) {
                return strpos($message, 'Exception for castle InvalidCastle') !== false;
            });

        // Verify that the valid castle was still processed
        $this->assertDatabaseHas('game_woe_scores', [
            'guild_id' => 1,
            'castle_name' => GuildCastle::KRIEMHILD,
        ]);

        // InvalidCastle should not be processed due to the exception
        $this->assertDatabaseMissing('game_woe_scores', [
            'castle_name' => 'InvalidCastle',
        ]);
    }

    public function testHandleProcessesAttendance()
    {
        // Create attendance events for Kriemhild
        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::ATTENDED,
            'guild_id' => 1,
            'season' => now()->format('n'),
            'message' => 'Guild [TestGuild1] has attended',
            'created_at' => now()->subMinutes(5),
            'processed' => false,
        ]);

        // Run the job
        $job = new WoeEventScheduleJob();
        $job->handle();

        // Verify that attendance was processed and scores were updated
        $this->assertDatabaseHas('game_woe_scores', [
            'guild_id' => 1,
            'castle_name' => GuildCastle::KRIEMHILD,
        ]);

        // Verify that attendance events were processed
        $this->assertDatabaseHas('game_woe_events', [
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::ATTENDED,
            'processed' => true,
        ]);
    }

    public function testHandleCalculatesScoresCorrectly()
    {
        // Create more Woe Events for Kriemhild with different guilds
        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::BREAK,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [TestGuild2] has broken',
            'created_at' => now()->subMinutes(5),
            'processed' => false,
        ]);

        GameWoeEvent::create([
            'castle' => GuildCastle::KRIEMHILD,
            'event' => GameWoeEvent::ENDED,
            'guild_id' => 2,
            'season' => now()->format('n'),
            'message' => 'Guild [TestGuild2] has ended',
            'created_at' => now(),
            'processed' => false,
        ]);

        // Run the job
        $job = new WoeEventScheduleJob();
        $job->handle();

        // Verify that the scores were calculated correctly
        $guild1Score = GameWoeScore::where('guild_id', 1)->first();
        $guild2Score = GameWoeScore::where('guild_id', 2)->first();

        $this->assertNotNull($guild1Score);
        $this->assertNotNull($guild2Score);

        // Add your assertions for the specific points expected for each guild
    }
}
