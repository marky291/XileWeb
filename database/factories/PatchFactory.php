<?php

namespace Database\Factories;

use App\Models\Patch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patch>
 */
class PatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numberBetween(1, 10000),
            'type' => fake()->randomElement(['FLD', 'GRF']),
            'client' => fake()->randomElement([Patch::CLIENT_XILERO, Patch::CLIENT_RETRO]),
            'patch_name' => fake()->sentence(3),
            'file' => fake()->slug().'.'.fake()->randomElement(['fld', 'grf']),
            'comments' => fake()->optional()->paragraph(),
        ];
    }
}
