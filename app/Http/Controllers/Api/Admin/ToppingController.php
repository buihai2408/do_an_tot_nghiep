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
        $topping->delete();
        return response()->json(['message' => 'Đã xóa!']);
    }
}
