<?php

declare(strict_types=1);

namespace Database\Factories\Ragnarok;

use App\Ragnarok\GuildMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Ragnarok\GuildMember>
 */
final class GuildMemberFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = GuildMember::class;

    /**
    * Define the model's default state.
    *
    * @return array
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
