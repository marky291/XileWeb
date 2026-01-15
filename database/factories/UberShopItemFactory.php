<?php

namespace Database\Factories;

use App\Models\DatabaseItem;
use App\Models\UberShopCategory;
use App\Models\UberShopItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UberShopItem>
 */
class UberShopItemFactory extends Factory
{
    protected $model = UberShopItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => UberShopCategory::factory(),
            'database_item_id' => DatabaseItem::factory(),
            'display_name' => null,
            'uber_cost' => fake()->numberBetween(1, 100),
            'quantity' => 1,
            'refine_level' => 0,
            'stock' => null,
            'display_order' => fake()->numberBetween(0, 100),
            'enabled' => true,
            'is_xilero' => true,
            'is_xileretro' => false,
        ];
    }

    public function forXileRO(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_xilero' => true,
            'is_xileretro' => false,
        ]);
    }

    public function forXileRetro(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_xilero' => false,
            'is_xileretro' => true,
        ]);
    }

    public function forBothServers(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_xilero' => true,
            'is_xileretro' => true,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }

    public function withStock(int $stock): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $stock,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->withStock(0);
    }

    public function refined(int $level = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'refine_level' => $level,
        ]);
    }
}
