<?php

declare(strict_types=1);

namespace Database\Factories\XileRO;

use App\XileRO\XileRO_GuildCastle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\XileRO\GuildCastle>
 */
final class XileRO_GuildCastleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = XileRO_GuildCastle::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'castle_id' => fake()->randomNumber(),
            'guild_id' => \App\XileRO\XileRO_Guild::factory(),
            'economy' => fake()->randomNumber(),
            'defense' => fake()->randomNumber(),
            'triggerE' => fake()->randomNumber(),
            'triggerD' => fake()->randomNumber(),
            'nextTime' => fake()->randomNumber(),
            'payTime' => fake()->randomNumber(),
            'createTime' => fake()->randomNumber(),
            'visibleC' => fake()->randomNumber(),
            'visibleG0' => fake()->randomNumber(),
            'visibleG1' => fake()->randomNumber(),
            'visibleG2' => fake()->randomNumber(),
            'visibleG3' => fake()->randomNumber(),
            'visibleG4' => fake()->randomNumber(),
            'visibleG5' => fake()->randomNumber(),
            'visibleG6' => fake()->randomNumber(),
            'visibleG7' => fake()->randomNumber(),
        ];
    }
}
