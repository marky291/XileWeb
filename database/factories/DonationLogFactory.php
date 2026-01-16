<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DonationLog>
 */
class DonationLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseUbers = $this->faker->randomElement([50, 110, 250, 600, 1500]);
        $bonusUbers = (int) floor($baseUbers * 0.1);

        return [
            'user_id' => User::factory(),
            'admin_id' => User::factory()->admin(),
            'amount' => $this->faker->randomElement([10, 20, 40, 80, 150]),
            'payment_method' => $this->faker->randomElement(['paypal', 'crypto', 'cashapp', 'venmo']),
            'base_ubers' => $baseUbers,
            'bonus_ubers' => $bonusUbers,
            'total_ubers' => $baseUbers + $bonusUbers,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function byAdmin(User $admin): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_id' => $admin->id,
        ]);
    }
}
