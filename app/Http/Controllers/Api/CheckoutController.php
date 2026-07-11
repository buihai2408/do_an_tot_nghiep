<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\PlaceOrderRequest;
use App\Http\Requests\Checkout\ApplyCouponRequest;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\OrderService;
use App\Services\PayOSService;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function store(PlaceOrderRequest $request, OrderService $orderService, PayOSService $payOSService)
    {
        try {
            $order = $orderService->placeOrder($request->validated());

            $redirect = route('orders.show', $order);

            if ($order->payment_method->value === 'payos') {
                try {
                    $returnUrl = route('checkout.payos.return');
                    $cancelUrl = route('checkout.payos.cancel');
                    $redirect = $payOSService->createPaymentLink($order, $returnUrl, $cancelUrl);
                } catch (\Exception $e) {
                    Log::error('PayOS: Failed to create payment link after order', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'error' => $e->getMessage(),
                    ]);

                    return response()->json([
                        'message' => 'Đặt hàng thành công nhưng không tạo được link thanh toán PayOS. Vui lòng vào chi tiết đơn hàng để thử lại.',
                        'order' => ['id' => $order->id, 'order_number' => $order->order_number],
                        'redirect' => route('orders.show', $order),
                    ], 201);
                }
            }

            return response()->json([
                'message' => 'Đặt hàng thành công!',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                'redirect' => $redirect,
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
