<?php

namespace Tests\Feature\Coupon;

use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    private function setupCartAndLogin(): User
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'base_price' => 60000]);
        $size = Size::create(['name' => 'M', 'label' => 'Vừa', 'sort_order' => 1]);
        $product->sizes()->attach($size->id, ['price' => 60000]);

        $this->actingAs($user);

        $cartService = app(CartService::class);
        $cartService->addItem([
            'product_id' => $product->id,
            'size_id' => $size->id,
            'quantity' => 2,
        ]);

        return $user;
    }

    public function test_valid_coupon_can_be_applied(): void
    {
        $this->setupCartAndLogin();

        Coupon::create([
            'code' => 'TEST10',
            'name' => 'Test Coupon',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 50000,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->postJson('/api/checkout/apply-coupon', ['code' => 'TEST10']);

        $response->assertStatus(200)
            ->assertJson(['valid' => true]);
    }

    public function test_invalid_coupon_is_rejected(): void
    {
        $this->setupCartAndLogin();

        $response = $this->postJson('/api/checkout/apply-coupon', ['code' => 'INVALID']);

        $response->assertStatus(422)
            ->assertJson(['valid' => false]);
    }

    public function test_expired_coupon_is_rejected(): void
    {
        $this->setupCartAndLogin();

        Coupon::create([
            'code' => 'EXPIRED',
            'name' => 'Expired',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'starts_at' => now()->subWeek(),
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->postJson('/api/checkout/apply-coupon', ['code' => 'EXPIRED']);

        $response->assertStatus(422)
            ->assertJson(['valid' => false]);
    }
}
