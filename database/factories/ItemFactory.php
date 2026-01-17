<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => fake()->unique()->numberBetween(501, 99999),
            'aegis_name' => fake()->unique()->regexify('[A-Z][a-z]+_[A-Z][a-z]+'),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['Healing', 'Usable', 'Etc', 'Armor', 'Weapon', 'Card', 'Pet', 'Ammo']),
            'subtype' => null,
            'weight' => fake()->numberBetween(0, 1000),
            'buy' => fake()->numberBetween(0, 100000),
            'sell' => fake()->numberBetween(0, 50000),
            'attack' => fake()->numberBetween(0, 500),
            'defense' => fake()->numberBetween(0, 100),
            'slots' => fake()->numberBetween(0, 4),
            'refineable' => fake()->boolean(30),
            'jobs' => null,
            'locations' => null,
            'flags' => null,
            'trade' => null,
            'script' => null,
            'equip_script' => null,
            'unequip_script' => null,
            'is_xileretro' => false,
        ];
    }

    public function xileretro(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_xileretro' => true,
        ]);
    }

    public function weapon(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Weapon',
            'attack' => fake()->numberBetween(50, 500),
            'refineable' => true,
        ]);
    }

    public function armor(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Armor',
            'defense' => fake()->numberBetween(10, 100),
            'refineable' => true,
        ]);
    }

    public function healing(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Healing',
            'attack' => 0,
            'defense' => 0,
            'slots' => 0,
            'refineable' => false,
        ]);
    }
}
