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
            ['name' => 'Cà phê', 'description' => 'Các loại cà phê truyền thống và hiện đại', 'sort_order' => 1],
            ['name' => 'Trà', 'description' => 'Các loại trà thơm ngon', 'sort_order' => 2],
            ['name' => 'Freeze & Đá xay', 'description' => 'Thức uống đá xay mát lạnh', 'sort_order' => 3],
            ['name' => 'Sinh tố', 'description' => 'Sinh tố trái cây tươi ngon', 'sort_order' => 4],
            ['name' => 'Bánh ngọt', 'description' => 'Bánh ngọt và snack đi kèm', 'sort_order' => 5],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'sort_order' => $cat['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
