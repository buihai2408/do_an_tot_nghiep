<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSizeRequest;
use App\Models\Size;

class SizeController extends Controller
{
    public function store(StoreSizeRequest $request)
    {
        $data = $request->validated();

        // Tự động gán thứ tự nếu không nhập
        if (empty($data['sort_order'])) {
            $data['sort_order'] = (Size::max('sort_order') ?? 0) + 1;
        }

        $size = Size::create($data);
        return response()->json(['message' => 'Đã tạo kích thước!', 'size' => $size], 201);
    }

    public function update(StoreSizeRequest $request, Size $size)
    {
        $size->update($request->validated());
        return response()->json(['message' => 'Đã cập nhật!', 'size' => $size]);
    }

    public function destroy(Size $size)
    {
        // Kiểm tra size có đang được gắn với sản phẩm nào không
        if ($size->products()->count() > 0) {
            return response()->json([
                'message' => 'Không thể xóa kích thước đang được gắn với sản phẩm! Vui lòng gỡ kích thước khỏi tất cả sản phẩm trước.',
            ], 422);
        }

        // Kiểm tra size có trong giỏ hàng không
        $inCart = \App\Models\CartItem::where('size_id', $size->id)->exists();
        if ($inCart) {
            return response()->json([
                'message' => 'Không thể xóa kích thước đang có trong giỏ hàng của khách!',
            ], 422);
        }

        $size->delete();
        return response()->json(['message' => 'Đã xóa kích thước!']);
    }
}
