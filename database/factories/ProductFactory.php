<?php

namespace Database\Factories;

use App\Models\Size;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {

        return [
            'name' => [
                'en' => $this->faker->name,
                'ar' => $this->faker->name,
            ],
            'description' => ([
                'en' => $this->faker->paragraph,
                'ar' => $this->faker->paragraph,
            ]),

            'price' => $this->faker->randomFloat(2, 10, 100),
            'offer_price' => $this->faker->randomFloat(2, 5, 50),
            'discount_type' => $this->faker->randomElement(['percent', 'value']),
            'discount' => $this->faker->optional()->numberBetween(1, 100),
            'is_offer' => $this->faker->boolean,
            'is_sale' => $this->faker->boolean,
            'active' => $this->faker->boolean,
            // 'additionn_id' => Category::factory(),
            // 'size_id' => Size::factory(),
            // 'image_id' => Image::factory(),
        ];
    }
}
