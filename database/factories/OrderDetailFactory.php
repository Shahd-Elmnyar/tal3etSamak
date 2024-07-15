<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\Payment;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    protected $model = OrderDetail::class;

    public function definition()
    {
        return [
            'name' => json_encode([
                'en' => $this->faker->name,
                'ar' => $this->faker->name,
            ]),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'address' => json_encode([
                'en' => $this->faker->address,
                'ar' => $this->faker->address,
            ]),
            'address_type' => json_encode([
                'en' => $this->faker->randomElement(['home', 'work']),
                'ar' => $this->faker->randomElement(['home', 'work']),
            ]),
            'order_type' => $this->faker->randomElement(['delivery', 'restaurant']), // Use a string directly
            'payment_id' => Payment::inRandomOrder()->first()->id,
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
        ];
    }
}
