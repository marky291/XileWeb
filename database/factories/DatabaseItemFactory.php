<?php

namespace Database\Factories;

use App\Models\DatabaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DatabaseItem>
 */
class DatabaseItemFactory extends Factory
{
    protected $model = DatabaseItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => fake()->unique()->numberBetween(1000, 99999),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'aegis_name' => fake()->slug(),
            'item_type' => fake()->randomElement(['Weapon', 'Armor', 'Card', 'Costume', 'Consumable']),
            'item_subtype' => fake()->randomElement(['Sword', 'Bow', 'Headgear', 'Shield', null]),
            'slots' => fake()->numberBetween(0, 4),
            'weight' => fake()->numberBetween(0, 1000),
            'attack' => fake()->numberBetween(0, 500),
            'defense' => fake()->numberBetween(0, 100),
            'equip_level_min' => fake()->numberBetween(0, 175),
            'weapon_level' => fake()->numberBetween(0, 5),
            'equip_locations' => null,
            'jobs' => null,
            'buy_price' => fake()->numberBetween(100, 100000),
            'sell_price' => fake()->numberBetween(50, 50000),
            'icon_path' => null,
            'collection_path' => null,
            'client_icon' => null,
            'client_collection' => null,
        ];
    }

    public function weapon(): static
    {
        return $this->state(fn (array $attributes) => [
            'item_type' => 'Weapon',
            'item_subtype' => fake()->randomElement(['Sword', 'Bow', 'Staff', 'Dagger']),
            'attack' => fake()->numberBetween(50, 300),
            'defense' => 0,
        ]);
    }

    public function armor(): static
    {
        return $this->state(fn (array $attributes) => [
            'item_type' => 'Armor',
            'item_subtype' => fake()->randomElement(['Shield', 'Headgear', 'Garment', 'Footgear']),
            'attack' => 0,
            'defense' => fake()->numberBetween(10, 100),
        ]);
    }

    public function card(): static
    {
        return $this->state(fn (array $attributes) => [
            'item_type' => 'Card',
            'item_subtype' => null,
            'slots' => 0,
            'attack' => 0,
            'defense' => 0,
        ]);
    }
}
