<?php

namespace Database\Factories;

use App\Models\UberShopCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UberShopCategory>
 */
class UberShopCategoryFactory extends Factory
{
    protected $model = UberShopCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(),
            'display_name' => fake()->words(2, true),
            'tagline' => fake()->sentence(),
            'uber_range' => fake()->randomElement(['1-10', '10-50', '50-100']),
            'display_order' => fake()->numberBetween(0, 100),
            'enabled' => true,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }
}
