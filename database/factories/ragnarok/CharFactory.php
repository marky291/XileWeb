<?php

declare(strict_types=1);

namespace Database\Factories\Ragnarok;

use App\Ragnarok\Char;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Ragnarok\Char>
 */
final class CharFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Char::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'account_id' => fake()->randomNumber(),
            'char_num' => fake()->boolean,
            'name' => fake()->name,
            'class' => fake()->randomNumber(),
            'base_level' => fake()->randomNumber(),
            'job_level' => fake()->randomNumber(),
            'base_exp' => fake()->randomNumber(),
            'job_exp' => fake()->randomNumber(),
            'zeny' => fake()->randomNumber(),
            'str' => fake()->randomNumber(),
            'agi' => fake()->randomNumber(),
            'vit' => fake()->randomNumber(),
            'int' => fake()->randomNumber(),
            'dex' => fake()->randomNumber(),
            'luk' => fake()->randomNumber(),
            'max_hp' => fake()->randomNumber(),
            'hp' => fake()->randomNumber(),
            'max_sp' => fake()->randomNumber(),
            'sp' => fake()->randomNumber(),
            'status_point' => fake()->randomNumber(),
            'skill_point' => fake()->randomNumber(),
            'option' => fake()->randomNumber(),
            'karma' => fake()->boolean,
            'manner' => fake()->randomNumber(),
            'party_id' => fake()->randomNumber(),
            'guild_id' => fake()->randomNumber(),
            'pet_id' => fake()->randomNumber(),
            'homun_id' => fake()->randomNumber(),
            'elemental_id' => fake()->randomNumber(),
            'hair' => fake()->boolean,
            'hair_color' => fake()->randomNumber(),
            'clothes_color' => fake()->randomNumber(),
            'body' => fake()->randomNumber(),
            'weapon' => fake()->randomNumber(),
            'shield' => fake()->randomNumber(),
            'head_top' => fake()->randomNumber(),
            'head_mid' => fake()->randomNumber(),
            'head_bottom' => fake()->randomNumber(),
            'robe' => fake()->randomNumber(),
            'last_map' => fake()->word,
            'last_x' => fake()->randomNumber(),
            'last_y' => fake()->randomNumber(),
            'save_map' => fake()->word,
            'save_x' => fake()->randomNumber(),
            'save_y' => fake()->randomNumber(),
            'partner_id' => fake()->randomNumber(),
            'online' => fake()->boolean,
            'father' => fake()->randomNumber(),
            'mother' => fake()->randomNumber(),
            'child' => fake()->randomNumber(),
            'fame' => fake()->randomNumber(),
            'rename' => fake()->randomNumber(),
            'delete_date' => fake()->randomNumber(),
            'moves' => fake()->randomNumber(),
            'unban_time' => fake()->randomNumber(),
            'font' => fake()->boolean,
            'uniqueitem_counter' => fake()->randomNumber(),
            'sex' => fake()->randomElement(['M', 'F']),
            'hotkey_rowshift' => fake()->boolean,
            'hotkey_rowshift2' => fake()->boolean,
            'clan_id' => fake()->randomNumber(),
            'last_login' => fake()->optional()->dateTime(),
            'title_id' => fake()->randomNumber(),
            'show_equip' => fake()->boolean,
        ];
    }
}
