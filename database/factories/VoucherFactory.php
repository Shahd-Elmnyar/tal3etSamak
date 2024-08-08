<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $endDate = $this->faker->dateTimeBetween('now', '+1 year');
        return [
            'code' => $this->faker->unique()->word,
            'discount_type' => $this->faker->randomElement(['percent', 'value']),
            'discount' => $this->faker->numberBetween(5, 50),
            'max_discount' => $this->faker->numberBetween(50, 200),
            'min_order' => $this->faker->numberBetween(10, 100),
            'times_used' => $this->faker->numberBetween(0, 100),
            'user_limit' => $this->faker->numberBetween(1, 10),
            'user_number' => $this->faker->numberBetween(1, 100),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'active' => $this->faker->boolean,
        ];
    }
}
