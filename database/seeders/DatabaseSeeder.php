<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            SizeSeeder::class,
            ToppingSeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
        ]);
    }
}
