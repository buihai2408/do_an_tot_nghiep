<?php

namespace Tests\Feature\Order;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    private function createOrder(OrderStatus $status = OrderStatus::Pending): Order
    {
        $user = User::factory()->create();

        return Order::create([
            'user_id' => $user->id,
            'order_number' => Order::generateOrderNumber(),
            'status' => $status,
            'order_type' => 'delivery',
            'subtotal' => 70000,
            'discount_amount' => 0,
            'shipping_fee' => 25000,
            'total' => 95000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'customer_name' => 'Test User',
            'customer_phone' => '0901234567',
        ]);
    }

    public function test_admin_can_confirm_order(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $order = $this->createOrder();

        $this->actingAs($admin);

        $response = $this->putJson("/api/admin/orders/{$order->id}/status", [
            'status' => 'confirmed',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_invalid_transition_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $order = $this->createOrder(OrderStatus::Completed);

        $this->actingAs($admin);

        $response = $this->putJson("/api/admin/orders/{$order->id}/status", [
            'status' => 'preparing',
        ]);

        $response->assertStatus(409);
    }

    public function test_customer_can_cancel_pending_order(): void
    {
        $order = $this->createOrder();
        $user = User::find($order->user_id);

        $this->actingAs($user);

        $response = $this->postJson("/api/orders/{$order->id}/cancel", [
            'cancel_reason' => 'Đổi ý',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_non_admin_cannot_update_status(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);
        $order = $this->createOrder();

        $this->actingAs($customer);

        $response = $this->putJson("/api/admin/orders/{$order->id}/status", [
            'status' => 'confirmed',
        ]);

        $response->assertStatus(403);
    }
}
