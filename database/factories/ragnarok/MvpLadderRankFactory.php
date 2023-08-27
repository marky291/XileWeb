<?php

declare(strict_types=1);

namespace Database\Factories\ragnarok;

use App\Ragnarok\MvpLadderRank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Ragnarok\MvpLadderRank>
 */
final class MvpLadderRankFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = MvpLadderRank::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'day_kills' => fake()->randomNumber(),
            'week_kills' => fake()->randomNumber(),
            'month_kills' => fake()->randomNumber(),
            'all_kills' => fake()->randomNumber(),
        ];
    }
}
