<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::create([
            'code' => 'WELCOME10',
            'name' => 'Giảm 10% cho khách mới',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 50000,
            'max_discount' => 30000,
            'usage_limit' => 100,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'GIAM20K',
            'name' => 'Giảm 20.000đ',
            'type' => 'fixed',
            'value' => 20000,
            'min_order_amount' => 100000,
            'max_discount' => null,
            'usage_limit' => 50,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(2),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'FREESHIP',
            'name' => 'Miễn phí giao hàng',
            'type' => 'fixed',
            'value' => 25000,
            'min_order_amount' => 80000,
            'max_discount' => 25000,
            'usage_limit' => null,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
            'is_active' => true,
        ]);
    }
}
