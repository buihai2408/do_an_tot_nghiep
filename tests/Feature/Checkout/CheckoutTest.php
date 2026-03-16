<?php

namespace Tests\Feature\Checkout;

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function setupCartWithItems(): User
    {
        $user = User::factory()->create(['phone' => '0901234567']);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'base_price' => 35000]);
        $size = Size::create(['name' => 'M', 'label' => 'Vừa', 'sort_order' => 1]);
        $product->sizes()->attach($size->id, ['price' => 35000]);

        $this->actingAs($user);

        $cartService = app(CartService::class);
        $cartService->addItem([
            'product_id' => $product->id,
            'size_id' => $size->id,
            'quantity' => 2,
        ]);

        return $user;
    }

    public function test_authenticated_user_can_place_order(): void
    {
        $user = $this->setupCartWithItems();

        $response = $this->postJson('/api/checkout', [
            'order_type' => 'delivery',
            'payment_method' => 'cod',
            'customer_name' => $user->name,
            'customer_phone' => '0901234567',
            'shipping_address' => '123 Test Street',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'order' => ['id', 'order_number']]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_guest_cannot_checkout(): void
    {
        $response = $this->postJson('/api/checkout', [
            'order_type' => 'delivery',
            'payment_method' => 'cod',
            'customer_name' => 'Test',
            'customer_phone' => '0901234567',
            'shipping_address' => '123 Test Street',
        ]);

        $response->assertStatus(401);
    }

    public function test_checkout_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/checkout', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_type', 'payment_method', 'customer_name', 'customer_phone']);
    }
}
