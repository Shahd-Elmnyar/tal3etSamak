<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Addition;

class AdditionFactory extends Factory
{
    protected $model = Addition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $i = 0;
        $i++;
        return [
            'name' => json_encode([
                'en' => $this->faker->sentence,
                'ar' => $this->faker->sentence,
            ]),
            'slug' => $this->faker->unique()->slug,
            'content' => json_encode([
                'en' => $this->faker->paragraph,
                'ar' => $this->faker->paragraph,
            ]),
            'img' => $i . ".png",
            'active' => $this->faker->boolean ? 1 : 0,
            'price' => $this->faker->randomFloat(2, 10, 100),
            // 'quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
