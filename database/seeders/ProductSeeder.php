<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\Topping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = Size::all();
        $toppings = Topping::all();

        $products = [
            'Cà phê' => [
                ['name' => 'Cà phê sữa đá', 'base_price' => 29000, 'is_featured' => true, 'description' => 'Cà phê phin truyền thống kết hợp với sữa đặc, thêm đá mát lạnh.'],
                ['name' => 'Bạc xỉu', 'base_price' => 29000, 'is_featured' => true, 'description' => 'Cà phê phin nhẹ nhàng, sữa đặc nhiều hơn cho vị ngọt béo.'],
                ['name' => 'Cà phê đen đá', 'base_price' => 25000, 'is_featured' => false, 'description' => 'Cà phê phin nguyên chất, đậm vị, thêm đá.'],
                ['name' => 'Americano', 'base_price' => 35000, 'is_featured' => false, 'description' => 'Espresso pha nước, vị đậm mà thanh.'],
                ['name' => 'Cappuccino', 'base_price' => 45000, 'is_featured' => true, 'description' => 'Espresso với bọt sữa mịn, thơm béo.'],
                ['name' => 'Latte', 'base_price' => 45000, 'is_featured' => false, 'description' => 'Espresso với sữa tươi hấp nóng, mịn màng.'],
                ['name' => 'Caramel Macchiato', 'base_price' => 49000, 'is_featured' => true, 'description' => 'Espresso, sữa tươi, sốt caramel thơm ngọt.'],
            ],
            'Trà' => [
                ['name' => 'Trà sen vàng', 'base_price' => 35000, 'is_featured' => true, 'description' => 'Trà ướp sen thơm ngát, vị thanh dịu.'],
                ['name' => 'Trà đào cam sả', 'base_price' => 39000, 'is_featured' => true, 'description' => 'Trà đào kết hợp cam tươi và sả thơm.'],
                ['name' => 'Trà vải', 'base_price' => 39000, 'is_featured' => false, 'description' => 'Trà xanh kết hợp vải thiều ngọt mát.'],
                ['name' => 'Trà sữa truyền thống', 'base_price' => 35000, 'is_featured' => false, 'description' => 'Trà đen pha sữa đậm đà kiểu truyền thống.'],
                ['name' => 'Matcha Latte', 'base_price' => 49000, 'is_featured' => false, 'description' => 'Bột matcha Nhật Bản với sữa tươi.'],
            ],
            'Freeze & Đá xay' => [
                ['name' => 'Freeze Trà xanh', 'base_price' => 49000, 'is_featured' => true, 'description' => 'Đá xay trà xanh mát lạnh, béo ngậy.'],
                ['name' => 'Freeze Cà phê', 'base_price' => 49000, 'is_featured' => false, 'description' => 'Đá xay cà phê đậm vị, ngọt dịu.'],
                ['name' => 'Freeze Socola', 'base_price' => 49000, 'is_featured' => false, 'description' => 'Đá xay socola đậm đà, thơm ngon.'],
                ['name' => 'Freeze Cookie & Cream', 'base_price' => 55000, 'is_featured' => false, 'description' => 'Đá xay bánh quy cream thơm beo.'],
            ],
            'Sinh tố' => [
                ['name' => 'Sinh tố bơ', 'base_price' => 45000, 'is_featured' => true, 'description' => 'Sinh tố bơ béo ngậy, thơm lừng.'],
                ['name' => 'Sinh tố xoài', 'base_price' => 39000, 'is_featured' => false, 'description' => 'Sinh tố xoài chín tự nhiên, ngọt thanh.'],
                ['name' => 'Sinh tố dâu', 'base_price' => 45000, 'is_featured' => false, 'description' => 'Sinh tố dâu tây tươi mát.'],
            ],
            'Bánh ngọt' => [
                ['name' => 'Croissant bơ', 'base_price' => 35000, 'is_featured' => false, 'description' => 'Bánh croissant giòn xốp, thơm bơ.'],
                ['name' => 'Bánh tiramisu', 'base_price' => 45000, 'is_featured' => true, 'description' => 'Tiramisu béo mịn, vị cà phê đậm.'],
                ['name' => 'Bánh mousse chocolate', 'base_price' => 45000, 'is_featured' => false, 'description' => 'Mousse socola mềm mịn, ngọt vừa.'],
            ],
        ];

        foreach ($products as $categoryName => $items) {
            $category = Category::where('name', $categoryName)->first();
            if (!$category) continue;

            foreach ($items as $item) {
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'description' => $item['description'],
                    'base_price' => $item['base_price'],
                    'is_active' => true,
                    'is_featured' => $item['is_featured'],
                ]);

                if ($categoryName !== 'Bánh ngọt') {
                    foreach ($sizes as $size) {
                        $priceIncrement = match ($size->name) {
                            'S' => 0,
                            'M' => 6000,
                            'L' => 10000,
                            default => 0,
                        };
                        $product->sizes()->attach($size->id, [
                            'price' => $item['base_price'] + $priceIncrement,
                        ]);
                    }

                    $product->toppings()->attach($toppings->pluck('id'));
                }
            }
        }
    }
}
