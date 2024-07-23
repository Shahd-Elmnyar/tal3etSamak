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
            'content' =>$this->faker->paragraph,

            // 'details' => json_encode([
            //     'en' => $this->faker->optional()->text,
            //     'ar' => $this->faker->optional()->text,
            // ]),
            'building' =>$this->faker->optional()->buildingNumber,

            'floor' => $this->faker->optional()->randomDigitNotNull,

            'apartment' =>$this->faker->optional()->randomDigitNotNull,

            'type' =>$this->faker->word,

            'information' => $this->faker->optional()->text,

            'city_of_residence' => $this->faker->city,

            'geo_address' => $this->faker->address,
            
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
            'active' => $this->faker->boolean ? 1 : 0,
            'user_id' => User::factory(),
            'city_id' =>City::factory(),
        ];
    }
}
