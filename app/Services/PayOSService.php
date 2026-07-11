<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use PayOS\PayOS;
use PayOS\Models\V2\PaymentRequests\CreatePaymentLinkRequest;

class PayOSService
{
    protected PayOS $payOS;

    public function __construct()
    {
        $this->payOS = new PayOS(
            clientId: config('services.payos.client_id'),
            apiKey: config('services.payos.api_key'),
            checksumKey: config('services.payos.checksum_key'),
        );
    }

    public function createPaymentLink(Order $order, string $returnUrl, string $cancelUrl): string
    {
        $orderCode = $this->generateOrderCode($order);
        $description = mb_substr("DH {$order->order_number}", 0, 25);

        Log::info('PayOS: Creating payment link', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'orderCode' => $orderCode,
            'amount' => (int) $order->total,
        ]);

        try {
            $paymentData = new CreatePaymentLinkRequest(
                orderCode: $orderCode,
                amount: (int) $order->total,
                description: $description,
                returnUrl: $returnUrl,
                cancelUrl: $cancelUrl,
            );

            $result = $this->payOS->paymentRequests->create($paymentData);

            return $result->checkoutUrl;
        } catch (\PayOS\Exceptions\APIException $e) {
            Log::warning('PayOS: API error when creating payment link', [
                'order_id' => $order->id,
                'orderCode' => $orderCode,
                'error' => $e->getMessage(),
            ]);

            if (str_contains($e->getMessage(), '231') || str_contains($e->getMessage(), 'đã tồn tại')) {
                try {
                    $existingPayment = $this->payOS->paymentRequests->get(strval($orderCode));

                    if (isset($existingPayment->status) && $existingPayment->status === 'PENDING' && isset($existingPayment->checkoutUrl)) {
                        Log::info('PayOS: Reusing existing pending payment link', [
                            'order_id' => $order->id,
                            'orderCode' => $orderCode,
                        ]);
                        return $existingPayment->checkoutUrl;
                    }
                } catch (\Exception $getEx) {
                    Log::warning('PayOS: Could not get existing payment, retrying with new code', [
                        'error' => $getEx->getMessage(),
                    ]);
                }

                $retryCode = $this->generateUniqueOrderCode($order);
                Log::info('PayOS: Retrying with new orderCode', [
                    'order_id' => $order->id,
                    'newOrderCode' => $retryCode,
                ]);

                $paymentData = new CreatePaymentLinkRequest(
                    orderCode: $retryCode,
                    amount: (int) $order->total,
                    description: $description,
                    returnUrl: $returnUrl,
                    cancelUrl: $cancelUrl,
                );

                $result = $this->payOS->paymentRequests->create($paymentData);

                return $result->checkoutUrl;
            }

            throw $e;
        }
    }

    private function generateOrderCode(Order $order): int
    {
        return $order->id + 1000000;
    }

    private function generateUniqueOrderCode(Order $order): int
    {
        $timestamp = (int) (microtime(true) * 10) % 10000000;
        return (int) ($order->id . $timestamp);
    }

    public function verifyWebhook(array $payload): object
    {
        return $this->payOS->webhooks->verify($payload);
    }

    public function getPaymentInfo(int $orderCode): object
    {
        return $this->payOS->paymentRequests->get(strval($orderCode));
    }
}

