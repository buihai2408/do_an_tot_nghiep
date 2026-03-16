<?php

namespace Tests\Feature\Cart;

use App\Models\Product;
use App\Models\Category;
use App\Models\Size;
use App\Models\Topping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(): Product
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $size = Size::create(['name' => 'M', 'label' => 'Vừa', 'sort_order' => 1]);
        $product->sizes()->attach($size->id, ['price' => 35000]);
        return $product;
    }

    public function test_can_add_item_to_cart(): void
    {
        $product = $this->createProduct();
        $size = $product->sizes->first();

        $response = $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'size_id' => $size->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'item', 'summary']);
    }

    public function test_can_update_cart_item_quantity(): void
    {
        $product = $this->createProduct();
        $size = $product->sizes->first();

        $addResponse = $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'size_id' => $size->id,
            'quantity' => 1,
        ]);

        $itemId = $addResponse->json('item.id');

        $response = $this->putJson("/api/cart/items/{$itemId}", [
            'quantity' => 3,
        ]);

        $response->assertStatus(200);
    }

    public function test_can_remove_cart_item(): void
    {
        $product = $this->createProduct();
        $size = $product->sizes->first();

        $addResponse = $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'size_id' => $size->id,
            'quantity' => 1,
        ]);

        $itemId = $addResponse->json('item.id');

        $response = $this->deleteJson("/api/cart/items/{$itemId}");
        $response->assertStatus(200);
    }

    public function test_validates_product_exists(): void
    {
        $response = $this->postJson('/api/cart/items', [
            'product_id' => 99999,
            'quantity' => 1,
        ]);

        $response->assertStatus(422);
    }
}
