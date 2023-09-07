<?php

namespace Database\Factories\ragnarok;

use App\Ragnarok\Guild;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Ragnarok\GameWoeScore>
 */
class GameWoeScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'season' => 1,
            'guild_id' => Guild::factory(),
            'guild_score' => 0,
        ];
    }
}
