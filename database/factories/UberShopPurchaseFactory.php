<?php

namespace Database\Factories;

use App\Models\UberShopItem;
use App\Models\UberShopPurchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UberShopPurchase>
 */
class UberShopPurchaseFactory extends Factory
{
    protected $model = UberShopPurchase::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => fake()->numberBetween(1, 100000),
            'account_name' => fake()->userName(),
            'shop_item_id' => UberShopItem::factory(),
            'item_id' => fake()->numberBetween(1000, 99999),
            'item_name' => fake()->words(3, true),
            'refine_level' => 0,
            'quantity' => 1,
            'uber_cost' => fake()->numberBetween(1, 100),
            'uber_balance_after' => fake()->numberBetween(0, 1000),
            'status' => UberShopPurchase::STATUS_PENDING,
            'purchased_at' => now(),
            'claimed_at' => null,
            'claimed_by_char_id' => null,
            'claimed_by_char_name' => null,
            'is_xileretro' => fake()->boolean(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UberShopPurchase::STATUS_PENDING,
            'claimed_at' => null,
        ]);
    }

    public function claimed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now(),
            'claimed_by_char_id' => fake()->numberBetween(1, 100000),
            'claimed_by_char_name' => fake()->userName(),
        ]);
    }
}
