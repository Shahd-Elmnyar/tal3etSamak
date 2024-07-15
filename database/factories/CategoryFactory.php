<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => json_encode([
                'en' => $this->faker->word,
                'ar' => $this->faker->word,
            ]),
            'slug' => $this->faker->slug,
            'content' => json_encode([
                'en' => $this->faker->paragraph,
                'ar' => $this->faker->paragraph,
            ]),
            'img' => $this->faker->imageUrl,
            'status' => $this->faker->boolean,
            'parent_id' => null, // Default to no parent
        ];
    }

    /**
     * Indicate that the category should have a parent.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withParent(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => Category::factory(),
            ];
        });
    }
}
