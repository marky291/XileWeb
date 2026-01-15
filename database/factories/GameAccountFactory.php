<?php

namespace Database\Factories;

use App\Actions\MakeHashedLoginPassword;
use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameAccountFactory extends Factory
{
    protected $model = GameAccount::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'server' => 'xilero',
            'ragnarok_account_id' => $this->faker->unique()->numberBetween(2000000, 9999999),
            'userid' => $this->faker->unique()->userName(),
            'user_pass' => MakeHashedLoginPassword::run('password'),
            'email' => $this->faker->unique()->safeEmail(),
            'sex' => $this->faker->randomElement(['M', 'F']),
            'group_id' => 0,
            'state' => 0,
            'uber_balance' => 0,
            'has_security_code' => false,
        ];
    }

    public function withSecurityCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_security_code' => true,
        ]);
    }

    public function xileretro(): static
    {
        return $this->state(fn (array $attributes) => [
            'server' => 'xileretro',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'group_id' => 99,
        ]);
    }
}
