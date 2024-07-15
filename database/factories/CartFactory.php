<?php

namespace Database\Factories;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'total_price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
