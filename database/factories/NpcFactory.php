<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Npc>
 */
class NpcFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'npc_id' => fake()->unique()->numberBetween(1, 99999),
            'name' => fake()->words(2, true),
            'sprite' => fake()->regexify('[A-Z_]+'),
        ];
    }
}
