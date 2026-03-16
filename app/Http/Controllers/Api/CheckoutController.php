<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\PlaceOrderRequest;
use App\Http\Requests\Checkout\ApplyCouponRequest;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\OrderService;

class CheckoutController extends Controller
{
    public function store(PlaceOrderRequest $request, OrderService $orderService)
    {
        try {
            $order = $orderService->placeOrder($request->validated());
            return response()->json([
                'message' => 'Đặt hàng thành công!',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                'redirect' => route('orders.show', $order),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function applyCoupon(ApplyCouponRequest $request, CouponService $couponService, CartService $cartService)
    {
        $summary = $cartService->getCartSummary();
        $result = $couponService->validate($request->code, $summary['subtotal']);

        return response()->json($result, $result['valid'] ? 200 : 422);
    }
}
