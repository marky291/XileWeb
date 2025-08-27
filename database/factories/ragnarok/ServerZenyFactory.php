<?php

declare(strict_types=1);

namespace Database\Factories\Ragnarok;

use App\Ragnarok\ServerZeny;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Ragnarok\ServerZeny>
 */
final class ServerZenyFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = ServerZeny::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
        ];
    }
}
