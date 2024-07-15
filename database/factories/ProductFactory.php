<?php

namespace Database\Factories;

use App\Models\Size;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => json_encode([
                'en' => $this->faker->word,
                'ar' => $this->faker->word,
            ]),
            'description' => json_encode([
                'en' => $this->faker->paragraph,
                'ar' => $this->faker->paragraph,
            ]),
            'img' => $this->faker->imageUrl,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'offer_price' => $this->faker->randomFloat(2, 5, 50),
            'discount_type' => $this->faker->randomElement(['percent', 'value']),
            'discount' => $this->faker->optional()->numberBetween(1, 100),
            'offer' => $this->faker->boolean,
            'sale' => $this->faker->boolean,
            'active' => $this->faker->boolean,
            'category_id' => Category::factory(),
            'size_id' => Size::factory(),
        ];
    }
}
