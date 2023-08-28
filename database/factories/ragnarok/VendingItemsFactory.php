<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Ragnarok\VendingItems;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Ragnarok\VendingItems>
 */
final class VendingItemsFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = VendingItems::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'account_id' => fake()->randomNumber(),
            'char_id' => fake()->randomNumber(),
            'sex' => fake()->randomElement(['F', 'M']),
            'map' => fake()->word,
            'x' => fake()->randomNumber(),
            'y' => fake()->randomNumber(),
            'title' => fake()->title,
            'body_direction' => fake()->word,
            'head_direction' => fake()->word,
            'sit' => fake()->word,
            'autotrade' => fake()->boolean,
        ];
    }
}
