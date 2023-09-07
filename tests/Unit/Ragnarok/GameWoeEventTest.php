<?php

namespace Tests\Unit\Ragnarok;

use App\Ragnarok\GameWoeEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameWoeEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_message_guild_break_guild_name()
    {
        $event = GameWoeEvent::factory()->break()->make([
            'message' => "Castle [Kriemhild] has been captured by [Test2] for Guild [XileRO]",
        ]);

        $this->assertEquals('XileRO', $event->guild_name_from_message);
    }

    public function test_message_guild_break_guild_castle_name()
    {
        $event = GameWoeEvent::factory()->break()->make([
            'message' => "Castle [Kriemhild] has been captured by [Test2] for Guild [XileRO]",
        ]);

        $this->assertEquals('Kriemhild', $event->castle_name_from_message);
    }

    public function test_message_guild_break_guild_player_name()
    {
        $event = GameWoeEvent::factory()->break()->make([
            'message' => "Castle [Kriemhild] has been captured by [Test2] for Guild [XileRO]",
        ]);

        $this->assertEquals('Test2', $event->player_name_from_message);
    }
}
