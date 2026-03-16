<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Support\Facades\Auth;

class CouponService
{
    public function validate(string $code, float $subtotal): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Mã giảm giá không tồn tại.'];
        }

        if (!$coupon->isValid()) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết hạn hoặc không khả dụng.'];
        }

        if ($subtotal < $coupon->min_order_amount) {
            return [
                'valid' => false,
                'message' => 'Đơn hàng tối thiểu ' . number_format($coupon->min_order_amount) . 'đ để sử dụng mã này.',
            ];
        }

        if (Auth::check()) {
            $used = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', Auth::id())
                ->exists();
            if ($used) {
                return ['valid' => false, 'message' => 'Bạn đã sử dụng mã giảm giá này rồi.'];
            }
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Áp dụng thành công! Giảm ' . number_format($discount) . 'đ',
        ];
    }

    public function apply(Coupon $coupon, int $userId, int $orderId, float $discount): void
    {
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_amount' => $discount,
        ]);

        $coupon->increment('used_count');
    }
}
