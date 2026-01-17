<?php

namespace Database\Factories;

use App\Models\GameAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SyncedCharacter>
 */
class SyncedCharacterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'game_account_id' => GameAccount::factory(),
            'char_id' => fake()->unique()->numberBetween(150000, 999999),
            'name' => fake()->userName(),
            'class_name' => fake()->randomElement(['Swordsman', 'Mage', 'Archer', 'Acolyte', 'Thief', 'Merchant', 'Knight', 'Wizard', 'Hunter', 'Priest', 'Assassin', 'Blacksmith']),
            'base_level' => fake()->numberBetween(1, 255),
            'job_level' => fake()->numberBetween(1, 120),
            'zeny' => fake()->numberBetween(0, 999999999),
            'last_map' => fake()->randomElement(['prontera', 'geffen', 'payon', 'morroc', 'alberta', 'izlude']),
            'guild_name' => fake()->optional(0.3)->company(),
            'online' => fake()->boolean(10),
            'synced_at' => now(),
        ];
    }

    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'online' => true,
        ]);
    }

    public function offline(): static
    {
        return $this->state(fn (array $attributes) => [
            'online' => false,
        ]);
    }

    public function inGuild(string $guildName): static
    {
        return $this->state(fn (array $attributes) => [
            'guild_name' => $guildName,
        ]);
    }
}
