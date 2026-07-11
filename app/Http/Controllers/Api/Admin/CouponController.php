<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function store(StoreCouponRequest $request)
    {
        $coupon = Coupon::create($request->validated());
        return response()->json(['message' => 'Đã tạo mã giảm giá!', 'coupon' => $coupon], 201);
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->validated());
        return response()->json(['message' => 'Đã cập nhật mã giảm giá!', 'coupon' => $coupon]);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(['message' => 'Đã xóa mã giảm giá!']);
    }
}
