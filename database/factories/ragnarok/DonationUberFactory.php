<?php

declare(strict_types=1);

namespace Database\Factories\ragnarok;

use App\Ragnarok\DonationUber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Ragnarok\DonationUber>
 */
final class DonationUberFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = DonationUber::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'id' => fake()->randomNumber(),
            'account_id' => fake()->randomNumber(),
            'username' => fake()->userName,
            'current_ubers' => fake()->optional()->randomNumber(),
            'pending_ubers' => fake()->optional()->randomNumber(),
            'updated_at' => fake()->optional()->dateTime(),
            'created_at' => fake()->optional()->dateTime(),
        ];
    }
}
