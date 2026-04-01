<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Cà phê', 'sort_order' => 1],
            ['name' => 'Trà', 'sort_order' => 2],
            ['name' => 'Freeze & Đá xay', 'sort_order' => 3],
            ['name' => 'Sinh tố', 'sort_order' => 4],
            ['name' => 'Bánh ngọt', 'sort_order' => 5],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'sort_order' => $cat['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
