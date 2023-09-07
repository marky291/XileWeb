<?php

namespace Database\Factories\ragnarok;

use App\Enum\WoeEventType;
use App\Ragnarok\GameWoeEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventWoe>
 */
class GameWoeEventFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GameWoeEvent::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event' => GameWoeEvent::BREAK,
            'message' => 'Castle [Kriemhild] has been captured by [Test] for Guild [AnotherTest]',
            'castle' => 'Kriemhild',
            'edition' => 1,
            'guild_id' => 6,
            'player' => 150000,
            'discord_sent' => 0,
            'processed' => 0,
        ];
    }

    public function started(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => GameWoeEvent::STARTED,
            'message' => 'Castle [Kriemhild] has been captured by [Test] for Guild [AnotherTest]',
            'castle' => 'Kriemhild',
            'edition' => 1,
            'guild_id' => 6,
            'player' => 150000,
            'discord_sent' => 0,
            'processed' => 0,
        ]);
    }

    public function break(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => GameWoeEvent::BREAK,
            'message' => 'Castle [Kriemhild] has been captured by [Test] for Guild [AnotherTest]',
            'castle' => 'Kriemhild',
            'edition' => 1,
            'guild_id' => 6,
            'player' => 150000,
            'discord_sent' => 0,
            'processed' => 0,
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => GameWoeEvent::ENDED,
            'message' => 'Castle [Kriemhild] has been captured by [Test] for Guild [AnotherTest]',
            'castle' => 'Kriemhild',
            'edition' => 1,
            'guild_id' => 6,
            'player' => 150000,
            'discord_sent' => 0,
            'processed' => 0,
        ]);
    }
}
