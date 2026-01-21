<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'client' => fake()->randomElement([Post::CLIENT_XILERO, Post::CLIENT_RETRO]),
            'patcher_notice' => fake()->paragraph(),
            'article_content' => fake()->paragraphs(3, true),
        ];
    }
}
