<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function newOrders()
    {
        $orders = Order::where('status', OrderStatus::Pending)
            ->with('user:id,name')
            ->latest()
            ->limit(10)
            ->get(['id', 'order_number', 'customer_name', 'total', 'created_at', 'user_id']);

        return response()->json([
            'count' => Order::where('status', OrderStatus::Pending)->count(),
            'orders' => $orders,
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderService $orderService)
    {
        try {
            $newStatus = OrderStatus::from($request->status);
            $order = $orderService->transition($order, $newStatus, $request->cancel_reason);
            return response()->json([
                'message' => 'Đã cập nhật trạng thái đơn hàng!',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function bulkUpdateStatus(Request $request, OrderService $orderService)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'integer|exists:orders,id',
            'status' => 'required|string',
        ]);

        $newStatus = OrderStatus::from($request->status);
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($request->order_ids as $orderId) {
            try {
                $order = Order::findOrFail($orderId);
                $orderService->transition($order, $newStatus);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'order_id' => $orderId,
                    'message' => $e->getMessage(),
                ];
            }
        }

        $message = "Đã cập nhật {$results['success']} đơn hàng.";
        if ($results['failed'] > 0) {
            $message .= " {$results['failed']} đơn không thể cập nhật.";
        }

        return response()->json([
            'message' => $message,
            'results' => $results,
        ], $results['failed'] > 0 && $results['success'] === 0 ? 422 : 200);
    }
}
