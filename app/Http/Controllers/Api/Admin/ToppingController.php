<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreToppingRequest;
use App\Models\Topping;

class ToppingController extends Controller
{
    public function store(StoreToppingRequest $request)
    {
        $topping = Topping::create($request->validated());
        return response()->json(['message' => 'Đã tạo topping!', 'topping' => $topping], 201);
    }

    public function update(StoreToppingRequest $request, Topping $topping)
    {
        $topping->update($request->validated());
        return response()->json(['message' => 'Đã cập nhật!', 'topping' => $topping]);
    }

    public function destroy(Topping $topping)
    {
        
        if ($topping->products()->count() > 0) {
            return response()->json([
                'message' => 'Không thể xóa topping đang được gắn với sản phẩm! Vui lòng gỡ topping khỏi tất cả sản phẩm trước.',
            ], 422);
        }

        
        $inCart = \DB::table('cart_item_topping')
            ->where('topping_id', $topping->id)
            ->exists();
        if ($inCart) {
            return response()->json([
                'message' => 'Không thể xóa topping đang có trong giỏ hàng của khách!',
            ], 422);
        }

        
        $inOrder = \App\Models\OrderItemTopping::where('topping_name', $topping->name)->exists();
        if ($inOrder) {
            return response()->json([
                'message' => 'Không thể xóa topping đã có trong đơn hàng!',
            ], 422);
        }

        $topping->delete();
        return response()->json(['message' => 'Đã xóa topping!']);
    }
}
