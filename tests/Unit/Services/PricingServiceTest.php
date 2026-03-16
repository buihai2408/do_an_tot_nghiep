<?php

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\Topping;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $pricingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricingService = app(PricingService::class);
    }

    public function test_calculates_base_price_without_size(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'base_price' => 29000,
        ]);

        $result = $this->pricingService->calculateItemPrice($product->id, null, []);

        $this->assertEquals(29000, $result['base_price']);
        $this->assertEquals(0, $result['toppings_total']);
        $this->assertEquals(29000, $result['unit_price']);
    }

    public function test_calculates_price_with_size(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'base_price' => 29000,
        ]);
        $size = Size::create(['name' => 'L', 'label' => 'Lớn', 'sort_order' => 3]);
        $product->sizes()->attach($size->id, ['price' => 39000]);

        $result = $this->pricingService->calculateItemPrice($product->id, $size->id, []);

        $this->assertEquals(39000, $result['base_price']);
        $this->assertEquals(39000, $result['unit_price']);
    }

    public function test_calculates_price_with_toppings(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'base_price' => 29000,
        ]);

        $topping1 = Topping::create(['name' => 'Trân châu', 'price' => 10000, 'is_active' => true]);
        $topping2 = Topping::create(['name' => 'Kem cheese', 'price' => 15000, 'is_active' => true]);

        $result = $this->pricingService->calculateItemPrice($product->id, null, [$topping1->id, $topping2->id]);

        $this->assertEquals(29000, $result['base_price']);
        $this->assertEquals(25000, $result['toppings_total']);
        $this->assertEquals(54000, $result['unit_price']);
        $this->assertCount(2, $result['toppings']);
    }
}
