<?php

declare(strict_types=1);

namespace Database\Factories\XileRO;

use App\XileRO\XileRO_Guild;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\XileRO\Guild>
 */
final class XileRO_GuildFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = XileRO_Guild::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'char_id' => fake()->randomNumber(),
            'master' => fake()->word,
            'guild_lv' => fake()->boolean,
            'connect_member' => fake()->boolean,
            'max_member' => fake()->boolean,
            'average_lv' => fake()->randomNumber(),
            'exp' => fake()->randomNumber(),
            'next_exp' => fake()->randomNumber(),
            'skill_point' => fake()->boolean,
            'mes1' => fake()->word,
            'mes2' => fake()->word,
            'emblem_len' => fake()->randomNumber(),
            'emblem_id' => fake()->randomNumber(),
            'emblem_data' => fake()->optional()->word,
            'last_master_change' => fake()->optional()->dateTime(),
        ];
    }
}
