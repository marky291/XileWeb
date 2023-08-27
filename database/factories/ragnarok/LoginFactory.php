<?php

namespace Database\Factories\ragnarok;

use App\Ragnarok\Login;
use Database\Factories\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoginFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Login::class; // Please update the namespace to match your Login model

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'userid' => $this->faker->userName,
            'user_pass' => $this->faker->password,
            'sex' => $this->faker->randomElement(['M', 'F', 'S']),
            'email' => $this->faker->safeEmail,
            'group_id' => $this->faker->numberBetween(0, 99),
            'state' => $this->faker->numberBetween(0, 1000000),
            'unban_time' => $this->faker->numberBetween(0, 1000000),
            'expiration_time' => $this->faker->numberBetween(0, 1000000),
            'logincount' => $this->faker->numberBetween(0, 999999),
            'lastlogin' => $this->faker->dateTime,
            'last_ip' => $this->faker->ipv4,
            'birthdate' => $this->faker->date,
            'character_slots' => $this->faker->numberBetween(0, 255),
            'pincode' => $this->faker->numerify('####'),
            'pincode_change' => $this->faker->numberBetween(0, 1000000),
            'vip_time' => $this->faker->numberBetween(0, 1000000),
            'old_group' => $this->faker->numberBetween(0, 9),
            'web_auth_token' => \Illuminate\Support\Str::random(17),
            'web_auth_token_enabled' => $this->faker->numberBetween(0, 1),
            'last_unique_id' => $this->faker->numberBetween(0, 1000000),
            'blocked_unique_id' => $this->faker->numberBetween(0, 1000000),
        ];
    }
}
