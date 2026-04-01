<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\Order;
use App\Models\Review;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function cancel(CancelOrderRequest $request, Order $order, OrderService $orderService)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        try {
            $order = $orderService->cancel($order, $request->cancel_reason);
            return response()->json(['message' => 'Đã hủy đơn hàng.', 'order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function review(StoreReviewRequest $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        if ($order->status->value !== 'completed') {
            return response()->json(['message' => 'Chỉ có thể đánh giá đơn hàng đã hoàn thành.'], 422);
        }

        if ($order->reviews()->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'Bạn đã đánh giá đơn hàng này rồi.'], 422);
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false,
        ]);

        return response()->json(['message' => 'Cảm ơn bạn đã đánh giá!', 'review' => $review], 201);
    }
}
