<?php

namespace Database\Seeders;

use App\Models\Addition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Addition::factory()->count(10)->create();
    }
}
