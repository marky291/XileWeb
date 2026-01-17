<?php

namespace Database\Factories;

use App\Models\Download;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Download>
 */
class DownloadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'type' => fake()->randomElement([Download::TYPE_FULL, Download::TYPE_ANDROID]),
            'link' => fake()->url(),
            'file' => null,
            'file_name' => null,
            'version' => fake()->optional()->semver(),
            'button_style' => fake()->randomElement([Download::BUTTON_STYLE_PRIMARY, Download::BUTTON_STYLE_SECONDARY]),
            'display_order' => fake()->numberBetween(0, 100),
            'enabled' => true,
        ];
    }

    public function full(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Download::TYPE_FULL,
        ]);
    }

    public function android(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => Download::TYPE_ANDROID,
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }

    public function withFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'file' => 'test-app.apk',
            'file_name' => 'test-app.apk',
            'link' => null,
        ]);
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'button_style' => Download::BUTTON_STYLE_PRIMARY,
        ]);
    }

    public function secondary(): static
    {
        return $this->state(fn (array $attributes) => [
            'button_style' => Download::BUTTON_STYLE_SECONDARY,
        ]);
    }
}
