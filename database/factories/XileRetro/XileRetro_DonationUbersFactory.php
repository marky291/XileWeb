<?php

namespace Database\Factories\XileRetro;

use App\XileRetro\XileRetro_DonationUbers;
use Illuminate\Database\Eloquent\Factories\Factory;

class XileRetro_DonationUbersFactory extends Factory
{
    protected $model = XileRetro_DonationUbers::class;

    public function definition(): array
    {
        return [
            'account_id' => $this->faker->unique()->numberBetween(2000000, 9999999),
            'username' => $this->faker->userName(),
            'current_ubers' => 0,
            'pending_ubers' => 0,
        ];
    }

    public function withUbers(int $current = 50, int $pending = 50): static
    {
        return $this->state(fn (array $attributes) => [
            'current_ubers' => $current,
            'pending_ubers' => $pending,
        ]);
    }
}
