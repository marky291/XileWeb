<?php

declare(strict_types=1);

namespace Database\Factories\XileRO;

use App\XileRO\XileRO_GuildMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\XileRO\GuildMember>
 */
final class XileRO_GuildMemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = XileRO_GuildMember::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'guild_id' => fake()->randomNumber(),
            'char_id' => fake()->randomNumber(),
            'exp' => fake()->randomNumber(),
            'position' => fake()->boolean,
        ];
    }
}
