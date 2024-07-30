<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Size;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition()
    {
        return [
            'quantity' => $this->faker->numberBetween(1, 10),
            'addition_quantity' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'total' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['price'] + $attributes['total_addition_price'];
            },
            'total_addition_price' => $this->faker->randomFloat(2, 1, 100),
            'active' => $this->faker->boolean,
            'cart_id' =>Cart::factory(),
            'product_id' =>Product::factory(),
            'size_id' =>Size::factory(),
        ];
    }
}
