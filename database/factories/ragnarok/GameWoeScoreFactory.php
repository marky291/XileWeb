<?php

namespace Database\Factories\ragnarok;

use App\Ragnarok\GameWoeScore;
use App\Ragnarok\Guild;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Ragnarok\GameWoeScore>
 */
class GameWoeScoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GameWoeScore::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'guild_name' => $this->faker->name,
            'season' => now()->format('n'),
            'guild_id' => Guild::factory(),
            'guild_score' => 0,
        ];
    }
}
