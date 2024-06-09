<?php

namespace Tests\Unit\WoeEvents;

use App\Ragnarok\GameWoeEvent;
use App\Ragnarok\GameWoeScore;
use App\Ragnarok\Guild;
use App\WoeEvents\WoeEventDiscordMessage;
use App\WoeEvents\WoeEventScoreRecorder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class WoeEventDiscordMessageTest extends TestCase
{
    use RefreshDatabase;

    public function testHandle()
    {
        // Arrange
        $castle = 'Kriemhild';

        $scoring = $this->getMockBuilder(WoeEventScoreRecorder::class)
            ->onlyMethods(['leaderboard'])
            ->getMock();

        // Create real instances of Guild
        $winningGuild = Guild::factory()->create(['name' => 'Xeleros Brothers']);
        $longestHoldGuild = Guild::factory()->create(['name' => 'LongestHoldGuild']);
        $firstBreakGuild = Guild::factory()->create(['name' => 'FirstBreakGuild']);
        $attendeeGuild = Guild::factory()->create(['name' => 'AttendeeGuild']);

        // Set the properties of WoeEventScoreRecorder
        $scoring->castle = $castle;
        $scoring->season = now()->format('n');
        $scoring->winning_guild = $winningGuild;
        $scoring->winning_award = 100;
        $scoring->longest_hold_guild = $longestHoldGuild;
        $scoring->longest_hold_award = 50;
        $scoring->first_break_guild = $firstBreakGuild;
        $scoring->first_break_award = 25;
        $scoring->attendee_award = 10;
        $scoring->addAttendee($attendeeGuild);

        // Create real instances of GameWoeScore and populate the leaderboard
        $leaderboard = new Collection();
        $gameWoeScore = GameWoeScore::factory()->create([
            'castle_name' => $castle,
            'guild_name' => $winningGuild->name,
            'season' => now()->format('n'),
            'guild_score' => 200,
            'previous_score' => '0',
            'guild_id' => $winningGuild->guild_id
        ]);
        $leaderboard->push($gameWoeScore);

        // Mock the leaderboard method
        $scoring->method('leaderboard')
            ->willReturn($leaderboard);

        $woeEventDiscordMessage = new WoeEventDiscordMessage();

        // Act
        $message = $woeEventDiscordMessage->handle($scoring, $castle);

        // Assert
        $expectedMessage = ">>> -----------------------------------------------------------------------------\n";
        $expectedMessage .= "\n**Kriemhild Events:**\n";
        $expectedMessage .= "- `Xeleros Brothers`  won  **100** Points as [__Castle Owner__]\n";
        $expectedMessage .= "- `LongestHoldGuild`  earned  **50** Points for [__Longest Castle Defense__] \n";
        $expectedMessage .= "- `FirstBreakGuild`  took  **25** Points for [__First Castle Break__]\n";
        $expectedMessage .= "- `AttendeeGuild`  saw  **10** Point for [__Attendance__]\n";
        $expectedMessage .= "\n**Kriemhild Leaderboard:**\n";
        $expectedMessage .= "#1. `Xeleros Brothers`  with  `200 Points Total` (0)\n";
        $expectedMessage .= "\n-----------------------------------------------------------------------------\n";

        $this->assertEquals($expectedMessage, $message);
    }

    public function testHandleNoEvents()
    {
        // Arrange
        $castle = 'Kriemhild';

        $scoring = $this->getMockBuilder(WoeEventScoreRecorder::class)
            ->onlyMethods(['leaderboard'])
            ->getMock();

        // Mock the leaderboard method to return an empty collection
        $scoring->method('leaderboard')
            ->willReturn(new Collection());

        $woeEventDiscordMessage = new WoeEventDiscordMessage();

        // Act
        $message = $woeEventDiscordMessage->handle($scoring, $castle);

        // Assert
        $expectedMessage = "";
        $this->assertEquals($expectedMessage, $message);
    }

    public function testHandleOnlyWinningGuild()
    {
        // Arrange
        $castle = 'Kriemhild';

        $scoring = $this->getMockBuilder(WoeEventScoreRecorder::class)
            ->onlyMethods(['leaderboard'])
            ->getMock();

        // Create real instances of Guild
        $winningGuild = Guild::factory()->create(['name' => 'Xeleros Brothers']);

        // Set the properties of WoeEventScoreRecorder
        $scoring->castle = $castle;
        $scoring->season = now()->format('n');
        $scoring->winning_guild = $winningGuild;
        $scoring->winning_award = 100;

        // Mock the leaderboard method to return an empty collection
        $scoring->method('leaderboard')
            ->willReturn(new Collection());

        $woeEventDiscordMessage = new WoeEventDiscordMessage();

        // Act
        $message = $woeEventDiscordMessage->handle($scoring, $castle);

        // Assert
        $expectedMessage = ">>> -----------------------------------------------------------------------------\n";
        $expectedMessage .= "\n**Kriemhild Events:**\n";
        $expectedMessage .= "- `Xeleros Brothers`  won  **100** Points as [__Castle Owner__]\n";
        $expectedMessage .= "\n-----------------------------------------------------------------------------\n";

        $this->assertEquals($expectedMessage, $message);
    }

    public function testHandleOnlyLongestHoldGuild()
    {
        // Arrange
        $castle = 'Kriemhild';

        $scoring = $this->getMockBuilder(WoeEventScoreRecorder::class)
            ->onlyMethods(['leaderboard'])
            ->getMock();

        // Create real instances of Guild
        $longestHoldGuild = Guild::factory()->create(['name' => 'LongestHoldGuild']);

        // Set the properties of WoeEventScoreRecorder
        $scoring->castle = $castle;
        $scoring->season = now()->format('n');
        $scoring->longest_hold_guild = $longestHoldGuild;
        $scoring->longest_hold_award = 50;

        // Mock the leaderboard method to return an empty collection
        $scoring->method('leaderboard')
            ->willReturn(new Collection());

        $woeEventDiscordMessage = new WoeEventDiscordMessage();

        // Act
        $message = $woeEventDiscordMessage->handle($scoring, $castle);

        // Assert
        $expectedMessage = ">>> -----------------------------------------------------------------------------\n";
        $expectedMessage .= "\n**Kriemhild Events:**\n";
        $expectedMessage .= "- `LongestHoldGuild`  earned  **50** Points for [__Longest Castle Defense__] \n";
        $expectedMessage .= "\n-----------------------------------------------------------------------------\n";

        $this->assertEquals($expectedMessage, $message);
    }

    public function testHandleOnlyFirstBreakGuild()
    {
        // Arrange
        $castle = 'Kriemhild';

        $scoring = $this->getMockBuilder(WoeEventScoreRecorder::class)
            ->onlyMethods(['leaderboard'])
            ->getMock();

        // Create real instances of Guild
        $firstBreakGuild = Guild::factory()->create(['name' => 'FirstBreakGuild']);

        // Set the properties of WoeEventScoreRecorder
        $scoring->castle = $castle;
        $scoring->season = now()->format('n');
        $scoring->first_break_guild = $firstBreakGuild;
        $scoring->first_break_award = 25;

        // Mock the leaderboard method to return an empty collection
        $scoring->method('leaderboard')
            ->willReturn(new Collection());

        $woeEventDiscordMessage = new WoeEventDiscordMessage();

        // Act
        $message = $woeEventDiscordMessage->handle($scoring, $castle);

        // Assert
        $expectedMessage = ">>> -----------------------------------------------------------------------------\n";
        $expectedMessage .= "\n**Kriemhild Events:**\n";
        $expectedMessage .= "- `FirstBreakGuild`  took  **25** Points for [__First Castle Break__]\n";
        $expectedMessage .= "\n-----------------------------------------------------------------------------\n";

        $this->assertEquals($expectedMessage, $message);
    }

    public function testHandleMultipleAttendees()
    {
        // Arrange
        $castle = 'Kriemhild';

        $scoring = $this->getMockBuilder(WoeEventScoreRecorder::class)
            ->onlyMethods(['leaderboard'])
            ->getMock();

        // Create real instances of Guild
        $attendeeGuild1 = Guild::factory()->create(['name' => 'AttendeeGuild1']);
        $attendeeGuild2 = Guild::factory()->create(['name' => 'AttendeeGuild2']);

        // Set the properties of WoeEventScoreRecorder
        $scoring->castle = $castle;
        $scoring->season = now()->format('n');
        $scoring->attendee_award = 10;
        $scoring->addAttendee($attendeeGuild1);
        $scoring->addAttendee($attendeeGuild2);

        // Mock the leaderboard method to return an empty collection
        $scoring->method('leaderboard')
            ->willReturn(new Collection());

        $woeEventDiscordMessage = new WoeEventDiscordMessage();

        // Act
        $message = $woeEventDiscordMessage->handle($scoring, $castle);

        // Assert
        $expectedMessage = ">>> -----------------------------------------------------------------------------\n";
        $expectedMessage .= "\n**Kriemhild Events:**\n";
        $expectedMessage .= "- `AttendeeGuild1`  saw  **10** Point for [__Attendance__]\n";
        $expectedMessage .= "- `AttendeeGuild2`  saw  **10** Point for [__Attendance__]\n";
        $expectedMessage .= "\n-----------------------------------------------------------------------------\n";

        $this->assertEquals($expectedMessage, $message);
    }

    public function testHandleNoAttendees()
    {
        // Arrange
        $castle = 'Kriemhild';

        $scoring = $this->getMockBuilder(WoeEventScoreRecorder::class)
            ->onlyMethods(['leaderboard'])
            ->getMock();

        // Create real instances of Guild
        $winningGuild = Guild::factory()->create(['name' => 'Xeleros Brothers']);

        // Set the properties of WoeEventScoreRecorder
        $scoring->castle = $castle;
        $scoring->season = now()->format('n');
        $scoring->winning_guild = $winningGuild;
        $scoring->winning_award = 100;

        // Mock the leaderboard method to return an empty collection
        $scoring->method('leaderboard')
            ->willReturn(new Collection());

        $woeEventDiscordMessage = new WoeEventDiscordMessage();

        // Act
        $message = $woeEventDiscordMessage->handle($scoring, $castle);

        // Assert
        $expectedMessage = ">>> -----------------------------------------------------------------------------\n";
        $expectedMessage .= "\n**Kriemhild Events:**\n";
        $expectedMessage .= "- `Xeleros Brothers`  won  **100** Points as [__Castle Owner__]\n";
        $expectedMessage .= "\n-----------------------------------------------------------------------------\n";

        $this->assertEquals($expectedMessage, $message);
    }

    public function testHandleMultipleLeaderboardEntries()
    {
        // Arrange
        $castle = 'Kriemhild';

        $scoring = $this->getMockBuilder(WoeEventScoreRecorder::class)
            ->onlyMethods(['leaderboard'])
            ->getMock();

        // Create real instances of Guild
        $winningGuild = Guild::factory()->create(['name' => 'Xeleros Brothers']);
        $secondPlaceGuild = Guild::factory()->create(['name' => 'SecondPlaceGuild']);
        $thirdPlaceGuild = Guild::factory()->create(['name' => 'ThirdPlaceGuild']);

        // Set the properties of WoeEventScoreRecorder
        $scoring->castle = $castle;
        $scoring->season = now()->format('n');
        $scoring->winning_guild = $winningGuild;
        $scoring->winning_award = 100;

        // Create real instances of GameWoeScore and populate the leaderboard
        $leaderboard = new Collection();
        $gameWoeScore1 = GameWoeScore::factory()->create([
            'castle_name' => $castle,
            'guild_name' => $winningGuild->name,
            'season' => now()->format('n'),
            'guild_score' => 200,
            'previous_score' => '0',
            'guild_id' => $winningGuild->guild_id
        ]);
        $gameWoeScore2 = GameWoeScore::factory()->create([
            'castle_name' => $castle,
            'guild_name' => $secondPlaceGuild->name,
            'season' => now()->format('n'),
            'guild_score' => 150,
            'previous_score' => '0',
            'guild_id' => $secondPlaceGuild->guild_id
        ]);
        $gameWoeScore3 = GameWoeScore::factory()->create([
            'castle_name' => $castle,
            'guild_name' => $thirdPlaceGuild->name,
            'season' => now()->format('n'),
            'guild_score' => 100,
            'previous_score' => '0',
            'guild_id' => $thirdPlaceGuild->guild_id
        ]);
        $leaderboard->push($gameWoeScore1);
        $leaderboard->push($gameWoeScore2);
        $leaderboard->push($gameWoeScore3);

        // Mock the leaderboard method
        $scoring->method('leaderboard')
            ->willReturn($leaderboard);

        $woeEventDiscordMessage = new WoeEventDiscordMessage();

        // Act
        $message = $woeEventDiscordMessage->handle($scoring, $castle);

        // Assert
        $expectedMessage = ">>> -----------------------------------------------------------------------------\n";
        $expectedMessage .= "\n**Kriemhild Events:**\n";
        $expectedMessage .= "- `Xeleros Brothers`  won  **100** Points as [__Castle Owner__]\n";
        $expectedMessage .= "\n**Kriemhild Leaderboard:**\n";
        $expectedMessage .= "#1. `Xeleros Brothers`  with  `200 Points Total` (0)\n";
        $expectedMessage .= "#2. `SecondPlaceGuild`  with  `150 Points Total` (0)\n";
        $expectedMessage .= "#3. `ThirdPlaceGuild`  with  `100 Points Total` (0)\n";
        $expectedMessage .= "\n-----------------------------------------------------------------------------\n";

        $this->assertEquals($expectedMessage, $message);
    }

    public function testHandleNoLeaderboardEntriesButEventsPresent()
    {
        // Arrange
        $castle = 'Kriemhild';

        $scoring = $this->getMockBuilder(WoeEventScoreRecorder::class)
            ->onlyMethods(['leaderboard'])
            ->getMock();

        // Create real instances of Guild
        $winningGuild = Guild::factory()->create(['name' => 'Xeleros Brothers']);
        $longestHoldGuild = Guild::factory()->create(['name' => 'LongestHoldGuild']);
        $firstBreakGuild = Guild::factory()->create(['name' => 'FirstBreakGuild']);
        $attendeeGuild = Guild::factory()->create(['name' => 'AttendeeGuild']);

        // Set the properties of WoeEventScoreRecorder
        $scoring->castle = $castle;
        $scoring->season = now()->format('n');
        $scoring->winning_guild = $winningGuild;
        $scoring->winning_award = 100;
        $scoring->longest_hold_guild = $longestHoldGuild;
        $scoring->longest_hold_award = 50;
        $scoring->first_break_guild = $firstBreakGuild;
        $scoring->first_break_award = 25;
        $scoring->attendee_award = 10;
        $scoring->addAttendee($attendeeGuild);

        // Mock the leaderboard method to return an empty collection
        $scoring->method('leaderboard')
            ->willReturn(new Collection());

        $woeEventDiscordMessage = new WoeEventDiscordMessage();

        // Act
        $message = $woeEventDiscordMessage->handle($scoring, $castle);

        // Assert
        $expectedMessage = ">>> -----------------------------------------------------------------------------\n";
        $expectedMessage .= "\n**Kriemhild Events:**\n";
        $expectedMessage .= "- `Xeleros Brothers`  won  **100** Points as [__Castle Owner__]\n";
        $expectedMessage .= "- `LongestHoldGuild`  earned  **50** Points for [__Longest Castle Defense__] \n";
        $expectedMessage .= "- `FirstBreakGuild`  took  **25** Points for [__First Castle Break__]\n";
        $expectedMessage .= "- `AttendeeGuild`  saw  **10** Point for [__Attendance__]\n";
        $expectedMessage .= "\n-----------------------------------------------------------------------------\n";

        $this->assertEquals($expectedMessage, $message);
    }

    public function testHandleWithProvidedData()
    {
        // Arrange
        $castle = 'Kriemhild';

        // Create real instances of Guild
        $winningGuild = Guild::updateOrCreate(
            ['guild_id' => 1322],
            ['name' => 'Bimbingan OrangTua', 'char_id' => 333333, 'master' => 'Master3']
        );

        $longestHoldGuild = Guild::updateOrCreate(
            ['guild_id' => 1149],
            ['name' => 'Gantz', 'char_id' => 222222, 'master' => 'Master2']
        );

        $firstBreakGuild = Guild::updateOrCreate(
            ['guild_id' => 1149],
            ['name' => 'Gantz', 'char_id' => 222222, 'master' => 'Master2']
        );

        // Create real instances of GameWoeEvent
        $firstBreakEvent = GameWoeEvent::factory()->create([
            'id' => 2,
            'message' => 'Castle [Kriemhild] has been captured by [Suzuki] of the [Gantz] guild',
            'castle' => 'Kriemhild',
            'edition' => 1,
            'season' => 1,
            'event' => 'break',
            'guild_id' => 1149,
            'player' => 196979,
            'created_at' => '2024-06-08 14:02:37',
            'updated_at' => '2024-06-08 23:11:37'
        ]);

        $winningEvent = GameWoeEvent::factory()->create([
            'id' => 6,
            'message' => 'The [Kriemhild] castle has been conquered by the [Bimbingan OrangTua] guild.',
            'castle' => 'Kriemhild',
            'edition' => 2,
            'season' => 1,
            'event' => 'end',
            'guild_id' => 1322,
            'created_at' => '2024-06-08 15:00:01',
            'updated_at' => '2024-06-08 23:11:37'
        ]);

        // Set up the WoeEventScoreRecorder
        $scoring = new WoeEventScoreRecorder();
        $scoring->castle = $castle;
        $scoring->season = 6;
        $scoring->winning_guild = $winningGuild;
        $scoring->winning_award = 4;
        $scoring->longest_hold_guild = $longestHoldGuild;
        $scoring->longest_hold_award = 3;
        $scoring->first_break_guild = $firstBreakGuild;
        $scoring->first_break_event = $firstBreakEvent;
        $scoring->first_break_award = 2;
        $scoring->attendee_award = 0;

        // Create real instances of GameWoeScore and populate the leaderboard
        GameWoeScore::factory()->create([
            'castle_name' => $castle,
            'guild_name' => $winningGuild->name,
            'season' => 6,
            'previous_score' => '0',
            'guild_score' => 200,
            'guild_id' => $winningGuild->guild_id
        ]);
        GameWoeScore::factory()->create([
            'castle_name' => $castle,
            'guild_name' => $longestHoldGuild->name,
            'season' => 6,
            'guild_score' => 150,
            'previous_score' => '0',
            'guild_id' => $longestHoldGuild->guild_id
        ]);

        // Populate total scores for the leaderboard
        GameWoeScore::where('guild_id', $longestHoldGuild->guild_id)
            ->update(['guild_score' => 150 + 3 + 2]); // Adding the longest hold and first break awards
        GameWoeScore::where('guild_id', $winningGuild->guild_id)
            ->update(['guild_score' => 200 + 4]); // Adding the castle owner award

        $woeEventDiscordMessage = new WoeEventDiscordMessage();

        // Act
        $message = $woeEventDiscordMessage->handle($scoring, $castle);

        // Assert
        $expectedMessage = ">>> -----------------------------------------------------------------------------\n";
        $expectedMessage .= "\n**Kriemhild Events:**\n";
        $expectedMessage .= "- `Bimbingan OrangTua`  won  **4** Points as [__Castle Owner__]\n";
        $expectedMessage .= "- `Gantz`  earned  **3** Points for [__Longest Castle Defense__] \n";
        $expectedMessage .= "- `Gantz`  took  **2** Points for [__First Castle Break__]\n";
        $expectedMessage .= "\n**Kriemhild Leaderboard:**\n";
        $expectedMessage .= "#1. `Bimbingan OrangTua`  with  `204 Points Total` (0)\n";
        $expectedMessage .= "#2. `Gantz`  with  `155 Points Total` (0)\n";
        $expectedMessage .= "\n-----------------------------------------------------------------------------\n";

        $this->assertEquals($expectedMessage, $message);
    }
}
