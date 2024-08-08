<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CartSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\OrderSeeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\AddressSeeder;
use Database\Seeders\PaymentSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\CartItemSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\FavoriteSeeder;
use Database\Seeders\OrderItemSeeder;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\OrderDetailSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            AddressSeeder::class,
            PaymentSeeder::class,
            FavoriteSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            CartSeeder::class,
            CartItemSeeder::class,
            OrderDetailSeeder::class,
            AdditionSeeder::class,
            SizeSeeder::class,
            CitySeeder::class,
            ImageSeeder::class,
            RateSeeder::class,
            ReviewSeeder::class,
            ShippingSeeder::class,
            VoucherSeeder::class,
        ]);
    }
}
