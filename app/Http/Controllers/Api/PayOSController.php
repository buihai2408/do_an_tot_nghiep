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
    public function webhook(Request $request, PayOSService $payOSService)
    {
        try {
            $webhookData = $payOSService->verifyWebhook($request->all());

            if (isset($webhookData->code) && $webhookData->code === '00') {
                $orderCode = $webhookData->data->orderCode ?? null;

                if ($orderCode) {
                    $order = Order::where('order_number', 'like', '%' . $orderCode . '%')
                        ->orWhere('id', $orderCode - 1000000)
                        ->first();

                    if ($order && $order->payment_status->value === 'pending') {
                        $order->update(['payment_status' => PaymentStatus::Paid]);
                    }
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
        $orderNumber = $request->query('orderCode');
        $status = $request->query('status');
        $code = $request->query('code');

        $order = null;
        if ($orderNumber) {
            $order = Order::where('order_number', 'like', '%' . $orderNumber . '%')
                ->orWhere('id', ((int) $orderNumber) - 1000000)
                ->first();
        }

        if ($order && ($code === '00' || $status === 'PAID')) {
            if ($order->payment_status->value === 'pending') {
                $order->update(['payment_status' => PaymentStatus::Paid]);
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
        $orderNumber = $request->query('orderCode');

        $order = null;
        if ($orderNumber) {
            $order = Order::where('order_number', 'like', '%' . $orderNumber . '%')
                ->orWhere('id', ((int) $orderNumber) - 1000000)
                ->first();
        }

        if ($order) {
            return redirect()->route('orders.show', $order)
                ->with('warning', 'Thanh toán đã bị hủy. Bạn có thể thử lại hoặc đổi phương thức thanh toán.');
        }

        return redirect()->route('home');
    }
}
