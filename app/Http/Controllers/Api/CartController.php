<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Services\CartService;

class CartController extends Controller
{
    public function store(AddToCartRequest $request, CartService $cartService)
    {
        $item = $cartService->addItem($request->validated());
        return response()->json([
            'message' => 'Đã thêm vào giỏ hàng!',
            'item' => $item,
            'summary' => $cartService->getCartSummary(),
        ], 201);
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem, CartService $cartService)
    {
        $item = $cartService->updateItem($cartItem, $request->validated());
        return response()->json([
            'message' => 'Đã cập nhật!',
            'item' => $item,
            'summary' => $cartService->getCartSummary(),
        ]);
    }

    public function destroy(CartItem $cartItem, CartService $cartService)
    {
        $cartService->removeItem($cartItem);
        return response()->json([
            'message' => 'Đã xóa khỏi giỏ hàng!',
            'summary' => $cartService->getCartSummary(),
        ]);
    }

    public function clear(CartService $cartService)
    {
        $cartService->clearCart();
        return response()->json([
            'message' => 'Đã xóa toàn bộ giỏ hàng!',
            'summary' => $cartService->getCartSummary(),
        ]);
    }
}
