<?php

namespace Database\Factories;

use App\Models\DonationLog;
use App\Models\DonationRewardClaim;
use App\Models\DonationRewardTier;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DonationRewardClaim>
 */
class DonationRewardClaimFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'donation_log_id' => DonationLog::factory(),
            'donation_reward_tier_id' => DonationRewardTier::factory(),
            'item_id' => Item::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'refine_level' => $this->faker->numberBetween(0, 10),
            'status' => DonationRewardClaim::STATUS_PENDING,
            'claimed_at' => null,
            'claimed_account_id' => null,
            'claimed_char_name' => null,
            'is_xilero' => true,
            'is_xileretro' => true,
            'expires_at' => null,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function forTier(DonationRewardTier $tier): static
    {
        return $this->state(fn (array $attributes) => [
            'donation_reward_tier_id' => $tier->id,
            'is_xilero' => $tier->is_xilero,
            'is_xileretro' => $tier->is_xileretro,
        ]);
    }

    public function forItem(Item $item): static
    {
        return $this->state(fn (array $attributes) => [
            'item_id' => $item->id,
        ]);
    }

    public function forDonationLog(DonationLog $log): static
    {
        return $this->state(fn (array $attributes) => [
            'donation_log_id' => $log->id,
            'user_id' => $log->user_id,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DonationRewardClaim::STATUS_PENDING,
            'claimed_at' => null,
            'claimed_account_id' => null,
            'claimed_char_name' => null,
        ]);
    }

    public function claimed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DonationRewardClaim::STATUS_CLAIMED,
            'claimed_at' => now(),
            'claimed_account_id' => $this->faker->numberBetween(2000000, 2999999),
            'claimed_char_name' => $this->faker->userName(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DonationRewardClaim::STATUS_EXPIRED,
            'expires_at' => now()->subDay(),
        ]);
    }

    public function expiresIn(int $days): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addDays($days),
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
}
