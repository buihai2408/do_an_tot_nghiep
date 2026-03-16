<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Seeder;

class ToppingSeeder extends Seeder
{
    public function run(): void
    {
        $toppings = [
            ['name' => 'Trân châu đen', 'price' => 10000],
            ['name' => 'Trân châu trắng', 'price' => 10000],
            ['name' => 'Thạch cà phê', 'price' => 10000],
            ['name' => 'Kem cheese', 'price' => 15000],
            ['name' => 'Shot espresso', 'price' => 15000],
            ['name' => 'Pudding', 'price' => 12000],
        ];

        foreach ($toppings as $topping) {
            Topping::create(array_merge($topping, ['is_active' => true]));
        }
    }
}
