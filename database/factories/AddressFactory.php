<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        return [
            'name' => json_encode([
                'en' => $this->faker->word,
                'ar' => $this->faker->word,
            ]),
            'content' => json_encode([
                'en' => $this->faker->paragraph,
                'ar' => $this->faker->paragraph,
            ]),
            // 'details' => json_encode([
            //     'en' => $this->faker->optional()->text,
            //     'ar' => $this->faker->optional()->text,
            // ]),
            'building' => json_encode([
                'en' => $this->faker->optional()->buildingNumber,
                'ar' => $this->faker->optional()->buildingNumber,
            ]),
            'floor' => json_encode([
                'en' => $this->faker->optional()->randomDigitNotNull,
                'ar' => $this->faker->optional()->randomDigitNotNull,
            ]),
            'apartment' => json_encode([
                'en' => $this->faker->optional()->randomDigitNotNull,
                'ar' => $this->faker->optional()->randomDigitNotNull,
            ]),
            'type' => json_encode([
                'en' => $this->faker->word,
                'ar' => $this->faker->word,
            ]),
            'information' => json_encode([
                'en' => $this->faker->optional()->text,
                'ar' => $this->faker->optional()->text,
            ]),
            'city_of_residence' => json_encode([
                'en' => $this->faker->city,
                'ar' => $this->faker->city,
            ]),
            'geo_address' => json_encode([
                'en' => $this->faker->address,
                'ar' => $this->faker->address,
            ]),
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
            'active' => $this->faker->boolean ? 1 : 0,
            'user_id' => User::factory(),
            'city_id' =>City::factory(),
        ];
    }
}
