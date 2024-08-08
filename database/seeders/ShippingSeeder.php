<?php

namespace Database\Seeders;

use App\Models\Shipping;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shipping::factory(50)->create();
    }
}
