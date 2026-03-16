<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('??##??')),
            'name' => $this->faker->sentence(3),
            'type' => $this->faker->randomElement(['percentage', 'fixed']),
            'value' => $this->faker->randomElement([10, 15, 20, 20000, 30000]),
            'min_order_amount' => $this->faker->randomElement([0, 50000, 100000]),
            'max_discount' => $this->faker->optional()->randomElement([20000, 30000, 50000]),
            'usage_limit' => $this->faker->optional()->numberBetween(10, 100),
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(3),
            'is_active' => true,
        ];
    }
}
