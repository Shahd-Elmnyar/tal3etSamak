<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
        ];
    }
}
