<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        Size::create(['name' => 'S', 'label' => 'Nhỏ', 'sort_order' => 1]);
        Size::create(['name' => 'M', 'label' => 'Vừa', 'sort_order' => 2]);
        Size::create(['name' => 'L', 'label' => 'Lớn', 'sort_order' => 3]);
    }
}
