<?php

namespace Database\Factories\Ragnarok;

use App\Ragnarok\Login;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LoginFactory extends Factory
{
    protected $model = Login::class;

    public function definition(): array
    {
        return [
            'userid' => $this->faker->userName(),
            'user_pass' => $this->faker->password(),
            'sex' => $this->faker->randomElement(['M', 'F']),
            'email' => $this->faker->unique()->safeEmail(),
            'group_id' => 0,
            'state' => 0,
            'unban_time' => 0,
            'expiration_time' => 0,
            'logincount' => 0,
            'lastlogin' => null,
            'last_ip' => '',
            'birthdate' => null,
            'character_slots' => 0,
            'pincode' => '',
            'pincode_change' => 0,
            'vip_time' => 0,
            'old_group' => 0,
            'web_auth_token' => null,
            'web_auth_token_enabled' => 0,
            'remember_token' => null,
            'email_verified_at' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'group_id' => 99,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }
}
