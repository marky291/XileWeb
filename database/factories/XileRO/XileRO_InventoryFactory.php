<?php

declare(strict_types=1);

namespace Database\Factories\XileRO;

use App\XileRO\XileRO_Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<XileRO_Inventory>
 */
final class XileRO_InventoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = XileRO_Inventory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'char_id' => fake()->randomNumber(),
            'nameid' => fake()->randomNumber(),
            'amount' => 1,
            'equip' => 0,
            'identify' => 1,
            'refine' => 0,
            'attribute' => 0,
            'card0' => 0,
            'card1' => 0,
            'card2' => 0,
            'card3' => 0,
            'option_id0' => 0,
            'option_val0' => 0,
            'option_parm0' => 0,
            'option_id1' => 0,
            'option_val1' => 0,
            'option_parm1' => 0,
            'option_id2' => 0,
            'option_val2' => 0,
            'option_parm2' => 0,
            'option_id3' => 0,
            'option_val3' => 0,
            'option_parm3' => 0,
            'option_id4' => 0,
            'option_val4' => 0,
            'option_parm4' => 0,
            'expire_time' => 0,
            'favorite' => 0,
            'bound' => 0,
            'unique_id' => 0,
            'equip_switch' => 0,
            'enchantgrade' => 0,
        ];
    }

    /**
     * State for an equipped item.
     */
    public function equipped(int $slot = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'equip' => $slot,
        ]);
    }

    /**
     * State for a refined item.
     */
    public function refined(int $level = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'refine' => $level,
        ]);
    }

    /**
     * State for a stacked item.
     */
    public function stacked(int $amount = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    /**
     * State for an item with cards.
     */
    public function withCards(int $card0 = 0, int $card1 = 0, int $card2 = 0, int $card3 = 0): static
    {
        return $this->state(fn (array $attributes) => [
            'card0' => $card0,
            'card1' => $card1,
            'card2' => $card2,
            'card3' => $card3,
        ]);
    }
}
