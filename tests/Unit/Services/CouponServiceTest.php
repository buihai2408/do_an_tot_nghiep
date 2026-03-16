<?php

namespace Tests\Unit\Services;

use App\Models\Coupon;
use App\Models\User;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponServiceTest extends TestCase
{
    use RefreshDatabase;

    private CouponService $couponService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->couponService = app(CouponService::class);
    }

    public function test_valid_percentage_coupon(): void
    {
        Coupon::create([
            'code' => 'PERCENT10',
            'name' => 'Test',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 50000,
            'max_discount' => 30000,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $result = $this->couponService->validate('PERCENT10', 100000);

        $this->assertTrue($result['valid']);
        $this->assertEquals(10000, $result['discount']);
    }

    public function test_valid_fixed_coupon(): void
    {
        Coupon::create([
            'code' => 'FIXED20K',
            'name' => 'Test',
            'type' => 'fixed',
            'value' => 20000,
            'min_order_amount' => 0,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $result = $this->couponService->validate('FIXED20K', 80000);

        $this->assertTrue($result['valid']);
        $this->assertEquals(20000, $result['discount']);
    }

    public function test_coupon_below_min_order(): void
    {
        Coupon::create([
            'code' => 'MIN100K',
            'name' => 'Test',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 100000,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $result = $this->couponService->validate('MIN100K', 50000);

        $this->assertFalse($result['valid']);
    }

    public function test_expired_coupon(): void
    {
        Coupon::create([
            'code' => 'EXPIRED',
            'name' => 'Test',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'starts_at' => now()->subWeek(),
            'expires_at' => now()->subDay(),
        ]);

        $result = $this->couponService->validate('EXPIRED', 100000);

        $this->assertFalse($result['valid']);
    }

    public function test_inactive_coupon(): void
    {
        Coupon::create([
            'code' => 'INACTIVE',
            'name' => 'Test',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => false,
        ]);

        $result = $this->couponService->validate('INACTIVE', 100000);

        $this->assertFalse($result['valid']);
    }

    public function test_nonexistent_coupon(): void
    {
        $result = $this->couponService->validate('DOESNOTEXIST', 100000);

        $this->assertFalse($result['valid']);
    }

    public function test_max_discount_is_respected(): void
    {
        Coupon::create([
            'code' => 'MAXED',
            'name' => 'Test',
            'type' => 'percentage',
            'value' => 50,
            'min_order_amount' => 0,
            'max_discount' => 20000,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $result = $this->couponService->validate('MAXED', 100000);

        $this->assertTrue($result['valid']);
        $this->assertEquals(20000, $result['discount']);
    }
}
