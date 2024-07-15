<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'total' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement(['pending', 'progress', 'canceled', 'delivered']),
            'type' => $this->faker->randomElement(['delivery', 'restaurant']),
            'user_id' =>User::factory(),
            'payment_id'=> Payment::inRandomOrder()->first()->id,
        ];
    }
}
