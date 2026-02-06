<?php

namespace Database\Factories;

use App\Models\DonationRewardTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DonationRewardTier>
 */
class DonationRewardTierFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true).' Tier',
            'description' => $this->faker->optional()->sentence(),
            'minimum_amount' => $this->faker->randomElement([10.00, 25.00, 50.00, 100.00]),
            'is_cumulative' => false,
            'claim_reset_period' => null,
            'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
            'is_xilero' => true,
            'is_xileretro' => true,
            'enabled' => true,
            'display_order' => 0,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }

    public function cumulative(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_cumulative' => true,
        ]);
    }

    public function lifetime(): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => DonationRewardTier::TRIGGER_LIFETIME,
        ]);
    }

    public function perDonation(): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
        ]);
    }

    public function xileroOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_xilero' => true,
            'is_xileretro' => false,
        ]);
    }

    public function xileretroOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_xilero' => false,
            'is_xileretro' => true,
        ]);
    }

    public function minimumAmount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'minimum_amount' => $amount,
        ]);
    }

    public function resetsDaily(): static
    {
        return $this->state(fn (array $attributes) => [
            'claim_reset_period' => DonationRewardTier::RESET_DAILY,
        ]);
    }

    public function resetsWeekly(): static
    {
        return $this->state(fn (array $attributes) => [
            'claim_reset_period' => DonationRewardTier::RESET_WEEKLY,
        ]);
    }

    public function resetsMonthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'claim_reset_period' => DonationRewardTier::RESET_MONTHLY,
        ]);
    }

    public function resetsYearly(): static
    {
        return $this->state(fn (array $attributes) => [
            'claim_reset_period' => DonationRewardTier::RESET_YEARLY,
        ]);
    }
}
