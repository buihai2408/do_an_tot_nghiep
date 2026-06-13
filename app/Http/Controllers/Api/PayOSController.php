<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\PayOSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PayOSController extends Controller
{
    /**
     * Tìm order từ orderCode PayOS trả về.
     * orderCode = order->id + 1000000 (xem PayOSService::createPaymentLink)
     */
    private function findOrderByPayOSCode($orderCode): ?Order
    {
        if (!$orderCode || !is_numeric($orderCode)) {
            return null;
        }

        $orderId = (int) $orderCode - 1000000;

        if ($orderId <= 0) {
            return null;
        }

        return Order::find($orderId);
    }

    public function webhook(Request $request, PayOSService $payOSService)
    {
        try {
            $webhookData = $payOSService->verifyWebhook($request->all());

            if (isset($webhookData->code) && $webhookData->code === '00') {
                $orderCode = $webhookData->data->orderCode ?? null;

                Log::info('PayOS Webhook received', [
                    'orderCode' => $orderCode,
                    'code' => $webhookData->code,
                ]);

                $order = $this->findOrderByPayOSCode($orderCode);

                if ($order && $order->payment_status->value === 'pending') {
                    $order->update(['payment_status' => PaymentStatus::Paid]);
                    Log::info('PayOS Webhook: Updated payment status to Paid', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                    ]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('PayOS Webhook error: ' . $e->getMessage());
            return response()->json(['success' => false], 400);
        }
    }

    public function return(Request $request)
    {
        $orderCode = $request->query('orderCode');
        $status = $request->query('status');
        $code = $request->query('code');

        Log::info('PayOS Return callback', [
            'orderCode' => $orderCode,
            'status' => $status,
            'code' => $code,
        ]);

        $order = $this->findOrderByPayOSCode($orderCode);

        if ($order && ($code === '00' || $status === 'PAID')) {
            if ($order->payment_status->value === 'pending') {
                $order->update(['payment_status' => PaymentStatus::Paid]);
                Log::info('PayOS Return: Updated payment status to Paid', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
            }

            return Inertia::render('Checkout/Success', [
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);
        }

        if ($order) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Trạng thái thanh toán đang được xác nhận.');
        }

        return redirect()->route('home');
    }

    public function cancel(Request $request)
    {
        $orderCode = $request->query('orderCode');

        $order = $this->findOrderByPayOSCode($orderCode);

        if ($order) {
            return redirect()->route('orders.show', $order)
                ->with('warning', 'Thanh toán đã bị hủy. Bạn có thể thử lại hoặc đổi phương thức thanh toán.');
        }

        return redirect()->route('home');
    }
}
