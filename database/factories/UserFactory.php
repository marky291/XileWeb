<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'max_game_accounts' => 6,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withDiscord(): static
    {
        return $this->state(fn (array $attributes) => [
            'discord_id' => $this->faker->unique()->numerify('####################'),
            'discord_username' => $this->faker->userName().'#'.$this->faker->numerify('####'),
            'discord_avatar' => $this->faker->imageUrl(128, 128),
            'discord_token' => $this->faker->sha256(),
            'discord_refresh_token' => $this->faker->sha256(),
        ]);
    }
}
