<?php

namespace Database\Factories;

use App\Models\Size;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        return [
            'quantity' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'total' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['price'];
            },
            'state' => $this->faker->boolean,
            'product_id' =>Product::factory(),
            'order_id' =>Order::factory(),
            'size_id' =>Size::factory(),
        ];
    }
}
