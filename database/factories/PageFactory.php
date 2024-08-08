<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3), // Generates a random sentence with 3 words
            'content' => $this->faker->paragraph(), // Generates a random paragraph
            'page_type' => $this->faker->randomElement(['help', 'type2', 'type3']), // Randomly selects a page type
            'order_id' => $this->faker->randomDigitNotNull(), // Generates a random digit
            'parent_id' => null, // You can set this to null or generate an ID depending on your requirements
        ];
    }
}
